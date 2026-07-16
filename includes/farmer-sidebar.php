<?php $current = basename($_SERVER['PHP_SELF']); ?>
<div class="agri-sidebar">
    <p class="text-white-50 text-uppercase small fw-bold mb-2" style="letter-spacing:0.06em;">Farmer Menu</p>
    <a href="/farmer/dashboard.php" class="<?= $current === 'dashboard.php' ? 'active' : '' ?>">📊 Dashboard</a>
    <a href="/farmer/products.php" class="<?= $current === 'products.php' ? 'active' : '' ?>">🌾 My Products</a>
    <a href="/farmer/product-form.php" class="<?= $current === 'product-form.php' ? 'active' : '' ?>">➕ Add Product</a>
    <a href="/farmer/orders.php" class="<?= $current === 'orders.php' ? 'active' : '' ?>">📦 My Orders</a>
</div>
