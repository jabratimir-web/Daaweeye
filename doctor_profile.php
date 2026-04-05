<?php
require_once __DIR__ . '/includes/bootstrap.php';
$doctorId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$doctorId) {
    exit('Invalid doctor id.');
}
$stmt = $mysqli->prepare('SELECT * FROM doctors WHERE id = ?');
$stmt->bind_param('i', $doctorId);
$stmt->execute();
$doctor = $stmt->get_result()->fetch_assoc();
if (!$doctor) {
    exit('Doctor not found.');
}
$slotStmt = $mysqli->prepare('SELECT day_name, start_time, end_time, slot_time FROM doctor_schedules WHERE doctor_id = ? ORDER BY FIELD(day_name,"Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday")');
$slotStmt->bind_param('i', $doctorId);
$slotStmt->execute();
$schedules = fetch_all_assoc($slotStmt);
$pageTitle = 'Doctor Profile';
require_once __DIR__ . '/includes/header.php';
?>
<div class="row g-4">
  <div class="col-lg-4"><img class="img-fluid rounded shadow-sm" src="<?= e($doctor['photo'] ?: 'https://via.placeholder.com/600x400?text=Doctor') ?>" alt="Doctor"></div>
  <div class="col-lg-8">
    <h1><?= e($doctor['full_name']) ?></h1>
    <p class="text-primary fw-semibold"><?= e($doctor['specialization']) ?></p>
    <p><strong>Qualification:</strong> <?= e($doctor['qualification'] ?? 'N/A') ?></p>
    <p><strong>Experience:</strong> <?= (int)$doctor['experience_years'] ?> years</p>
    <p><strong>Description:</strong> <?= e($doctor['description'] ?? '') ?></p>
    <h5>Working Schedule</h5>
    <ul>
      <?php foreach ($schedules as $schedule): ?>
      <li><?= e($schedule['day_name']) ?>: <?= e($schedule['start_time']) ?> - <?= e($schedule['end_time']) ?> (Slots: <?= e($schedule['slot_time']) ?>)</li>
      <?php endforeach; ?>
    </ul>
    <a href="book_appointment.php?doctor_id=<?= (int)$doctor['id'] ?>" class="btn btn-success">Book Appointment</a>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
