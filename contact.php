<?php
require_once __DIR__ . '/includes/bootstrap.php';
verify_csrf();
$status = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $message = trim($_POST['message'] ?? '');
    if ($name && $email && $message) {
        $stmt = $mysqli->prepare('INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)');
        $stmt->bind_param('sss', $name, $email, $message);
        $stmt->execute();
        $status = 'Message sent successfully.';
    } else {
        $status = 'Please provide valid form details.';
    }
}
$pageTitle = 'Contact Us';
require_once __DIR__ . '/includes/header.php';
?>
<h1>Contact Us</h1>
<p><strong>Phone:</strong> +1 800 123 4567<br><strong>Email:</strong> support@daaweeye.com<br><strong>Location:</strong> Mogadishu Telehealth Center</p>
<?php if ($status): ?><div class="alert alert-info"><?= e($status) ?></div><?php endif; ?>
<form method="post" class="card p-3 shadow-sm">
<input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
<div class="mb-3"><label class="form-label">Name</label><input class="form-control" name="name" required></div>
<div class="mb-3"><label class="form-label">Email</label><input type="email" class="form-control" name="email" required></div>
<div class="mb-3"><label class="form-label">Message</label><textarea class="form-control" rows="4" name="message" required></textarea></div>
<button class="btn btn-primary">Send Message</button>
</form>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
