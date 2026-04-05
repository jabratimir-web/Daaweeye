<?php
require_once __DIR__ . '/includes/bootstrap.php';
verify_csrf();
$msg = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['full_name'] ?? '');
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';
    $role = ($_POST['role'] ?? 'patient') === 'doctor' ? 'doctor' : 'patient';
    if ($name && $email && strlen($password) >= 8) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $mysqli->prepare('INSERT INTO users (full_name, email, password_hash, role) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('ssss', $name, $email, $hash, $role);
        if ($stmt->execute()) {
            $msg = 'Registration successful. Please login.';
        } else {
            $msg = 'Email already exists.';
        }
    } else {
        $msg = 'Invalid input. Password must be at least 8 characters.';
    }
}
$pageTitle = 'Register';
require_once __DIR__ . '/includes/header.php';
?>
<h1>Register</h1>
<?php if ($msg): ?><div class="alert alert-info"><?= e($msg) ?></div><?php endif; ?>
<form method="post" class="card p-3">
<input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
<div class="mb-3"><label class="form-label">Full Name</label><input class="form-control" name="full_name" required></div>
<div class="mb-3"><label class="form-label">Email</label><input type="email" class="form-control" name="email" required></div>
<div class="mb-3"><label class="form-label">Password</label><input type="password" class="form-control" name="password" required></div>
<div class="mb-3"><label class="form-label">Account Type</label><select class="form-select" name="role"><option value="patient">Patient</option><option value="doctor">Doctor</option></select></div>
<button class="btn btn-primary">Create Account</button>
</form>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
