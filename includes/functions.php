<?php
/**
 * General-purpose helper functions used across AgriLink Rwanda.
 */

function slugify($text) {
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    $text = trim($text, '-');
    return $text !== '' ? $text : 'item';
}

function formatMoney($amount) {
    return number_format((float)$amount, 2) . ' RWF';
}

function e($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function redirectTo($path) {
    header('Location: ' . $path);
    exit;
}

function setFlash($type, $message) {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function getFlashes() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $flashes = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $flashes;
}

function generateOrderNumber() {
    return 'AGL-' . strtoupper(base_convert((string)time(), 10, 36)) . '-' . rand(100, 999);
}

function oldInput($key, $default = '') {
    return isset($_POST[$key]) ? e($_POST[$key]) : $default;
}
