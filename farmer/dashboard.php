<?php
require_once __DIR__ . '/../config/bootstrap.php';
requireRole('farmer');

$user = currentUser();
$farmerModel = new FarmerProfile($pdo);
$farmer = $farmerModel->findByUserId((int)$user['id']);

$productModel = new Product($pdo);
$products = $productModel->byFarmer((int)$farmer['id']);

$orderModel = new Order($pdo);
$orderItems = $orderModel->itemsForFarmer((int)$farmer['id']);

$totalSales = array_sum(array_column($orderItems, 'subtotal'));
$totalOrders = count(array_unique(array_column($orderItems, 'order_id')));
$lowStock = count(array_filter($products, fn($p) => (int)$p['stock_quantity'] < 5));

$title = 'Farmer Dashboard';
require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/navbar.php';
?>

<section class="agri-section pt-4">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-3">
                <?php require __DIR__ . '/../includes/farmer-sidebar.php'; ?>
            </div>
            <div class="col-lg-9">
                <h1 class="agri-section-title mb-1">Welcome, <?= e($farmer['farm_name']) ?></h1>
                <p class="text-muted mb-4"><?= e($farmer['district']) ?><?= $farmer['sector'] ? ', ' . e($farmer['sector']) : '' ?></p>

                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-3">
                        <div class="agri-stat-card">
                            <span class="stat-value"><?= count($products) ?></span>
                            <span class="stat-label">Products</span>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="agri-stat-card">
                            <span class="stat-value"><?= $totalOrders ?></span>
                            <span class="stat-label">Orders</span>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="agri-stat-card">
                            <span class="stat-value" style="font-size:1.4rem;"><?= formatMoney($totalSales) ?></span>
                            <span class="stat-label">Total Sales</span>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="agri-stat-card" style="border-left-color: var(--gold);">
                            <span class="stat-value"><?= $lowStock ?></span>
                            <span class="stat-label">Low Stock Items</span>
                        </div>
                    </div>
                </div>

                <div class="agri-form-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Recent Orders</h5>
                        <a href="/farmer/orders.php" class="btn btn-sm btn-agri-outline">View All</a>
                    </div>
                    <?php if (empty($orderItems)): ?>
                        <p class="text-muted mb-0">No orders yet. Once a consumer buys your products, they'll show up here.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table agri-table align-middle">
                                <thead><tr><th>Order</th><th>Product</th><th>Qty</th><th>Subtotal</th><th>Status</th></tr></thead>
                                <tbody>
                                <?php foreach (array_slice($orderItems, 0, 8) as $oi): ?>
                                    <tr>
                                        <td><?= e($oi['order_number']) ?></td>
                                        <td><?= e($oi['product_name']) ?></td>
                                        <td><?= (int)$oi['quantity'] ?></td>
                                        <td><?= formatMoney($oi['subtotal']) ?></td>
                                        <td><span class="status-pill status-<?= e($oi['farmer_status']) ?>"><?= e(ucfirst($oi['farmer_status'])) ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
