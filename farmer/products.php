<?php
require_once __DIR__ . '/../config/bootstrap.php';
requireRole('farmer');

$user = currentUser();
$farmerModel = new FarmerProfile($pdo);
$farmer = $farmerModel->findByUserId((int)$user['id']);

$productModel = new Product($pdo);
$products = $productModel->byFarmer((int)$farmer['id']);

$title = 'My Products';
require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/navbar.php';
?>

<section class="agri-section pt-4">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-3"><?php require __DIR__ . '/../includes/farmer-sidebar.php'; ?></div>
            <div class="col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="agri-section-title mb-0">My Products</h1>
                    <a href="/farmer/product-form.php" class="btn btn-agri-primary">+ Add Product</a>
                </div>

                <?php if (empty($products)): ?>
                    <div class="agri-empty-state">
                        <div class="icon">🌱</div>
                        <p>You haven't listed any products yet.</p>
                        <a href="/farmer/product-form.php" class="btn btn-agri-primary">Add Your First Product</a>
                    </div>
                <?php else: ?>
                    <div class="agri-form-card p-0">
                        <div class="table-responsive">
                            <table class="table agri-table align-middle mb-0">
                                <thead>
                                    <tr><th></th><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Status</th><th></th></tr>
                                </thead>
                                <tbody>
                                <?php foreach ($products as $p): ?>
                                    <tr>
                                        <td><img src="<?= e($p['image_url']) ?>" style="width:48px;height:48px;object-fit:cover;border-radius:8px;"></td>
                                        <td><?= e($p['name']) ?></td>
                                        <td><?= e($p['category_name']) ?></td>
                                        <td><?= formatMoney($p['price']) ?> / <?= e($p['unit']) ?></td>
                                        <td><?= (int)$p['stock_quantity'] ?><?= $p['stock_quantity'] < 5 ? ' <span class="text-danger">⚠</span>' : '' ?></td>
                                        <td><span class="status-pill <?= $p['is_active'] ? 'status-delivered' : 'status-cancelled' ?>"><?= $p['is_active'] ? 'Active' : 'Hidden' ?></span></td>
                                        <td class="text-end">
                                            <a href="/farmer/product-form.php?id=<?= (int)$p['id'] ?>" class="btn btn-sm btn-agri-outline">Edit</a>
                                            <form method="POST" action="/farmer/delete-product.php" class="d-inline" onsubmit="return confirm('Delete this product?');">
                                                <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
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
