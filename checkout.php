<?php
require_once __DIR__ . '/config/bootstrap.php';
requireRole('consumer');

$user = currentUser();
$cartModel = new Cart($pdo);
$cartId = $cartModel->getOrCreateForUser($user['id']);
$items = $cartModel->items($cartId);
$total = $cartModel->total($cartId);

if (empty($items)) {
    setFlash('error', 'Your cart is empty.');
    redirectTo('/products.php');
}

$errors = [];
$title = 'Checkout';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/navbar.php';
?>

<section class="agri-section pt-4">
    <div class="container">
        <h1 class="agri-section-title">Checkout</h1>

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="agri-form-card">
                    <h5 class="mb-3">Delivery Details</h5>
                    <form method="POST" action="/place-order.php" data-validate novalidate>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="customer_name" class="form-control" required value="<?= e($user['full_name']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="customer_email" class="form-control" required value="<?= e($user['email']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone</label>
                                <input type="text" name="customer_phone" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Payment Method</label>
                                <select name="payment_method" class="form-select">
                                    <option value="cash_on_delivery">Cash on Delivery</option>
                                    <option value="mobile_money">Mobile Money</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Delivery Address</label>
                                <textarea name="shipping_address" class="form-control" rows="3" required placeholder="Street, sector, district..."></textarea>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-agri-primary btn-lg w-100 mt-4">Place Order &mdash; <?= formatMoney($total) ?></button>
                    </form>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="agri-form-card">
                    <h5 class="mb-3">Order Review</h5>
                    <?php foreach ($items as $item): ?>
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <div>
                                <p class="mb-0 fw-semibold small"><?= e($item['name']) ?> &times; <?= (int)$item['quantity'] ?></p>
                                <p class="mb-0 text-muted small">from <?= e($item['farm_name']) ?></p>
                            </div>
                            <span class="fw-semibold"><?= formatMoney($item['price'] * $item['quantity']) ?></span>
                        </div>
                    <?php endforeach; ?>
                    <div class="d-flex justify-content-between mt-3">
                        <span class="fw-bold">Total</span>
                        <span class="fw-bold agri-price"><?= formatMoney($total) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
