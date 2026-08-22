<?php
require_once __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(SITE_URL . '/products.php');
}

$productId = (int) ($_POST['product_id'] ?? 0);
$qty = max(1, (int) ($_POST['qty'] ?? 1));

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? AND status = 1");
$stmt->execute([$productId]);
$product = $stmt->fetch();

if (!$product) {
    flash_set('error', 'That product is not available.');
    redirect($_SERVER['HTTP_REFERER'] ?? (SITE_URL . '/products.php'));
}

if (empty($_SESSION['cart'])) $_SESSION['cart'] = [];

if (isset($_SESSION['cart'][$productId])) {
    $_SESSION['cart'][$productId]['qty'] += $qty;
} else {
    $_SESSION['cart'][$productId] = [
        'product_id' => $product['id'],
        'name'       => $product['name'],
        'price'      => (float) $product['price'],
        'unit'       => $product['unit'],
        'image'      => $product['image'],
        'qty'        => $qty,
    ];
}

flash_set('success', h($product['name']) . ' added to your cart.');
redirect(SITE_URL . '/cart.php');
