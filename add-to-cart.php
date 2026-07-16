<?php
require_once __DIR__ . '/config/bootstrap.php';
requireRole('consumer');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectTo('/products.php');
}

$productId = (int)($_POST['product_id'] ?? 0);
$quantity = max(1, (int)($_POST['quantity'] ?? 1));

$productModel = new Product($pdo);
$product = $productModel->findById($productId);

if (!$product || !$product['is_active']) {
    setFlash('error', 'Product is not available.');
    redirectTo('/products.php');
}

$user = currentUser();
$cartModel = new Cart($pdo);
$cartId = $cartModel->getOrCreateForUser($user['id']);
$cartModel->addItem($cartId, $productId, $quantity);

setFlash('success', $product['name'] . ' added to your cart.');
redirectTo('/cart.php');
