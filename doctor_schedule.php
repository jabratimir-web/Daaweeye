<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_login('doctor');
verify_csrf();
$doctorUserId = current_user_id();
$lookup = $mysqli->prepare('SELECT id FROM doctors WHERE user_id = ?');
$lookup->bind_param('i', $doctorUserId);
$lookup->execute();
$doctor = $lookup->get_result()->fetch_assoc();
if (!$doctor) { exit('Doctor profile missing.'); }
$doctorId = (int)$doctor['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $day = $_POST['day_name'] ?? '';
    $start = $_POST['start_time'] ?? '';
    $end = $_POST['end_time'] ?? '';
    $slot = $_POST['slot_time'] ?? '';
    $stmt = $mysqli->prepare('INSERT INTO doctor_schedules (doctor_id, day_name, start_time, end_time, slot_time) VALUES (?, ?, ?, ?, ?)');
    $stmt->bind_param('issss', $doctorId, $day, $start, $end, $slot);
    $stmt->execute();
}
$listStmt = $mysqli->prepare('SELECT * FROM doctor_schedules WHERE doctor_id = ?');
$listStmt->bind_param('i', $doctorId);
$listStmt->execute();
$schedules = fetch_all_assoc($listStmt);
$pageTitle = 'Doctor Schedule';
require_once __DIR__ . '/includes/header.php';
?>
<h1>Doctor Schedule</h1>
<form method="post" class="card p-3 mb-3">
<input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
<div class="row g-2">
  <div class="col-md-3"><select class="form-select" name="day_name"><?php foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $d):?><option><?= $d ?></option><?php endforeach;?></select></div>
  <div class="col-md-3"><input class="form-control" type="time" name="start_time" required></div>
  <div class="col-md-3"><input class="form-control" type="time" name="end_time" required></div>
  <div class="col-md-3"><input class="form-control" name="slot_time" placeholder="e.g 09:00-09:30" required></div>
</div>
<button class="btn btn-primary mt-2">Add Slot</button>
</form>
<ul class="list-group">
<?php foreach($schedules as $s): ?><li class="list-group-item"><?= e($s['day_name']) ?> <?= e($s['start_time']) ?>-<?= e($s['end_time']) ?> (<?= e($s['slot_time']) ?>)</li><?php endforeach; ?>
</ul>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
