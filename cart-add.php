<?php
require_once __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(SITE_URL . '/products.php');
}

// The shop adds in place (it asks for JSON); without JavaScript the form posts here and goes to the basket
$wantsJson = str_contains((string) ($_SERVER['HTTP_ACCEPT'] ?? ''), 'application/json');

$productId = (int) ($_POST['product_id'] ?? 0);
$qty = min(99, max(1, (int) ($_POST['qty'] ?? 1)));

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? AND status = 1");
$stmt->execute([$productId]);
$product = $stmt->fetch();

if (!$product) {
    if ($wantsJson) {
        header('Content-Type: application/json');
        http_response_code(404);
        echo json_encode(['ok' => false, 'message' => 'That product is not available.']);
        exit;
    }
    flash_set('error', 'That product is not available.');
    redirect($_SERVER['HTTP_REFERER'] ?? (SITE_URL . '/products.php'));
}

if (empty($_SESSION['cart'])) $_SESSION['cart'] = [];

if (isset($_SESSION['cart'][$productId])) {
    $_SESSION['cart'][$productId]['qty'] = min(99, $_SESSION['cart'][$productId]['qty'] + $qty);
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

if ($wantsJson) {
    $count = array_sum(array_column($_SESSION['cart'], 'qty'));
    $total = array_sum(array_map(fn($i) => $i['price'] * $i['qty'], $_SESSION['cart']));
    header('Content-Type: application/json');
    echo json_encode(['ok' => true, 'name' => $product['name'], 'count' => $count, 'total' => format_price($total)]);
    exit;
}

flash_set('success', $product['name'] . ' is in your basket.');
redirect(SITE_URL . '/cart.php');
