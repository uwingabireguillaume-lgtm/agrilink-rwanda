<?php
require_once __DIR__ . '/../config/bootstrap.php';
requireRole('farmer');

$user = currentUser();
$farmerModel = new FarmerProfile($pdo);
$farmer = $farmerModel->findByUserId((int)$user['id']);

$categoryModel = new Category($pdo);
$categories = $categoryModel->all();

$productModel = new Product($pdo);
$editing = false;
$product = null;

if (!empty($_GET['id'])) {
    $product = $productModel->findByIdForFarmer((int)$_GET['id'], (int)$farmer['id']);
    if (!$product) {
        setFlash('error', 'Product not found.');
        redirectTo('/farmer/products.php');
    }
    $editing = true;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'name' => trim($_POST['name'] ?? ''),
        'category_id' => (int)($_POST['category_id'] ?? 0),
        'description' => trim($_POST['description'] ?? ''),
        'price' => (float)($_POST['price'] ?? 0),
        'unit' => trim($_POST['unit'] ?? 'kg'),
        'stock_quantity' => (int)($_POST['stock_quantity'] ?? 0),
        'image_url' => trim($_POST['image_url'] ?? ''),
        'is_active' => isset($_POST['is_active']) ? 1 : 0,
    ];

    if ($data['name'] === '') $errors[] = 'Product name is required.';
    if ($data['category_id'] <= 0) $errors[] = 'Please choose a category.';
    if ($data['price'] <= 0) $errors[] = 'Price must be greater than zero.';
    if ($data['stock_quantity'] < 0) $errors[] = 'Stock quantity cannot be negative.';

    if (empty($errors)) {
        if ($editing) {
            $productModel->update((int)$product['id'], (int)$farmer['id'], $data);
            setFlash('success', 'Product updated.');
        } else {
            $data['farmer_id'] = $farmer['id'];
            $productModel->create($data);
            setFlash('success', 'Product added.');
        }
        redirectTo('/farmer/products.php');
    }
}

$title = $editing ? 'Edit Product' : 'Add Product';
require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/navbar.php';
?>

<section class="agri-section pt-4">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-3"><?php require __DIR__ . '/../includes/farmer-sidebar.php'; ?></div>
            <div class="col-lg-9">
                <div class="agri-form-card" style="max-width: 700px;">
                    <h4 class="mb-3"><?= $editing ? 'Edit Product' : 'Add a New Product' ?></h4>

                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul></div>
                    <?php endif; ?>

                    <form method="POST" data-validate novalidate>
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label">Product Name</label>
                                <input type="text" name="name" class="form-control" required value="<?= e($product['name'] ?? oldInput('name')) ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Category</label>
                                <select name="category_id" class="form-select" required>
                                    <option value="">Choose...</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= (int)$cat['id'] ?>" <?= (($product['category_id'] ?? null) == $cat['id']) ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="3"><?= e($product['description'] ?? '') ?></textarea>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Price (RWF)</label>
                                <input type="number" step="0.01" min="0" name="price" class="form-control" required value="<?= e($product['price'] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Unit</label>
                                <select name="unit" class="form-select">
                                    <?php foreach (['kg','g','litre','piece','bunch','crate','sack'] as $u): ?>
                                        <option value="<?= $u ?>" <?= ($product['unit'] ?? 'kg') === $u ? 'selected' : '' ?>><?= $u ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Stock Quantity</label>
                                <input type="number" min="0" name="stock_quantity" class="form-control" required value="<?= e($product['stock_quantity'] ?? 0) ?>">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Image URL (optional)</label>
                                <input type="text" name="image_url" class="form-control" placeholder="/assets/images/product-placeholder.svg" value="<?= e($product['image_url'] ?? '') ?>">
                            </div>
                            <?php if ($editing): ?>
                            <div class="col-12 form-check">
                                <input type="checkbox" class="form-check-input" id="isActive" name="is_active" <?= ($product['is_active'] ?? 1) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="isActive">Visible to consumers</label>
                            </div>
                            <?php endif; ?>
                        </div>
                        <button type="submit" class="btn btn-agri-primary btn-lg mt-4"><?= $editing ? 'Save Changes' : 'Add Product' ?></button>
                        <a href="/farmer/products.php" class="btn btn-link mt-4">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
