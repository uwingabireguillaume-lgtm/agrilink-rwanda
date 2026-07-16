<?php
require_once __DIR__ . '/../config/bootstrap.php';
requireRole('farmer');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = currentUser();
    $farmerModel = new FarmerProfile($pdo);
    $farmer = $farmerModel->findByUserId((int)$user['id']);

    $productModel = new Product($pdo);
    $productModel->delete((int)($_POST['id'] ?? 0), (int)$farmer['id']);
    setFlash('success', 'Product deleted.');
}

redirectTo('/farmer/products.php');
