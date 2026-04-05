<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_role(['patient']);

$doctorId = filter_input(INPUT_GET, 'doctor_id', FILTER_VALIDATE_INT) ?: filter_input(INPUT_POST, 'doctor_id', FILTER_VALIDATE_INT);
if (!$doctorId) {
    exit('Invalid doctor.');
}

$doctorStmt = $pdo->prepare("SELECT d.id, u.full_name FROM doctors d JOIN users u ON u.id = d.user_id WHERE d.id = :id");
$doctorStmt->execute(['id' => $doctorId]);
$doctor = $doctorStmt->fetch();
if (!$doctor) {
    exit('Doctor not found.');
}

$slotStmt = $pdo->prepare('SELECT id, day_name, start_time, end_time, slot_label FROM doctor_schedules WHERE doctor_id = :doctor_id ORDER BY sort_order, start_time');
$slotStmt->execute(['doctor_id' => $doctorId]);
$slots = $slotStmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        flash('error', 'Invalid CSRF token.');
    } else {
        $date = (string) ($_POST['appointment_date'] ?? '');
        $slotId = (int) ($_POST['schedule_id'] ?? 0);
        $time = (string) ($_POST['appointment_time'] ?? '');

        $insert = $pdo->prepare('INSERT INTO appointments (patient_id, doctor_id, schedule_id, appointment_date, appointment_time, status) VALUES (:patient_id, :doctor_id, :schedule_id, :appointment_date, :appointment_time, :status)');
        $insert->execute([
            'patient_id' => $_SESSION['user']['id'],
            'doctor_id' => $doctorId,
            'schedule_id' => $slotId ?: null,
            'appointment_date' => $date,
            'appointment_time' => $time,
            'status' => 'Pending',
        ]);

        $notif = $pdo->prepare('INSERT INTO notifications (user_id, title, body) VALUES (:user_id, :title, :body)');
        $notif->execute(['user_id' => $doctor['id'], 'title' => 'New appointment booked', 'body' => 'A patient submitted a new appointment request.']);
        flash('success', 'Appointment request submitted (Pending).');
        header('Location: /patient/my_appointments.php');
        exit;
    }
}

require_once __DIR__ . '/../includes/header.php';
?>
<h1 class="h4">Book Appointment with <?= e($doctor['full_name']) ?></h1>
<form method="post" class="card p-3 shadow-sm">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <input type="hidden" name="doctor_id" value="<?= (int) $doctorId ?>">
    <div class="mb-3"><label class="form-label">Date</label><input type="date" min="<?= date('Y-m-d') ?>" class="form-control" name="appointment_date" required></div>
    <div class="mb-3"><label class="form-label">Available Slot</label>
        <select class="form-select" name="schedule_id">
            <option value="">Manual time selection</option>
            <?php foreach ($slots as $slot): ?>
                <option value="<?= (int) $slot['id'] ?>"><?= e($slot['day_name']) ?> (<?= e(substr($slot['start_time'], 0, 5)) ?>-<?= e(substr($slot['end_time'], 0, 5)) ?>) - <?= e($slot['slot_label']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="mb-3"><label class="form-label">Time</label><input type="time" class="form-control" name="appointment_time" required></div>
    <button class="btn btn-primary" type="submit">Submit Appointment Request</button>
</form>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
