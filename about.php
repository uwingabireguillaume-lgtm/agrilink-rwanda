<?php
require_once __DIR__ . '/config/bootstrap.php';
$title = 'About';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/navbar.php';
?>
<section class="agri-section">
    <div class="container" style="max-width: 780px;">
        <h1 class="agri-section-title">About AgriLink Rwanda</h1>
        <p class="text-muted mb-4">A smart digital marketplace connecting farmers and consumers, built for EWA408510 &mdash; E-Commerce and Web Application.</p>

        <p>AgriLink Rwanda exists to close the gap between the farmer who grows the food and the family who eats it. Too often, produce passes through several middlemen before it reaches a consumer &mdash; raising prices for buyers while farmers capture only a fraction of the final sale price.</p>

        <p>AgriLink is a <strong>multi-vendor marketplace</strong>: any approved farmer can register, create a storefront, and list their own products with their own prices and stock levels. Consumers browse the full catalog across every farmer, add items from multiple farms to one cart, and check out once &mdash; behind the scenes, the order is automatically split so each farmer only sees and manages their own portion of the sale.</p>

        <h4 class="mt-5">How it works</h4>
        <ol>
            <li><strong>Farmers register</strong> and set up a simple storefront profile (farm name, district, bio).</li>
            <li><strong>Farmers list products</strong> with price, unit, quantity in stock, and a description.</li>
            <li><strong>Consumers browse and search</strong> by category, district, or keyword.</li>
            <li><strong>Consumers check out</strong> once, even when their cart includes produce from several different farmers.</li>
            <li><strong>Each farmer manages their own orders</strong> from a personal dashboard, updating fulfillment status independently.</li>
        </ol>

        <h4 class="mt-5">Built with</h4>
        <p>PHP &amp; MySQL on the backend, Bootstrap 5 on the frontend, containerized with Docker, tested and deployed automatically via GitHub Actions CI/CD, and hosted on Render.</p>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
