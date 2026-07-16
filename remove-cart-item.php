<?php
require_once __DIR__ . '/config/bootstrap.php';
requireRole('consumer');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cartItemId = (int)($_POST['cart_item_id'] ?? 0);
    $cartModel = new Cart($pdo);
    $cartModel->removeItem($cartItemId);
    setFlash('success', 'Item removed from cart.');
}

redirectTo('/cart.php');
