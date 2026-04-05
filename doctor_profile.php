<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    http_response_code(404);
    exit('Doctor not found.');
}

$stmt = $pdo->prepare("SELECT d.*, u.full_name FROM doctors d JOIN users u ON u.id = d.user_id WHERE d.id = :id");
$stmt->execute(['id' => $id]);
$doctor = $stmt->fetch();
if (!$doctor) {
    http_response_code(404);
    exit('Doctor not found.');
}

$slots = $pdo->prepare('SELECT day_name, start_time, end_time, slot_label FROM doctor_schedules WHERE doctor_id = :doctor_id ORDER BY sort_order, start_time');
$slots->execute(['doctor_id' => $id]);
$schedules = $slots->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>
<div class="row g-4">
    <div class="col-md-4"><img class="img-fluid rounded" src="<?= e($doctor['photo_path'] ?: 'https://placehold.co/640x420?text=Doctor') ?>" alt="Doctor"></div>
    <div class="col-md-8">
        <h1 class="h3"><?= e($doctor['full_name']) ?></h1>
        <p class="text-primary mb-1"><?= e($doctor['specialization']) ?></p>
        <p class="mb-1"><strong>Qualification:</strong> <?= e($doctor['qualification'] ?? 'N/A') ?></p>
        <p class="mb-1"><strong>Experience:</strong> <?= (int) $doctor['experience_years'] ?> years</p>
        <p><strong>Description:</strong> <?= e($doctor['description'] ?? '') ?></p>
        <h2 class="h5">Working Schedule</h2>
        <ul class="list-group mb-3">
            <?php foreach ($schedules as $schedule): ?>
                <li class="list-group-item d-flex justify-content-between">
                    <span><?= e($schedule['day_name']) ?>: <?= e(substr($schedule['start_time'], 0, 5)) ?> - <?= e(substr($schedule['end_time'], 0, 5)) ?></span>
                    <span class="badge bg-info text-dark"><?= e($schedule['slot_label']) ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
        <a class="btn btn-primary" href="/patient/book_appointment.php?doctor_id=<?= (int) $doctor['id'] ?>">Book Appointment</a>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
