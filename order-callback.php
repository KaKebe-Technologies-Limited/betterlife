<?php
/**
 * Pesapal redirects the shopper back here after they attempt payment on
 * the hosted checkout page. We re-verify the transaction status directly
 * with Pesapal (never trust the redirect alone) and forward to the
 * order confirmation page.
 */
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/pesapal.php';
require_once __DIR__ . '/includes/mailer.php';

$ref = $_GET['ref'] ?? '';
$stmt = $pdo->prepare("SELECT * FROM orders WHERE order_ref = ?");
$stmt->execute([$ref]);
$order = $stmt->fetch();

if (!$order) {
    redirect(SITE_URL . '/products.php');
}

if ($order['status'] === 'pending') {
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
}

redirect(SITE_URL . '/order-confirmation.php?ref=' . urlencode($order['order_ref']));
