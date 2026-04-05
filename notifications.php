<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_login();
$userId = current_user_id();
$notes = $mysqli->query("SELECT * FROM notifications WHERE user_id=$userId ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);
$pageTitle = 'Notifications';
require_once __DIR__ . '/includes/header.php';
?>
<h1>Notifications</h1>
<ul class="list-group">
<?php foreach($notes as $n): ?><li class="list-group-item"><strong><?= e($n['type']) ?></strong> - <?= e($n['message']) ?> <span class="text-muted small float-end"><?= e($n['created_at']) ?></span></li><?php endforeach; ?>
</ul>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
