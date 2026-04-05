<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_role(['doctor']);

$doctor = $pdo->prepare('SELECT id FROM doctors WHERE user_id = :user_id');
$doctor->execute(['user_id' => $_SESSION['user']['id']]);
$doctorId = (int) ($doctor->fetch()['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf()) {
    $day = trim((string) ($_POST['day_name'] ?? ''));
    $start = (string) ($_POST['start_time'] ?? '');
    $end = (string) ($_POST['end_time'] ?? '');
    $label = trim((string) ($_POST['slot_label'] ?? 'General'));
    $sortOrder = (int) ($_POST['sort_order'] ?? 99);
    $stmt = $pdo->prepare('INSERT INTO doctor_schedules (doctor_id, day_name, start_time, end_time, slot_label, sort_order) VALUES (:doctor_id,:day_name,:start_time,:end_time,:slot_label,:sort_order)');
    $stmt->execute(['doctor_id' => $doctorId, 'day_name' => $day, 'start_time' => $start, 'end_time' => $end, 'slot_label' => $label, 'sort_order' => $sortOrder]);
    flash('success', 'Schedule slot added.');
    header('Location: /doctor/schedule.php');
    exit;
}

$rows = $pdo->prepare('SELECT * FROM doctor_schedules WHERE doctor_id = :doctor_id ORDER BY sort_order, start_time');
$rows->execute(['doctor_id' => $doctorId]);
$schedules = $rows->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>
<h1 class="h4">Schedule Management</h1>
<form class="card p-3 mb-3" method="post">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <div class="row g-2">
        <div class="col-md-3"><input class="form-control" name="day_name" placeholder="Day e.g Monday" required></div>
        <div class="col-md-2"><input class="form-control" name="start_time" type="time" required></div>
        <div class="col-md-2"><input class="form-control" name="end_time" type="time" required></div>
        <div class="col-md-3"><input class="form-control" name="slot_label" placeholder="Available Times"></div>
        <div class="col-md-1"><input class="form-control" name="sort_order" type="number" value="1"></div>
        <div class="col-md-1"><button class="btn btn-primary">Add</button></div>
    </div>
</form>
<table class="table bg-white"><thead><tr><th>Day</th><th>Start</th><th>End</th><th>Slot</th></tr></thead><tbody>
<?php foreach ($schedules as $s): ?><tr><td><?= e($s['day_name']) ?></td><td><?= e($s['start_time']) ?></td><td><?= e($s['end_time']) ?></td><td><?= e($s['slot_label']) ?></td></tr><?php endforeach; ?>
</tbody></table>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
