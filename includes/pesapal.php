<?php
/**
 * Pesapal payment integration (hosted checkout — supports card & mobile
 * money in one flow). Credentials are read from the `settings` table so
 * they can be managed from Admin → Site Settings → Payments & Email.
 *
 * Pattern adapted from the working chama-funds-official integration.
 */
require_once __DIR__ . '/functions.php';

class PesapalApiClient
{
    private string $consumerKey;
    private string $consumerSecret;
    private bool $isSandbox;
    private string $baseUrl;

    public function __construct(string $consumerKey, string $consumerSecret, bool $isSandbox = true)
    {
        $this->consumerKey = $consumerKey;
        $this->consumerSecret = $consumerSecret;
        $this->isSandbox = $isSandbox;
        $this->baseUrl = $isSandbox
            ? 'https://cybqa.pesapal.com/pesapalv3'
            : 'https://pay.pesapal.com/v3';
    }

    private function request(string $method, string $path, ?array $body = null, ?string $token = null)
    {
        $ch = curl_init();
        $url = rtrim($this->baseUrl, '/') . $path;
        $headers = ['Accept: application/json', 'Content-Type: application/json'];
        if ($token !== null) $headers[] = 'Authorization: Bearer ' . $token;

        $opts = [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_HTTPHEADER     => $headers,
        ];
        if ($body !== null) $opts[CURLOPT_POSTFIELDS] = json_encode($body);

        curl_setopt_array($ch, $opts);
        $response = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($response === false) throw new Exception('Pesapal connection error: ' . $curlError);
        if ($httpCode >= 400) throw new Exception('Pesapal HTTP ' . $httpCode . ': ' . $response);

        return json_decode($response) ?: (object) ['raw' => $response];
    }

    public function getToken(): string
    {
        $res = $this->request('POST', '/api/Auth/RequestToken', [
            'consumer_key' => $this->consumerKey,
            'consumer_secret' => $this->consumerSecret,
        ]);
        if (empty($res->token)) throw new Exception('Pesapal token request failed.');
        return $res->token;
    }

    public function registerIpnUrl(string $ipnUrl)
    {
        $token = $this->getToken();
        return $this->request('POST', '/api/URLSetup/RegisterIPN', [
            'url' => $ipnUrl,
            'ipn_notification_type' => 'POST',
        ], $token);
    }

    public function submitOrder(array $orderData)
    {
        $token = $this->getToken();
        return $this->request('POST', '/api/Transactions/SubmitOrderRequest', $orderData, $token);
    }

    public function getTransactionStatus(string $orderTrackingId)
    {
        $token = $this->getToken();
        return $this->request('GET', '/api/Transactions/GetTransactionStatus?orderTrackingId=' . urlencode($orderTrackingId), null, $token);
    }
}

function pesapal_client(PDO $pdo): PesapalApiClient
{
    return new PesapalApiClient(
        setting($pdo, 'pesapal_consumer_key'),
        setting($pdo, 'pesapal_consumer_secret'),
        setting($pdo, 'pesapal_sandbox', '0') === '1'
    );
}

function pesapal_callback_url(): string
{
    return public_base_url() . SITE_URL . '/order-callback.php';
}

function pesapal_ipn_url(): string
{
    return public_base_url() . SITE_URL . '/order-ipn.php';
}

/**
 * Get (or lazily register) the Pesapal IPN id, cached in `settings`.
 */
function pesapal_ipn_id(PDO $pdo): ?string
{
    $cached = setting($pdo, 'pesapal_ipn_id');
    if ($cached !== '') return $cached;

    try {
        $client = pesapal_client($pdo);
        $res = $client->registerIpnUrl(pesapal_ipn_url());
        $ipnId = $res->ipn_id ?? null;
        if ($ipnId) {
            $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES ('pesapal_ipn_id', ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)")
                ->execute([$ipnId]);
            return $ipnId;
        }
    } catch (Exception $e) {
        error_log('Pesapal IPN registration failed: ' . $e->getMessage());
    }
    return null;
}

/**
 * Kick off payment for an existing pending order. Returns the Pesapal
 * hosted-checkout redirect URL on success, or throws on failure.
 */
function pesapal_initiate_order_payment(PDO $pdo, array $order): string
{
    $client = pesapal_client($pdo);
    $ipnId = pesapal_ipn_id($pdo);

    $orderData = [
        'id'              => $order['order_ref'],
        'currency'        => $order['currency'],
        'amount'          => (float) $order['total_amount'],
        'description'     => 'BetterLife Farm order ' . $order['order_ref'],
        'callback_url'    => pesapal_callback_url() . '?ref=' . urlencode($order['order_ref']),
        'billing_address' => [
            'email_address' => $order['customer_email'],
            'phone_number'  => preg_replace('/[^0-9]/', '', $order['customer_phone']),
            'country_code'  => 'UG',
            'first_name'    => mb_substr($order['customer_name'], 0, 50),
            'last_name'     => '',
            'line_1'        => mb_substr($order['delivery_location'], 0, 100),
            'city'          => 'Kampala',
            'state'         => '',
            'postal_code'   => '',
        ],
    ];
    if ($ipnId) $orderData['notification_id'] = $ipnId;

    $res = $client->submitOrder($orderData);
    $redirectUrl = $res->redirect_url ?? null;
    $trackingId = $res->order_tracking_id ?? null;

    if (!$redirectUrl) {
        $err = $res->error->message ?? $res->message ?? json_encode($res);
        throw new Exception('Pesapal did not return a checkout link: ' . $err);
    }

    $pdo->prepare("UPDATE orders SET pesapal_tracking_id = ? WHERE id = ?")->execute([$trackingId, $order['id']]);

    return $redirectUrl;
}

/**
 * Re-check a pending order's payment status with Pesapal and update the DB.
 * Returns the resulting order status string.
 */
function pesapal_sync_order_status(PDO $pdo, array $order): string
{
    if ($order['status'] !== 'pending' || empty($order['pesapal_tracking_id'])) {
        return $order['status'];
    }
    try {
        $client = pesapal_client($pdo);
        $res = $client->getTransactionStatus($order['pesapal_tracking_id']);
        $desc = strtoupper($res->payment_status_description ?? $res->status ?? '');
    } catch (Exception $e) {
        error_log('Pesapal status check failed: ' . $e->getMessage());
        return $order['status'];
    }

    if ($desc === 'COMPLETED') {
        $pdo->prepare("UPDATE orders SET status = 'paid', paid_at = NOW() WHERE id = ? AND status = 'pending'")->execute([$order['id']]);
        return 'paid';
    }
    if (in_array($desc, ['FAILED', 'INVALID', 'REVERSED'], true)) {
        $pdo->prepare("UPDATE orders SET status = 'failed' WHERE id = ? AND status = 'pending'")->execute([$order['id']]);
        return 'failed';
    }
    return 'pending';
}

function generate_order_ref(): string
{
    return 'BL' . date('ymd') . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
}
