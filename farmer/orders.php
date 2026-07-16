<?php
require_once __DIR__ . '/../config/bootstrap.php';
requireRole('farmer');

$user = currentUser();
$farmerModel = new FarmerProfile($pdo);
$farmer = $farmerModel->findByUserId((int)$user['id']);

$orderModel = new Order($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderItemId = (int)($_POST['order_item_id'] ?? 0);
    $status = $_POST['status'] ?? 'pending';
    $orderModel->updateItemStatus($orderItemId, (int)$farmer['id'], $status);
    setFlash('success', 'Order status updated.');
    redirectTo('/farmer/orders.php');
}

$orderItems = $orderModel->itemsForFarmer((int)$farmer['id']);

$title = 'My Orders';
require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/navbar.php';
?>

<section class="agri-section pt-4">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-3"><?php require __DIR__ . '/../includes/farmer-sidebar.php'; ?></div>
            <div class="col-lg-9">
                <h1 class="agri-section-title mb-4">My Orders</h1>

                <?php if (empty($orderItems)): ?>
                    <div class="agri-empty-state">
                        <div class="icon">📦</div>
                        <p>No orders yet for your products.</p>
                    </div>
                <?php else: ?>
                    <div class="agri-form-card p-0">
                        <div class="table-responsive">
                            <table class="table agri-table align-middle mb-0">
                                <thead>
                                    <tr><th>Order #</th><th>Customer</th><th>Product</th><th>Qty</th><th>Subtotal</th><th>Status</th><th>Update</th></tr>
                                </thead>
                                <tbody>
                                <?php foreach ($orderItems as $oi): ?>
                                    <tr>
                                        <td><?= e($oi['order_number']) ?></td>
                                        <td>
                                            <?= e($oi['customer_name']) ?><br>
                                            <span class="text-muted small"><?= e($oi['customer_phone']) ?></span>
                                        </td>
                                        <td><?= e($oi['product_name']) ?></td>
                                        <td><?= (int)$oi['quantity'] ?></td>
                                        <td><?= formatMoney($oi['subtotal']) ?></td>
                                        <td><span class="status-pill status-<?= e($oi['farmer_status']) ?>"><?= e(ucfirst($oi['farmer_status'])) ?></span></td>
                                        <td>
                                            <form method="POST" class="d-flex gap-1">
                                                <input type="hidden" name="order_item_id" value="<?= (int)$oi['id'] ?>">
                                                <select name="status" class="form-select form-select-sm" style="width:130px;">
                                                    <?php foreach (['pending','confirmed','shipped','delivered','cancelled'] as $s): ?>
                                                        <option value="<?= $s ?>" <?= $oi['farmer_status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <button type="submit" class="btn btn-sm btn-agri-outline">Go</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
