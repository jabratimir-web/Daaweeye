<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_role(['doctor']);

$doctor = $pdo->prepare('SELECT id FROM doctors WHERE user_id = :user_id');
$doctor->execute(['user_id' => $_SESSION['user']['id']]);
$doctorId = (int) ($doctor->fetch()['id'] ?? 0);

$count = $pdo->prepare('SELECT COUNT(*) total FROM appointments WHERE doctor_id = :doctor_id');
$count->execute(['doctor_id' => $doctorId]);
$totalAppointments = (int) $count->fetch()['total'];

require_once __DIR__ . '/../includes/header.php';
?>
<h1 class="h4">Doctor Dashboard</h1>
<div class="row g-3">
    <div class="col-md-4"><div class="card stat-card p-3"><h2 class="h6">Appointments</h2><p class="display-6 mb-0"><?= $totalAppointments ?></p></div></div>
    <div class="col-md-4"><a class="btn btn-outline-primary w-100 h-100 d-flex align-items-center justify-content-center" href="/doctor/schedule.php">Manage Schedule</a></div>
    <div class="col-md-4"><a class="btn btn-outline-success w-100 h-100 d-flex align-items-center justify-content-center" href="/doctor/wallet.php">Wallet</a></div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
