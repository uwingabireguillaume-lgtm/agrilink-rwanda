<?php
require_once __DIR__ . '/../config/bootstrap.php';

if (isLoggedIn()) {
    redirectTo('/index.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $userModel = new User($pdo);
    $user = $userModel->findByEmail($email);

    if (!$user || !$userModel->verifyPassword($password, $user['password'])) {
        $errors[] = 'Invalid email or password.';
    } elseif (!$user['is_active']) {
        $errors[] = 'This account has been deactivated.';
    }

    if (empty($errors)) {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'full_name' => $user['full_name'],
            'email' => $user['email'],
            'role' => $user['role'],
        ];

        setFlash('success', 'Welcome back, ' . $user['full_name'] . '!');

        if ($user['role'] === 'farmer') redirectTo('/farmer/dashboard.php');
        if ($user['role'] === 'admin') redirectTo('/admin/dashboard.php');
        redirectTo('/products.php');
    }
}

$title = 'Log In';
require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/navbar.php';
?>

<section class="agri-section">
    <div class="container" style="max-width: 480px;">
        <div class="agri-form-card">
            <h2 class="mb-1">Welcome Back</h2>
            <p class="text-muted mb-4">Log in to your AgriLink Rwanda account.</p>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="/auth/login.php" data-validate novalidate>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required value="<?= oldInput('email') ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-agri-primary btn-lg w-100">Log In</button>
            </form>
            <p class="text-center mt-3 mb-0 text-muted">Don't have an account? <a href="/auth/register.php">Sign up</a></p>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
