<?php
require_once __DIR__ . '/config/bootstrap.php';

$productModel = new Product($pdo);
$categoryModel = new Category($pdo);
$farmerModel = new FarmerProfile($pdo);

$filters = [
    'q'        => trim($_GET['q'] ?? ''),
    'category' => trim($_GET['category'] ?? ''),
    'district' => trim($_GET['district'] ?? ''),
    'sort'     => trim($_GET['sort'] ?? ''),
];

$products = $productModel->search($filters);
$categories = $categoryModel->all();
$districts = $farmerModel->distinctDistricts();

$title = 'Browse Products';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/navbar.php';
?>

<section class="agri-section pt-4">
    <div class="container">
        <h1 class="agri-section-title">Browse Products</h1>
        <p class="agri-section-sub">Search fresh produce from farmers across Rwanda.</p>

        <form method="GET" action="/products.php" class="row g-2 mb-4 agri-form-card">
            <div class="col-md-4">
                <label class="form-label">Search</label>
                <input type="text" name="q" class="form-control" placeholder="e.g. tomatoes, coffee, honey..." value="<?= e($filters['q']) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Category</label>
                <select name="category" class="form-select">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= e($cat['slug']) ?>" <?= $filters['category'] === $cat['slug'] ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">District</label>
                <select name="district" class="form-select">
                    <option value="">All Districts</option>
                    <?php foreach ($districts as $d): ?>
                        <option value="<?= e($d) ?>" <?= $filters['district'] === $d ? 'selected' : '' ?>><?= e($d) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Sort By</label>
                <select name="sort" class="form-select">
                    <option value="">Newest</option>
                    <option value="price_asc" <?= $filters['sort'] === 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
                    <option value="price_desc" <?= $filters['sort'] === 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
                </select>
            </div>
            <div class="col-12 mt-3">
                <button type="submit" class="btn btn-agri-primary">Apply Filters</button>
                <a href="/products.php" class="btn btn-agri-outline">Reset</a>
            </div>
        </form>

        <p class="text-muted mb-3"><?= count($products) ?> product<?= count($products) === 1 ? '' : 's' ?> found</p>

        <div class="row g-4">
            <?php foreach ($products as $product): ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <a href="/product.php?slug=<?= e($product['slug']) ?>" class="text-decoration-none text-reset">
                        <div class="agri-card">
                            <div class="card-img-wrap">
                                <img src="<?= e($product['image_url']) ?>" alt="<?= e($product['name']) ?>">
                            </div>
                            <div class="card-body">
                                <span class="badge-category"><?= e($product['category_name']) ?></span>
                                <h5 class="mt-2 mb-1" style="font-size:1.05rem;"><?= e($product['name']) ?></h5>
                                <p class="agri-price mb-1"><?= formatMoney($product['price']) ?> <span class="agri-unit">/ <?= e($product['unit']) ?></span></p>
                                <span class="agri-farmer-tag">🌾 <?= e($product['farm_name']) ?>, <?= e($product['district']) ?></span>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (empty($products)): ?>
            <div class="agri-empty-state">
                <div class="icon">🔍</div>
                <p>No products match your search. Try a different keyword or clear your filters.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
