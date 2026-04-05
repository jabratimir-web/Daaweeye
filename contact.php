<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        flash('error', 'Invalid security token.');
    } else {
        $name = trim((string) ($_POST['name'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $message = trim((string) ($_POST['message'] ?? ''));
        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $message === '') {
            flash('error', 'Please complete all fields correctly.');
        } else {
            $stmt = $pdo->prepare('INSERT INTO contact_messages (name, email, message) VALUES (:name, :email, :message)');
            $stmt->execute(['name' => $name, 'email' => $email, 'message' => $message]);
            flash('success', 'Message sent successfully.');
            header('Location: /contact.php');
            exit;
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>
<div class="row g-4">
    <div class="col-md-5">
        <div class="card shadow-sm"><div class="card-body">
            <h1 class="h4">Contact Us</h1>
            <p><strong>Phone:</strong> +1 (555) 100-2000</p>
            <p><strong>Email:</strong> support@daaweeye.com</p>
            <p><strong>Location:</strong> Mogadishu, Somalia</p>
        </div></div>
    </div>
    <div class="col-md-7">
        <div class="card shadow-sm"><div class="card-body">
            <form method="post">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <div class="mb-3"><label class="form-label">Name</label><input class="form-control" name="name" required></div>
                <div class="mb-3"><label class="form-label">Email</label><input class="form-control" name="email" type="email" required></div>
                <div class="mb-3"><label class="form-label">Message</label><textarea class="form-control" name="message" rows="4" required></textarea></div>
                <button class="btn btn-primary" type="submit">Send Message</button>
            </form>
        </div></div>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
