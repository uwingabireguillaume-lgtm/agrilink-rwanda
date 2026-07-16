<?php $user = currentUser(); ?>
<nav class="navbar navbar-expand-lg navbar-dark sticky-top agri-navbar">
    <div class="container">
        <a class="navbar-brand agri-brand" href="/index.php">
            <span class="agri-logo">🌿</span> AgriLink <span class="agri-brand-accent">Rwanda</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="/index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="/products.php">Browse Products</a></li>
                <li class="nav-item"><a class="nav-link" href="/about.php">About</a></li>
                <?php if ($user && $user['role'] === 'farmer'): ?>
                    <li class="nav-item"><a class="nav-link" href="/farmer/dashboard.php">My Dashboard</a></li>
                <?php endif; ?>
                <?php if ($user && $user['role'] === 'admin'): ?>
                    <li class="nav-item"><a class="nav-link" href="/admin/dashboard.php">Admin</a></li>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav align-items-lg-center gap-lg-2">
                <?php if ($user): ?>
                    <?php if ($user['role'] === 'consumer'): ?>
                        <li class="nav-item">
                            <a class="nav-link position-relative" href="/cart.php">
                                Cart
                                <span class="badge rounded-pill agri-cart-badge"><?= (int)cartItemCount($pdo) ?></span>
                            </a>
                        </li>
                        <li class="nav-item"><a class="nav-link" href="/my-orders.php">My Orders</a></li>
                    <?php endif; ?>
                    <li class="nav-item"><span class="nav-link disabled text-white-50">Hi, <?= e($user['full_name']) ?></span></li>
                    <li class="nav-item"><a class="btn btn-sm agri-btn-outline" href="/auth/logout.php">Log Out</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="btn btn-sm agri-btn-outline me-lg-2" href="/auth/login.php">Log In</a></li>
                    <li class="nav-item"><a class="btn btn-sm agri-btn-solid" href="/auth/register.php">Sign Up</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<?php $flashes = getFlashes(); if (!empty($flashes)): ?>
    <div class="container mt-3">
        <?php foreach ($flashes as $flash): ?>
            <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : 'success' ?> alert-dismissible fade show" role="alert">
                <?= e($flash['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
