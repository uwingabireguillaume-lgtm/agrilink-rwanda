<?php
require_once __DIR__ . '/config/bootstrap.php';
requireRole('consumer');

$user = currentUser();
$cartModel = new Cart($pdo);
$cartId = $cartModel->getOrCreateForUser($user['id']);
$items = $cartModel->items($cartId);
$total = $cartModel->total($cartId);

$title = 'Your Cart';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/navbar.php';
?>

<section class="agri-section pt-4">
    <div class="container">
        <h1 class="agri-section-title">Your Cart</h1>

        <?php if (empty($items)): ?>
            <div class="agri-empty-state">
                <div class="icon">🛒</div>
                <p>Your cart is empty.</p>
                <a href="/products.php" class="btn btn-agri-primary">Browse Products</a>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <div class="col-lg-8">
                    <?php
                    $grouped = [];
                    foreach ($items as $item) {
                        $grouped[$item['farm_name']][] = $item;
                    }
                    ?>
                    <?php foreach ($grouped as $farmName => $farmItems): ?>
                        <div class="agri-form-card mb-3">
                            <p class="fw-semibold mb-3">🌾 Sold by <?= e($farmName) ?></p>
                            <?php foreach ($farmItems as $item): ?>
                                <div class="d-flex align-items-center gap-3 py-2 border-bottom">
                                    <img src="<?= e($item['image_url']) ?>" alt="" style="width:64px;height:64px;object-fit:cover;border-radius:10px;">
                                    <div class="flex-grow-1">
                                        <p class="mb-0 fw-semibold"><?= e($item['name']) ?></p>
                                        <p class="mb-0 text-muted small"><?= formatMoney($item['price']) ?> / <?= e($item['unit']) ?></p>
                                    </div>
                                    <form method="POST" action="/update-cart.php" class="d-flex align-items-center gap-2">
                                        <input type="hidden" name="cart_item_id" value="<?= (int)$item['cart_item_id'] ?>">
                                        <input type="number" name="quantity" value="<?= (int)$item['quantity'] ?>" min="1" max="<?= (int)$item['stock_quantity'] ?>" class="form-control form-control-sm" style="width:70px;">
                                        <button type="submit" class="btn btn-sm btn-agri-outline">Update</button>
                                    </form>
                                    <p class="mb-0 fw-semibold" style="width:110px; text-align:right;"><?= formatMoney($item['price'] * $item['quantity']) ?></p>
                                    <form method="POST" action="/remove-cart-item.php">
                                        <input type="hidden" name="cart_item_id" value="<?= (int)$item['cart_item_id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">&times;</button>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="col-lg-4">
                    <div class="agri-form-card">
                        <h5 class="mb-3">Order Summary</h5>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal</span>
                            <span class="fw-semibold"><?= formatMoney($total) ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Delivery</span>
                            <span class="text-muted">Calculated at checkout</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="fw-bold">Total</span>
                            <span class="fw-bold agri-price"><?= formatMoney($total) ?></span>
                        </div>
                        <a href="/checkout.php" class="btn btn-agri-primary w-100">Proceed to Checkout</a>
                        <a href="/products.php" class="btn btn-link w-100 mt-2">Continue Shopping</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
