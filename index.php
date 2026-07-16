<?php
require_once __DIR__ . '/config/bootstrap.php';

$productModel = new Product($pdo);
$categoryModel = new Category($pdo);
$farmerModel = new FarmerProfile($pdo);

$featuredProducts = $productModel->featured(8);
$categories = array_slice($categoryModel->all(), 0, 6);
$farmerCount = $farmerModel->count();
$productCount = $productModel->count();

$title = 'Home';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/navbar.php';
?>

<section class="agri-hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <span class="eyebrow">Land of a thousand hills, one marketplace</span>
                <h1 class="mt-3 mb-3">Fresh produce, straight from Rwanda's farmers to your table.</h1>
                <p class="lead">AgriLink Rwanda connects local farmers directly with consumers &mdash; no middlemen, fairer prices, and produce that's still got the morning dew on it.</p>
                <div class="d-flex flex-wrap gap-3 mt-4">
                    <a href="/products.php" class="btn btn-agri-gold btn-lg">Browse the Marketplace</a>
                    <a href="/auth/register.php" class="btn btn-agri-outline btn-lg" style="border-color:rgba(255,255,255,0.6); color:#fff;">Sell as a Farmer</a>
                </div>
                <div class="agri-hero-stats">
                    <div>
                        <span class="stat-num"><?= (int)$farmerCount ?>+</span>
                        <span class="stat-label">Registered Farmers</span>
                    </div>
                    <div>
                        <span class="stat-num"><?= (int)$productCount ?>+</span>
                        <span class="stat-label">Products Listed</span>
                    </div>
                    <div>
                        <span class="stat-num">30</span>
                        <span class="stat-label">Districts Reachable</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="agri-terraces">
    <svg viewBox="0 0 1440 64" preserveAspectRatio="none">
        <path d="M0,64 L0,40 Q180,10 360,32 T720,28 T1080,36 T1440,20 L1440,64 Z" fill="#7FC08A" opacity="0.55"/>
        <path d="M0,64 L0,50 Q220,26 440,46 T880,42 T1440,38 L1440,64 Z" fill="#4C9A5B" opacity="0.75"/>
        <path d="M0,64 L0,58 Q260,44 520,58 T1040,54 T1440,52 L1440,64 Z" fill="#FAF6EC"/>
    </svg>
</div>

<section class="agri-section">
    <div class="container">
        <h2 class="agri-section-title">Shop by Category</h2>
        <p class="agri-section-sub">Everything comes straight from a registered local farm.</p>
        <div class="row g-3">
            <?php foreach ($categories as $cat): ?>
                <div class="col-6 col-md-4 col-lg-2">
                    <a class="agri-category-pill" href="/products.php?category=<?= e($cat['slug']) ?>"><?= e($cat['name']) ?></a>
                </div>
            <?php endforeach; ?>
            <?php if (empty($categories)): ?>
                <p class="text-muted">Categories will appear here once seeded.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="agri-section" style="background: var(--cream-deep);">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end flex-wrap gap-2">
            <div>
                <h2 class="agri-section-title mb-1">Freshly Listed</h2>
                <p class="agri-section-sub mb-0">New produce from farmers across Rwanda.</p>
            </div>
            <a href="/products.php" class="btn btn-agri-outline">View All Products</a>
        </div>
        <div class="row g-4 mt-2">
            <?php foreach ($featuredProducts as $product): ?>
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
            <?php if (empty($featuredProducts)): ?>
                <div class="agri-empty-state">
                    <div class="icon">🌱</div>
                    <p>No products listed yet. Be the first farmer to add one!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="agri-section">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-md-4">
                <div class="p-4">
                    <div style="font-size:2.2rem;">🧑🏾‍🌾</div>
                    <h5 class="mt-3">Farmers List Produce</h5>
                    <p class="text-muted">Verified local farmers create a storefront and list what's ready to sell, at the price they set.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4">
                    <div style="font-size:2.2rem;">🛒</div>
                    <h5 class="mt-3">Consumers Shop Freely</h5>
                    <p class="text-muted">Browse by category or district, add to cart from multiple farmers, and check out once.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4">
                    <div style="font-size:2.2rem;">📦</div>
                    <h5 class="mt-3">Orders Split Automatically</h5>
                    <p class="text-muted">Each farmer sees and fulfills only their part of the order &mdash; simple for everyone.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
