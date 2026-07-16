<?php
/**
 * Bootstrap: loaded at the top of every page.
 * Starts the session, loads config, helpers, and model classes.
 */

define('BASE_URL', rtrim(getenv('APP_BASE_URL') !== false ? getenv('APP_BASE_URL') : '/agrilink-php', '/'));

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

startSecureSession();

require_once __DIR__ . '/database.php'; // provides $pdo

require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/FarmerProfile.php';
require_once __DIR__ . '/../classes/Category.php';
require_once __DIR__ . '/../classes/Product.php';
require_once __DIR__ . '/../classes/Cart.php';
require_once __DIR__ . '/../classes/Order.php';
