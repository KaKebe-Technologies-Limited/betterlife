<?php
/**
 * Pesapal server-to-server IPN webhook. Pesapal posts (or GETs, depending
 * on configuration) order_tracking_id/order_merchant_reference here when
 * a transaction's status changes — this is the reliable source of truth,
 * independent of whether the shopper's browser ever returns to our site.
 */
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/pesapal.php';
require_once __DIR__ . '/includes/mailer.php';

$trackingId = $_REQUEST['OrderTrackingId'] ?? $_REQUEST['order_tracking_id'] ?? '';
$merchantRef = $_REQUEST['OrderMerchantReference'] ?? $_REQUEST['order_merchant_reference'] ?? '';

header('Content-Type: application/json');

if ($trackingId === '' && $merchantRef === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Missing tracking reference']);
    exit;
}

$stmt = $merchantRef !== ''
    ? $pdo->prepare("SELECT * FROM orders WHERE order_ref = ? LIMIT 1")
    : $pdo->prepare("SELECT * FROM orders WHERE pesapal_tracking_id = ? LIMIT 1");
$stmt->execute([$merchantRef !== '' ? $merchantRef : $trackingId]);
$order = $stmt->fetch();

if (!$order) {
    http_response_code(404);
    echo json_encode(['error' => 'Order not found']);
    exit;
}

if (empty($order['pesapal_tracking_id']) && $trackingId !== '') {
    $pdo->prepare("UPDATE orders SET pesapal_tracking_id = ? WHERE id = ?")->execute([$trackingId, $order['id']]);
    $order['pesapal_tracking_id'] = $trackingId;
}

$newStatus = pesapal_sync_order_status($pdo, $order);

if ($newStatus === 'paid') {
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->execute([$order['id']]);
    $order = $stmt->fetch();
    if (!$order['receipt_sent']) {
        $items = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
        $items->execute([$order['id']]);
        try {
            send_receipt_to_customer($pdo, $order, $items->fetchAll());
            $pdo->prepare("UPDATE orders SET receipt_sent = 1 WHERE id = ?")->execute([$order['id']]);
        } catch (Throwable $e) {
            error_log($e->getMessage());
        }
    }
}

echo json_encode([
    'orderNotificationType' => 'IPNCHANGE',
    'orderTrackingId'       => $order['pesapal_tracking_id'],
    'orderMerchantReference'=> $order['order_ref'],
    'status'                => 200,
]);
