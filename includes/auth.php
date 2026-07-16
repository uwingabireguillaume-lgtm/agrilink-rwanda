<?php
/**
 * Session-based authentication helpers.
 */

function startSecureSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function isLoggedIn() {
    startSecureSession();
    return isset($_SESSION['user']);
}

function currentUser() {
    startSecureSession();
    return $_SESSION['user'] ?? null;
}

function requireLogin() {
    if (!isLoggedIn()) {
        setFlash('error', 'Please log in to continue.');
        redirectTo('/auth/login.php');
    }
}

function requireRole($role) {
    requireLogin();
    $user = currentUser();
    if ($user['role'] !== $role) {
        setFlash('error', 'You do not have access to that page.');
        redirectTo('/index.php');
    }
}

function cartItemCount($pdo) {
    $user = currentUser();
    if (!$user) return 0;
    $stmt = $pdo->prepare(
        'SELECT COALESCE(SUM(ci.quantity), 0) AS total
         FROM cart_items ci
         JOIN carts c ON c.id = ci.cart_id
         WHERE c.user_id = :uid'
    );
    $stmt->execute(['uid' => $user['id']]);
    return (int)$stmt->fetch()['total'];
}
