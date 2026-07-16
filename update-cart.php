<?php
require_once __DIR__ . '/config/bootstrap.php';
requireRole('consumer');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cartItemId = (int)($_POST['cart_item_id'] ?? 0);
    $quantity = (int)($_POST['quantity'] ?? 1);

    $cartModel = new Cart($pdo);
    $cartModel->updateItemQuantity($cartItemId, $quantity);
    setFlash('success', 'Cart updated.');
}

redirectTo('/cart.php');
