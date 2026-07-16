<?php
require_once __DIR__ . '/config/bootstrap.php';
requireRole('consumer');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectTo('/checkout.php');
}

$user = currentUser();
$cartModel = new Cart($pdo);
$cartId = $cartModel->getOrCreateForUser($user['id']);
$items = $cartModel->items($cartId);

$customer = [
    'customer_name'    => trim($_POST['customer_name'] ?? ''),
    'customer_email'   => trim($_POST['customer_email'] ?? ''),
    'customer_phone'   => trim($_POST['customer_phone'] ?? ''),
    'shipping_address' => trim($_POST['shipping_address'] ?? ''),
    'payment_method'   => $_POST['payment_method'] ?? 'cash_on_delivery',
];

if ($customer['customer_name'] === '' || $customer['customer_phone'] === '' || $customer['shipping_address'] === '') {
    setFlash('error', 'Please fill in all required delivery details.');
    redirectTo('/checkout.php');
}

// Map cart items to the shape Order::placeOrder expects
$orderItemsInput = array_map(function ($item) {
    return [
        'product_id'     => $item['product_id'],
        'name'           => $item['name'],
        'price'          => $item['price'],
        'quantity'       => $item['quantity'],
        'stock_quantity' => $item['stock_quantity'],
    ];
}, $items);

$orderModel = new Order($pdo);

try {
    $order = $orderModel->placeOrder((int)$user['id'], $orderItemsInput, $customer);
    $cartModel->clear($cartId);
} catch (Exception $e) {
    setFlash('error', 'Could not place order: ' . $e->getMessage());
    redirectTo('/checkout.php');
}

$title = 'Order Confirmed';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/navbar.php';
?>

<section class="agri-section pt-4">
    <div class="container" style="max-width: 720px;">
        <div class="agri-form-card text-center">
            <div style="font-size:3rem;">✅</div>
            <h1 class="agri-section-title">Order Confirmed!</h1>
            <p class="text-muted">Thank you, <?= e($order['customer_name']) ?>. Your order has been placed successfully.</p>
            <p class="fw-bold fs-5">Order Number: <?= e($order['order_number']) ?></p>
        </div>

        <?php
        $grouped = [];
        foreach ($order['items'] as $item) {
            $grouped[$item['farm_name']][] = $item;
        }
        ?>

        <div class="agri-form-card mt-3">
            <h5 class="mb-3">Order Details</h5>
            <?php foreach ($grouped as $farmName => $farmItems): ?>
                <p class="fw-semibold mb-2">🌾 From <?= e($farmName) ?></p>
                <?php foreach ($farmItems as $item): ?>
                    <div class="d-flex justify-content-between border-bottom py-2">
                        <span><?= e($item['product_name']) ?> &times; <?= (int)$item['quantity'] ?></span>
                        <span class="fw-semibold"><?= formatMoney($item['subtotal']) ?></span>
                    </div>
                <?php endforeach; ?>
            <?php endforeach; ?>
            <div class="d-flex justify-content-between mt-3">
                <span class="fw-bold">Total Paid</span>
                <span class="fw-bold agri-price"><?= formatMoney($order['total_amount']) ?></span>
            </div>
            <hr>
            <p class="mb-1"><strong>Delivery Address:</strong> <?= e($order['shipping_address']) ?></p>
            <p class="mb-1"><strong>Payment Method:</strong> <?= e(ucwords(str_replace('_', ' ', $order['payment_method']))) ?></p>
            <p class="mb-0"><strong>Status:</strong> <span class="status-pill status-pending">Pending</span></p>
        </div>

        <div class="text-center mt-4">
            <a href="/products.php" class="btn btn-agri-outline">Continue Shopping</a>
            <a href="/my-orders.php" class="btn btn-agri-primary">View My Orders</a>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
