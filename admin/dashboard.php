<?php
require_once __DIR__ . '/../config/bootstrap.php';
requireRole('admin');

$userModel = new User($pdo);
$farmerModel = new FarmerProfile($pdo);
$productModel = new Product($pdo);
$orderModel = new Order($pdo);
$categoryModel = new Category($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'toggle_approval') {
        $farmerModel->toggleApproval((int)$_POST['farmer_id']);
        setFlash('success', 'Farmer status updated.');
    } elseif (isset($_POST['action']) && $_POST['action'] === 'add_category') {
        $name = trim($_POST['name'] ?? '');
        if ($name !== '') {
            try {
                $categoryModel->create($name, trim($_POST['description'] ?? ''));
                setFlash('success', 'Category added.');
            } catch (Exception $e) {
                setFlash('error', 'Could not add category (it may already exist).');
            }
        }
    }
    redirectTo('/admin/dashboard.php');
}

$stats = [
    'consumers' => $userModel->countByRole('consumer'),
    'farmers' => $farmerModel->count(),
    'products' => $productModel->count(),
    'orders' => $orderModel->count(),
];
$farmers = $farmerModel->all();
$recentOrders = $orderModel->recent(10);
$categories = $categoryModel->all();

$title = 'Admin Dashboard';
require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/navbar.php';
?>

<section class="agri-section pt-4">
    <div class="container">
        <h1 class="agri-section-title mb-4">Admin Dashboard</h1>

        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="agri-stat-card"><span class="stat-value"><?= $stats['consumers'] ?></span><span class="stat-label">Consumers</span></div>
            </div>
            <div class="col-6 col-md-3">
                <div class="agri-stat-card"><span class="stat-value"><?= $stats['farmers'] ?></span><span class="stat-label">Farmers</span></div>
            </div>
            <div class="col-6 col-md-3">
                <div class="agri-stat-card"><span class="stat-value"><?= $stats['products'] ?></span><span class="stat-label">Products</span></div>
            </div>
            <div class="col-6 col-md-3">
                <div class="agri-stat-card"><span class="stat-value"><?= $stats['orders'] ?></span><span class="stat-label">Orders</span></div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="agri-form-card">
                    <h5 class="mb-3">Farmers</h5>
                    <div class="table-responsive">
                        <table class="table agri-table align-middle">
                            <thead><tr><th>Farm</th><th>Owner</th><th>District</th><th>Status</th><th></th></tr></thead>
                            <tbody>
                            <?php foreach ($farmers as $f): ?>
                                <tr>
                                    <td><?= e($f['farm_name']) ?></td>
                                    <td><?= e($f['full_name']) ?></td>
                                    <td><?= e($f['district']) ?></td>
                                    <td><span class="status-pill <?= $f['is_approved'] ? 'status-delivered' : 'status-pending' ?>"><?= $f['is_approved'] ? 'Approved' : 'Pending' ?></span></td>
                                    <td>
                                        <form method="POST">
                                            <input type="hidden" name="action" value="toggle_approval">
                                            <input type="hidden" name="farmer_id" value="<?= (int)$f['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-agri-outline"><?= $f['is_approved'] ? 'Revoke' : 'Approve' ?></button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="agri-form-card mt-3">
                    <h5 class="mb-3">Recent Orders</h5>
                    <div class="table-responsive">
                        <table class="table agri-table align-middle">
                            <thead><tr><th>Order #</th><th>Customer</th><th>Total</th><th>Status</th><th>Date</th></tr></thead>
                            <tbody>
                            <?php foreach ($recentOrders as $o): ?>
                                <tr>
                                    <td><?= e($o['order_number']) ?></td>
                                    <td><?= e($o['customer_name']) ?></td>
                                    <td><?= formatMoney($o['total_amount']) ?></td>
                                    <td><span class="status-pill status-<?= e($o['status']) ?>"><?= e(ucfirst($o['status'])) ?></span></td>
                                    <td><?= date('M j, Y', strtotime($o['created_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="agri-form-card">
                    <h5 class="mb-3">Categories</h5>
                    <ul class="list-group list-group-flush mb-3">
                        <?php foreach ($categories as $c): ?>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <?= e($c['name']) ?>
                                <span class="text-muted small"><?= e($c['description']) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <form method="POST" class="d-flex gap-2">
                        <input type="hidden" name="action" value="add_category">
                        <input type="text" name="name" class="form-control" placeholder="New category name" required>
                        <button type="submit" class="btn btn-agri-primary">Add</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
