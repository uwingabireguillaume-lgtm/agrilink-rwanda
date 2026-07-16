<?php
/**
 * Database connection (PDO + MySQL)
 * Reads credentials from environment variables when available (Docker/Render),
 * falling back to local XAMPP defaults for local development.
 */

function getEnvOrDefault($key, $default) {
    $value = getenv($key);
    return ($value !== false && $value !== '') ? $value : $default;
}

$DB_HOST = getEnvOrDefault('DB_HOST', 'localhost');
$DB_PORT = getEnvOrDefault('DB_PORT', '3306');
$DB_NAME = getEnvOrDefault('DB_NAME', 'agrilink_rwanda');
$DB_USER = getEnvOrDefault('DB_USER', 'root');
$DB_PASS = getEnvOrDefault('DB_PASSWORD', '');

$dsn = "mysql:host={$DB_HOST};port={$DB_PORT};dbname={$DB_NAME};charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);
} catch (PDOException $e) {
    http_response_code(500);
    die('Database connection failed. Please check your configuration. (' . $e->getMessage() . ')');
}
