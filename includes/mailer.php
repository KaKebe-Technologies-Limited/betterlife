<?php
/**
 * Email sending via PHPMailer + Gmail SMTP (App Password), configured from
 * Admin → Site Settings → Payments & Email. Used for order alerts,
 * confirmations and receipts.
 */
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/../vendor/phpmailer/src/Exception.php';
require_once __DIR__ . '/../vendor/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/../vendor/phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

/**
 * Low-level sender. Returns true/false; never throws (logs instead) so a
 * failed email never breaks the checkout/order flow.
 */
function send_email(PDO $pdo, string $toEmail, string $toName, string $subject, string $htmlBody): bool
{
    $host = setting($pdo, 'smtp_host');
    $user = setting($pdo, 'smtp_username');
    $pass = setting($pdo, 'smtp_app_password');
    $port = (int) setting($pdo, 'smtp_port', '587');
    $fromName = setting($pdo, 'smtp_from_name', setting($pdo, 'site_name', 'BetterLife International'));

    if ($host === '' || $user === '' || $pass === '') {
        error_log('Email not sent (SMTP not configured): ' . $subject);
        return false;
    }

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = $host;
        $mail->SMTPAuth = true;
        $mail->Username = $user;
        $mail->Password = $pass;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $port;

        $mail->setFrom($user, $fromName);
        $mail->addAddress($toEmail, $toName);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $htmlBody;
        $mail->AltBody = trim(strip_tags(preg_replace('/<br\s*\/?>/i', "\n", $htmlBody)));

        $mail->send();
        return true;
    } catch (PHPMailerException $e) {
        error_log('Email send failed: ' . $mail->ErrorInfo);
        return false;
    }
}

/**
 * Shared branded email wrapper (logo + title band + body + footer).
 */
function email_wrap(PDO $pdo, string $title, string $bodyHtml): string
{
    $logoUrl = full_asset_url(setting($pdo, 'logo', 'assets/img/logo.png'));
    $siteName = h(setting($pdo, 'site_name', 'BetterLife International'));

    return '
    <div style="font-family:Arial,Helvetica,sans-serif;background:#f4f6f5;padding:30px 16px;">
      <div style="max-width:560px;margin:0 auto;background:#ffffff;border-radius:14px;overflow:hidden;border:1px solid #e4e3d8;">
        <div style="background:#0b3d2e;padding:26px 30px;text-align:center;">
          <img src="' . h($logoUrl) . '" alt="' . $siteName . '" style="height:42px;">
        </div>
        <div style="padding:32px 30px;">
          <h2 style="font-family:Arial,sans-serif;color:#0b3d2e;font-size:20px;margin:0 0 18px;">' . h($title) . '</h2>
          ' . $bodyHtml . '
        </div>
        <div style="background:#f4f6f5;padding:18px 30px;text-align:center;font-size:12px;color:#6b7972;">
          ' . $siteName . ' &middot; ' . h(setting($pdo, 'address')) . '
        </div>
      </div>
    </div>';
}

function order_items_table_html(array $items, string $currency = 'UGX'): string
{
    $rows = '';
    foreach ($items as $it) {
        $rows .= '<tr>
            <td style="padding:10px 0;border-bottom:1px solid #eee;font-size:14px;">' . h($it['product_name']) . '</td>
            <td style="padding:10px 0;border-bottom:1px solid #eee;font-size:14px;text-align:center;">' . (int) $it['quantity'] . '</td>
            <td style="padding:10px 0;border-bottom:1px solid #eee;font-size:14px;text-align:right;">' . $currency . ' ' . number_format((float) $it['line_total']) . '</td>
        </tr>';
    }
    return '<table style="width:100%;border-collapse:collapse;margin:14px 0;">
        <tr>
            <th style="text-align:left;font-size:12px;color:#6b7972;padding-bottom:8px;border-bottom:2px solid #e4e3d8;">Item</th>
            <th style="text-align:center;font-size:12px;color:#6b7972;padding-bottom:8px;border-bottom:2px solid #e4e3d8;">Qty</th>
            <th style="text-align:right;font-size:12px;color:#6b7972;padding-bottom:8px;border-bottom:2px solid #e4e3d8;">Amount</th>
        </tr>' . $rows . '</table>';
}

/* ---------------------------------------------------------------------
 * Order-specific emails
 * ------------------------------------------------------------------- */

function send_order_confirmation_to_customer(PDO $pdo, array $order, array $items): bool
{
    $body = '
      <p style="font-size:14px;color:#2c3a32;">Hi ' . h($order['customer_name']) . ',</p>
      <p style="font-size:14px;color:#2c3a32;">Thank you for your order from BetterLife Farm! Here are your order details:</p>
      <p style="font-size:14px;"><strong>Order Ref:</strong> ' . h($order['order_ref']) . '<br>
      <strong>Delivery Location:</strong> ' . h($order['delivery_location']) . '</p>
      ' . order_items_table_html($items, $order['currency']) . '
      <p style="font-size:15px;font-weight:bold;text-align:right;">Total: ' . h($order['currency']) . ' ' . number_format((float) $order['total_amount']) . '</p>
      <p style="font-size:14px;color:#2c3a32;">To complete your order, please finish payment via the secure link provided at checkout (card or mobile money). We will email you a receipt once payment is confirmed.</p>';

    return send_email($pdo, $order['customer_email'], $order['customer_name'], 'Your BetterLife Farm order ' . $order['order_ref'], email_wrap($pdo, 'Order Received', $body));
}

function send_order_alert_to_admin(PDO $pdo, array $order, array $items): bool
{
    $adminEmail = setting($pdo, 'admin_alert_email', 'ot.sedrick@gmail.com');
    $body = '
      <p style="font-size:14px;color:#2c3a32;">A new order has been placed on BetterLife Farm.</p>
      <p style="font-size:14px;">
        <strong>Order Ref:</strong> ' . h($order['order_ref']) . '<br>
        <strong>Customer:</strong> ' . h($order['customer_name']) . '<br>
        <strong>Email:</strong> ' . h($order['customer_email']) . '<br>
        <strong>Phone:</strong> ' . h($order['customer_phone']) . '<br>
        <strong>Delivery Location:</strong> ' . h($order['delivery_location']) . '<br>
        ' . ($order['notes'] ? '<strong>Notes:</strong> ' . nl2br(h($order['notes'])) . '<br>' : '') . '
      </p>
      ' . order_items_table_html($items, $order['currency']) . '
      <p style="font-size:15px;font-weight:bold;text-align:right;">Total: ' . h($order['currency']) . ' ' . number_format((float) $order['total_amount']) . '</p>
      <p style="font-size:13px;color:#6b7972;">Status: ' . h(ucfirst($order['status'])) . ' — view full details in the admin dashboard under Orders.</p>';

    return send_email($pdo, $adminEmail, 'BetterLife Admin', 'New order: ' . $order['order_ref'] . ' — ' . h($order['customer_name']), email_wrap($pdo, 'New Order Alert', $body));
}

function send_receipt_to_customer(PDO $pdo, array $order, array $items): bool
{
    $body = '
      <p style="font-size:14px;color:#2c3a32;">Hi ' . h($order['customer_name']) . ',</p>
      <p style="font-size:14px;color:#2c3a32;">Your payment has been received. Thank you for supporting BetterLife Farm and our community programs!</p>
      <p style="font-size:14px;">
        <strong>Receipt / Order Ref:</strong> ' . h($order['order_ref']) . '<br>
        <strong>Paid On:</strong> ' . h(format_date($order['paid_at'], 'F j, Y g:i A')) . '<br>
        <strong>Delivery Location:</strong> ' . h($order['delivery_location']) . '
      </p>
      ' . order_items_table_html($items, $order['currency']) . '
      <p style="font-size:16px;font-weight:bold;text-align:right;color:#16593f;">Total Paid: ' . h($order['currency']) . ' ' . number_format((float) $order['total_amount']) . '</p>
      <p style="font-size:13px;color:#6b7972;">This email serves as your official receipt. Our team will be in touch shortly to arrange delivery.</p>';

    return send_email($pdo, $order['customer_email'], $order['customer_name'], 'Receipt for order ' . $order['order_ref'], email_wrap($pdo, 'Payment Receipt', $body));
}
