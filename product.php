<?php
require_once __DIR__ . '/config/bootstrap.php';

$productModel = new Product($pdo);
$slug = trim($_GET['slug'] ?? '');
$product = $slug ? $productModel->findBySlug($slug) : null;

if (!$product) {
    setFlash('error', 'Product not found.');
    redirectTo('/products.php');
}

$related = $productModel->related((int)$product['category_id'], (int)$product['id'], 4);

// Reviews
$reviewStmt = $pdo->prepare(
    'SELECT r.*, u.full_name FROM reviews r JOIN users u ON u.id = r.user_id WHERE r.product_id = :pid ORDER BY r.created_at DESC'
);
$reviewStmt->execute(['pid' => $product['id']]);
$reviews = $reviewStmt->fetchAll();
$avgRating = $reviews ? round(array_sum(array_column($reviews, 'rating')) / count($reviews), 1) : null;

$title = $product['name'];
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/navbar.php';
?>

<section class="agri-section pt-4">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="/products.php">Products</a></li>
                <li class="breadcrumb-item"><a href="/products.php?category=<?= e($product['category_slug']) ?>"><?= e($product['category_name']) ?></a></li>
                <li class="breadcrumb-item active"><?= e($product['name']) ?></li>
            </ol>
        </nav>

        <div class="row g-5">
            <div class="col-md-6">
                <div class="agri-card p-0">
                    <div class="card-img-wrap" style="aspect-ratio: 1/1;">
                        <img src="<?= e($product['image_url']) ?>" alt="<?= e($product['name']) ?>">
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <span class="badge-category"><?= e($product['category_name']) ?></span>
                <h1 class="mt-2 mb-1"><?= e($product['name']) ?></h1>
                <?php if ($avgRating): ?>
                    <p class="text-warning mb-2">
                        <?= str_repeat('★', round($avgRating)) . str_repeat('☆', 5 - round($avgRating)) ?>
                        <span class="text-muted small">(<?= $avgRating ?> / 5, <?= count($reviews) ?> review<?= count($reviews) === 1 ? '' : 's' ?>)</span>
                    </p>
                <?php endif; ?>
                <p class="agri-price fs-3"><?= formatMoney($product['price']) ?> <span class="agri-unit">/ <?= e($product['unit']) ?></span></p>

                <p class="text-muted"><?= nl2br(e($product['description'] ?: 'No description provided by the farmer.')) ?></p>

                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="agri-farmer-tag">🌾 Sold by <strong><?= e($product['farm_name']) ?></strong> &mdash; <?= e($product['district']) ?></span>
                </div>

                <?php if ($product['stock_quantity'] > 0): ?>
                    <p class="text-success fw-semibold">✔ In stock (<?= (int)$product['stock_quantity'] ?> <?= e($product['unit']) ?> available)</p>
                <?php else: ?>
                    <p class="text-danger fw-semibold">Out of stock</p>
                <?php endif; ?>

                <?php $user = currentUser(); ?>
                <?php if ($user && $user['role'] === 'consumer'): ?>
                    <?php if ($product['stock_quantity'] > 0): ?>
                        <form method="POST" action="/add-to-cart.php" class="d-flex align-items-center gap-3 mt-3">
                            <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
                            <div class="d-flex align-items-center border rounded-pill overflow-hidden" data-qty-input>
                                <button type="button" class="btn btn-light px-3" data-qty-minus>&minus;</button>
                                <input type="number" name="quantity" value="1" min="1" max="<?= (int)$product['stock_quantity'] ?>" class="form-control border-0 text-center" style="width:60px;">
                                <button type="button" class="btn btn-light px-3" data-qty-plus>+</button>
                            </div>
                            <button type="submit" class="btn btn-agri-primary btn-lg">Add to Cart</button>
                        </form>
                    <?php endif; ?>
                <?php elseif (!$user): ?>
                    <a href="/auth/login.php" class="btn btn-agri-primary btn-lg mt-3">Log In to Purchase</a>
                <?php else: ?>
                    <p class="text-muted small mt-3">Farmer and admin accounts cannot place orders.</p>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($reviews)): ?>
        <div class="mt-5" style="max-width:700px;">
            <h4>Customer Reviews</h4>
            <?php foreach ($reviews as $r): ?>
                <div class="border-bottom py-3">
                    <strong><?= e($r['full_name']) ?></strong>
                    <span class="text-warning"><?= str_repeat('★', (int)$r['rating']) . str_repeat('☆', 5 - (int)$r['rating']) ?></span>
                    <p class="mb-0 text-muted"><?= e($r['comment']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($related)): ?>
        <div class="mt-5">
            <h4 class="mb-3">You might also like</h4>
            <div class="row g-4">
                <?php foreach ($related as $rp): ?>
                    <div class="col-6 col-md-3">
                        <a href="/product.php?slug=<?= e($rp['slug']) ?>" class="text-decoration-none text-reset">
                            <div class="agri-card">
                                <div class="card-img-wrap"><img src="<?= e($rp['image_url']) ?>" alt="<?= e($rp['name']) ?>"></div>
                                <div class="card-body">
                                    <h6 class="mb-1"><?= e($rp['name']) ?></h6>
                                    <p class="agri-price mb-0"><?= formatMoney($rp['price']) ?></p>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
