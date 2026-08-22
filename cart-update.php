<?php
require_once __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify()) {
    redirect(SITE_URL . '/cart.php');
}

$action = $_POST['action'] ?? '';
$productId = (int) ($_POST['product_id'] ?? 0);

if (!empty($_SESSION['cart'][$productId])) {
    if ($action === 'remove') {
        unset($_SESSION['cart'][$productId]);
        flash_set('success', 'Item removed from cart.');
    } elseif ($action === 'update') {
        $qty = max(1, (int) ($_POST['qty'] ?? 1));
        $_SESSION['cart'][$productId]['qty'] = $qty;
    }
}

if ($action === 'clear') {
    $_SESSION['cart'] = [];
}

redirect(SITE_URL . '/cart.php');
