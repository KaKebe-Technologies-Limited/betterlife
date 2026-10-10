<?php
/**
 * Gifts made on donate.php, paid through Pesapal (mobile money or card) like farm-shop orders.
 * A gift is stored as pending, the giver pays on Pesapal's page, and the status is confirmed with
 * Pesapal itself (on return to donate-thanks.php and by the IPN in order-ipn.php), never from the
 * redirect alone. The receipt and the alert go out once, when Pesapal reports the gift as completed.
 *
 * The donations table is created here on first use, so the live site needs no separate migration.
 * The live content update (Admin > Site Update) never touches it.
 */
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/pesapal.php';
require_once __DIR__ . '/mailer.php';

const DONATION_MIN = 5000;
const DONATION_MAX = 50000000;

function donations_ensure_table(PDO $pdo): void
{
    static $done = false;
    if ($done) return;
    $pdo->exec("CREATE TABLE IF NOT EXISTS donations (
        id int(11) NOT NULL AUTO_INCREMENT,
        donation_ref varchar(40) NOT NULL,
        donor_name varchar(150) NOT NULL,
        donor_email varchar(150) NOT NULL,
        donor_phone varchar(50) DEFAULT NULL,
        amount decimal(12,2) NOT NULL,
        currency varchar(10) NOT NULL DEFAULT 'UGX',
        message text DEFAULT NULL,
        status enum('pending','paid','failed','cancelled') NOT NULL DEFAULT 'pending',
        pesapal_tracking_id varchar(100) DEFAULT NULL,
        paid_at datetime DEFAULT NULL,
        receipt_sent tinyint(1) NOT NULL DEFAULT 0,
        created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY donation_ref (donation_ref),
        KEY status (status)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    $done = true;
}

/** Suggested amounts in Uganda shillings. */
function donation_presets(): array
{
    return [20000, 50000, 100000, 250000];
}

/** Online giving needs Pesapal keys (Admin > Site Settings > Payments & Email). */
function donations_can_pay(PDO $pdo): bool
{
    return setting($pdo, 'pesapal_consumer_key') !== '' && setting($pdo, 'pesapal_consumer_secret') !== '';
}

function donation_ref(): string
{
    return 'DN' . date('ymd') . strtoupper(bin2hex(random_bytes(3)));
}

function donation_find(PDO $pdo, string $ref = '', string $trackingId = ''): ?array
{
    donations_ensure_table($pdo);
    $stmt = $ref !== ''
        ? $pdo->prepare('SELECT * FROM donations WHERE donation_ref = ? LIMIT 1')
        : $pdo->prepare('SELECT * FROM donations WHERE pesapal_tracking_id = ? LIMIT 1');
    $stmt->execute([$ref !== '' ? $ref : $trackingId]);
    return $stmt->fetch() ?: null;
}

/** Sends the giver to Pesapal: returns the payment page address, or throws. */
function donation_initiate_payment(PDO $pdo, array $d): string
{
    $client = pesapal_client($pdo);
    $ipnId = pesapal_ipn_id($pdo);
    [$first, $last] = array_pad(explode(' ', trim($d['donor_name']), 2), 2, '');

    $data = [
        'id'              => $d['donation_ref'],
        'currency'        => $d['currency'],
        'amount'          => (float) $d['amount'],
        'description'     => 'Gift to BetterLife International ' . $d['donation_ref'],
        'callback_url'    => public_base_url() . SITE_URL . '/donate-thanks.php?ref=' . urlencode($d['donation_ref']),
        'billing_address' => [
            'email_address' => $d['donor_email'],
            'phone_number'  => preg_replace('/[^0-9]/', '', (string) $d['donor_phone']),
            'country_code'  => 'UG',
            'first_name'    => mb_substr($first, 0, 50),
            'last_name'     => mb_substr($last, 0, 50),
        ],
    ];
    if ($ipnId) $data['notification_id'] = $ipnId;

    $res = $client->submitOrder($data);
    if (empty($res->redirect_url)) {
        throw new Exception('Pesapal did not return a payment link: ' . ($res->error->message ?? $res->message ?? json_encode($res)));
    }
    $pdo->prepare('UPDATE donations SET pesapal_tracking_id = ? WHERE id = ?')->execute([$res->order_tracking_id ?? null, $d['id']]);
    return $res->redirect_url;
}

/** Asks Pesapal how a pending gift stands and records it. Returns the status. */
function donation_sync_status(PDO $pdo, array $d): string
{
    if ($d['status'] !== 'pending' || empty($d['pesapal_tracking_id'])) return $d['status'];
    try {
        $res = pesapal_client($pdo)->getTransactionStatus($d['pesapal_tracking_id']);
        $desc = strtoupper($res->payment_status_description ?? $res->status ?? '');
    } catch (Exception $e) {
        error_log('Pesapal status check failed for ' . $d['donation_ref'] . ': ' . $e->getMessage());
        return $d['status'];
    }
    if ($desc === 'COMPLETED') {
        $pdo->prepare("UPDATE donations SET status = 'paid', paid_at = NOW() WHERE id = ? AND status = 'pending'")->execute([$d['id']]);
        donation_after_paid($pdo, $d['id']);
        return 'paid';
    }
    if (in_array($desc, ['FAILED', 'INVALID', 'REVERSED'], true)) {
        $pdo->prepare("UPDATE donations SET status = 'failed' WHERE id = ? AND status = 'pending'")->execute([$d['id']]);
        return 'failed';
    }
    return 'pending';
}

/** Thank-you receipt to the giver and an alert to BetterLife, once per gift. */
function donation_after_paid(PDO $pdo, int $id): void
{
    // Claim the gift first, so the return page and the IPN arriving together cannot both send
    $claim = $pdo->prepare("UPDATE donations SET receipt_sent = 1 WHERE id = ? AND status = 'paid' AND receipt_sent = 0");
    $claim->execute([$id]);
    if (!$claim->rowCount()) return;
    $stmt = $pdo->prepare('SELECT * FROM donations WHERE id = ?');
    $stmt->execute([$id]);
    $d = $stmt->fetch();

    $legal = setting($pdo, 'legal_name') ?: setting($pdo, 'site_name');
    $regNo = setting($pdo, 'ngo_reg_no');
    $amount = h(format_price((float) $d['amount']));
    $when = h(date('j F Y', strtotime($d['paid_at'] ?: 'now')));
    $rows = '<table cellpadding="8" style="border-collapse:collapse;font-size:15px;margin:14px 0;">'
          . '<tr><td style="color:#5b6b63;">Amount</td><td><strong>' . $amount . '</strong></td></tr>'
          . '<tr><td style="color:#5b6b63;">Reference</td><td>' . h($d['donation_ref']) . '</td></tr>'
          . '<tr><td style="color:#5b6b63;">Date</td><td>' . $when . '</td></tr></table>';

    try {
        send_email($pdo, $d['donor_email'], $d['donor_name'], 'Thank you for your gift to BetterLife International', email_wrap($pdo, 'Thank you, ' . explode(' ', trim($d['donor_name']))[0],
            '<p>We have received your gift to ' . h($legal) . '. Thank you for supporting our work with women, young people, refugees, displaced families and farming communities.</p>'
            . $rows . '<p>Please keep this email as the record of your gift.</p>'
            . '<p style="color:#5b6b63;font-size:13px;">' . h($legal) . ($regNo ? ', a registered non-governmental organisation in Uganda, Reg. No. ' . h($regNo) : '') . '.</p>'));
    } catch (Throwable $e) {
        error_log($e->getMessage());
    }
    $to = setting($pdo, 'admin_alert_email') ?: setting($pdo, 'email');
    if ($to) {
        try {
            send_email($pdo, $to, 'BetterLife', 'New gift: ' . format_price((float) $d['amount']) . ' from ' . $d['donor_name'], email_wrap($pdo, 'A new gift has arrived',
                '<p><strong>' . h($d['donor_name']) . '</strong> (' . h($d['donor_email']) . ($d['donor_phone'] ? ', ' . h($d['donor_phone']) : '') . ') gave ' . $amount . '.</p>' . $rows
                . ($d['message'] ? '<p><strong>Their message:</strong><br>' . nl2br(h($d['message'])) . '</p>' : '')
                . '<p>All gifts are listed under Admin &gt; Donations.</p>'), [], [$d['donor_email'], $d['donor_name']]);
        } catch (Throwable $e) {
            error_log($e->getMessage());
        }
    }
}

/**
 * Pesapal's IPN for a gift (called from order-ipn.php, which serves farm orders too).
 * Answers and returns true when the notification is about a gift; false leaves it to the orders code.
 */
function donation_handle_ipn(PDO $pdo, string $trackingId, string $merchantRef): bool
{
    if ($merchantRef !== '' && !str_starts_with($merchantRef, 'DN')) return false;
    try {
        $d = donation_find($pdo, $merchantRef, $trackingId);
    } catch (Throwable $e) {
        error_log('Donation IPN lookup failed: ' . $e->getMessage());
        return false;
    }
    if (!$d) return false;
    if (empty($d['pesapal_tracking_id']) && $trackingId !== '') {
        $pdo->prepare('UPDATE donations SET pesapal_tracking_id = ? WHERE id = ?')->execute([$trackingId, $d['id']]);
        $d['pesapal_tracking_id'] = $trackingId;
    }
    donation_sync_status($pdo, $d);
    echo json_encode(['orderNotificationType' => 'IPNCHANGE', 'orderTrackingId' => $d['pesapal_tracking_id'], 'orderMerchantReference' => $d['donation_ref'], 'status' => 200]);
    return true;
}
