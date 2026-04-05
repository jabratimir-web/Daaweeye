<?php
require_once __DIR__ . '/includes/bootstrap.php';
if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}
verify_csrf();
$doctorId = filter_input(INPUT_GET, 'doctor_id', FILTER_VALIDATE_INT) ?: filter_input(INPUT_POST, 'doctor_id', FILTER_VALIDATE_INT);
$message = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $doctorId) {
    $appointmentDate = $_POST['appointment_date'] ?? '';
    $appointmentTime = $_POST['appointment_time'] ?? '';
    if ($appointmentDate && $appointmentTime) {
        $patientId = current_user_id();
        $status = 'Pending';
        $stmt = $mysqli->prepare('INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time, status) VALUES (?, ?, ?, ?, ?)');
        $stmt->bind_param('iisss', $patientId, $doctorId, $appointmentDate, $appointmentTime, $status);
        $stmt->execute();

        $note = 'Appointment booked and awaiting approval.';
        $nstmt = $mysqli->prepare('INSERT INTO notifications (user_id, type, message) VALUES (?, ?, ?)');
        $type = 'appointment_booked';
        $nstmt->bind_param('iss', $patientId, $type, $note);
        $nstmt->execute();

        $message = 'Appointment request submitted.';
    }
}

$schedule = [];
if ($doctorId) {
    $s = $mysqli->prepare('SELECT day_name, slot_time FROM doctor_schedules WHERE doctor_id=?');
    $s->bind_param('i', $doctorId);
    $s->execute();
    $schedule = fetch_all_assoc($s);
}
$pageTitle = 'Book Appointment';
require_once __DIR__ . '/includes/header.php';
?>
<h1>Book Appointment</h1>
<?php if ($message): ?><div class="alert alert-success"><?= e($message) ?></div><?php endif; ?>
<?php if (!$doctorId): ?>
  <div class="alert alert-warning">Please open this page from a doctor profile to select available slots.</div>
<?php endif; ?>
<form method="post" class="card p-3">
<input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
<input type="hidden" name="doctor_id" value="<?= (int)$doctorId ?>">
<div class="mb-3"><label class="form-label">Date</label><input type="date" class="form-control" name="appointment_date" min="<?= date('Y-m-d') ?>" required></div>
<div class="mb-3"><label class="form-label">Available Time Slot</label>
<select class="form-select" name="appointment_time" required>
<?php foreach ($schedule as $slot): ?><option value="<?= e($slot['slot_time']) ?>"><?= e($slot['day_name']) ?> - <?= e($slot['slot_time']) ?></option><?php endforeach; ?>
</select></div>
<button class="btn btn-primary">Submit Appointment Request</button>
</form>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
