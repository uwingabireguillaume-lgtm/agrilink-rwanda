<?php
require_once __DIR__ . '/config/bootstrap.php';
requireRole('consumer');

$user = currentUser();
$orderModel = new Order($pdo);
$orders = $orderModel->byUser((int)$user['id']);

$title = 'My Orders';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/navbar.php';
?>

<section class="agri-section pt-4">
    <div class="container">
        <h1 class="agri-section-title">My Orders</h1>

        <?php if (empty($orders)): ?>
            <div class="agri-empty-state">
                <div class="icon">📦</div>
                <p>You haven't placed any orders yet.</p>
                <a href="/products.php" class="btn btn-agri-primary">Start Shopping</a>
            </div>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
                <div class="agri-form-card mb-3">
                    <div class="d-flex justify-content-between flex-wrap mb-3">
                        <div>
                            <p class="fw-bold mb-0"><?= e($order['order_number']) ?></p>
                            <p class="text-muted small mb-0"><?= date('M j, Y \a\t g:i A', strtotime($order['created_at'])) ?></p>
                        </div>
                        <div class="text-end">
                            <span class="status-pill status-<?= e($order['status']) ?>"><?= e(ucfirst($order['status'])) ?></span>
                            <p class="fw-bold agri-price mb-0 mt-1"><?= formatMoney($order['total_amount']) ?></p>
                        </div>
                    </div>
                    <?php foreach ($order['items'] as $item): ?>
                        <div class="d-flex justify-content-between border-top py-2 small">
                            <span><?= e($item['product_name']) ?> &times; <?= (int)$item['quantity'] ?> <span class="text-muted">(<?= e($item['farm_name']) ?>)</span></span>
                            <span>
                                <?= formatMoney($item['subtotal']) ?>
                                <span class="status-pill status-<?= e($item['farmer_status']) ?> ms-2"><?= e(ucfirst($item['farmer_status'])) ?></span>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
