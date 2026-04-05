<?php
require_once __DIR__ . '/includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        flash('error', 'Invalid security token.');
    } else {
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        if (login_user($email, $password)) {
            $role = $_SESSION['user']['role'];
            if ($role === 'admin') header('Location: /admin/dashboard.php');
            elseif ($role === 'doctor') header('Location: /doctor/dashboard.php');
            else header('Location: /patient/my_appointments.php');
            exit;
        }
        flash('error', 'Invalid credentials.');
    }
}

require_once __DIR__ . '/includes/header.php';
?>
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm"><div class="card-body">
            <h1 class="h4 mb-3">Login</h1>
            <form method="post">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <div class="mb-3"><label class="form-label">Email</label><input class="form-control" type="email" name="email" required></div>
                <div class="mb-3"><label class="form-label">Password</label><input class="form-control" type="password" name="password" required></div>
                <button class="btn btn-primary w-100" type="submit">Login</button>
            </form>
        </div></div>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
