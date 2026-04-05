<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        flash('error', 'Invalid security token.');
    } else {
        $fullName = trim((string) ($_POST['full_name'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        if ($fullName === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6) {
            flash('error', 'Please provide valid details.');
        } else {
            $check = $pdo->prepare('SELECT id FROM users WHERE email = :email');
            $check->execute(['email' => $email]);
            if ($check->fetch()) {
                flash('error', 'Email already exists.');
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password, role) VALUES (:full_name, :email, :password, 'patient')");
                $stmt->execute(['full_name' => $fullName, 'email' => $email, 'password' => $hash]);
                flash('success', 'Registration complete. Please login.');
                header('Location: /login.php');
                exit;
            }
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>
<div class="row justify-content-center"><div class="col-md-6">
    <div class="card shadow-sm"><div class="card-body">
        <h1 class="h4">Register as Patient</h1>
        <form method="post">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <div class="mb-3"><label class="form-label">Full Name</label><input class="form-control" name="full_name" required></div>
            <div class="mb-3"><label class="form-label">Email</label><input class="form-control" name="email" type="email" required></div>
            <div class="mb-3"><label class="form-label">Password</label><input class="form-control" name="password" type="password" minlength="6" required></div>
            <button class="btn btn-primary" type="submit">Create Account</button>
        </form>
    </div></div>
</div></div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
