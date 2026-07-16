<?php
require_once __DIR__ . '/../config/bootstrap.php';

if (isLoggedIn()) {
    redirectTo('/index.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $role = ($_POST['role'] ?? 'consumer') === 'farmer' ? 'farmer' : 'consumer';
    $farmName = trim($_POST['farm_name'] ?? '');
    $district = trim($_POST['district'] ?? '');
    $sector = trim($_POST['sector'] ?? '');

    if ($fullName === '') $errors[] = 'Full name is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'A valid email is required.';
    if (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters.';
    if ($password !== $confirmPassword) $errors[] = 'Passwords do not match.';
    if ($phone === '') $errors[] = 'Phone number is required.';
    if ($role === 'farmer' && ($farmName === '' || $district === '')) {
        $errors[] = 'Farm name and district are required for farmer accounts.';
    }

    $userModel = new User($pdo);
    if (empty($errors) && $userModel->findByEmail($email)) {
        $errors[] = 'An account with that email already exists.';
    }

    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            $userId = $userModel->create([
                'full_name' => $fullName,
                'email' => $email,
                'password' => $password,
                'phone' => $phone,
                'address' => $address,
                'role' => $role,
            ]);

            $cartModel = new Cart($pdo);
            $cartModel->getOrCreateForUser($userId);

            if ($role === 'farmer') {
                $farmerModel = new FarmerProfile($pdo);
                $farmerModel->create($userId, [
                    'farm_name' => $farmName,
                    'district' => $district,
                    'sector' => $sector,
                ]);
            }

            $pdo->commit();

            setFlash('success', 'Account created! Please log in.');
            redirectTo('/auth/login.php');
        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = 'Registration failed: ' . $e->getMessage();
        }
    }
}

$title = 'Create Account';
require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/navbar.php';
?>

<section class="agri-section">
    <div class="container" style="max-width: 640px;">
        <div class="agri-form-card">
            <h2 class="mb-1">Create Your Account</h2>
            <p class="text-muted mb-4">Join AgriLink Rwanda as a consumer or as a farmer selling produce.</p>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="/auth/register.php" data-validate novalidate>
                <div class="mb-3">
                    <label class="form-label">I am registering as a...</label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="role" id="roleConsumer" value="consumer" <?= ($_POST['role'] ?? 'consumer') === 'consumer' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="roleConsumer">Consumer (I want to buy)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="role" id="roleFarmer" value="farmer" <?= ($_POST['role'] ?? '') === 'farmer' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="roleFarmer">Farmer (I want to sell)</label>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="full_name" class="form-control" required value="<?= oldInput('full_name') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required value="<?= oldInput('email') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" required value="<?= oldInput('phone') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Address (optional)</label>
                        <input type="text" name="address" class="form-control" value="<?= oldInput('address') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required minlength="6">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="confirm_password" class="form-control" required minlength="6">
                    </div>
                </div>

                <div id="farmerFields" class="row g-3 mt-1" style="display:none;">
                    <hr class="mt-3">
                    <p class="fw-semibold mb-1">Farm Details</p>
                    <div class="col-md-6">
                        <label class="form-label">Farm Name</label>
                        <input type="text" name="farm_name" class="form-control" value="<?= oldInput('farm_name') ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">District</label>
                        <input type="text" name="district" class="form-control" value="<?= oldInput('district') ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Sector (optional)</label>
                        <input type="text" name="sector" class="form-control" value="<?= oldInput('sector') ?>">
                    </div>
                </div>

                <button type="submit" class="btn btn-agri-primary btn-lg w-100 mt-4">Create Account</button>
            </form>
            <p class="text-center mt-3 mb-0 text-muted">Already have an account? <a href="/auth/login.php">Log in</a></p>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const farmerFields = document.getElementById('farmerFields');
    const radios = document.querySelectorAll('input[name="role"]');
    function toggle() {
        const isFarmer = document.getElementById('roleFarmer').checked;
        farmerFields.style.display = isFarmer ? 'flex' : 'none';
    }
    radios.forEach(r => r.addEventListener('change', toggle));
    toggle();
});
</script>

<?php require __DIR__ . '/../includes/footer.php'; ?>
