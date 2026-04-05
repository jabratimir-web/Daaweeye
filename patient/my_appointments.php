<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_role(['patient']);

$stmt = $pdo->prepare("SELECT a.*, u.full_name doctor_name FROM appointments a JOIN doctors d ON d.id = a.doctor_id JOIN users u ON u.id = d.user_id WHERE a.patient_id = :patient_id ORDER BY a.id DESC");
$stmt->execute(['patient_id' => $_SESSION['user']['id']]);
$appointments = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>
<h1 class="h4 mb-3">My Appointments</h1>
<table class="table table-striped bg-white shadow-sm">
    <thead><tr><th>Doctor</th><th>Date</th><th>Time</th><th>Status</th><th>Payment</th></tr></thead>
    <tbody>
    <?php foreach ($appointments as $a): ?>
        <tr>
            <td><?= e($a['doctor_name']) ?></td><td><?= e($a['appointment_date']) ?></td><td><?= e($a['appointment_time']) ?></td><td><?= e($a['status']) ?></td>
            <td><?php if ($a['status'] === 'Approved'): ?><a href="/patient/pay.php?appointment_id=<?= (int) $a['id'] ?>" class="btn btn-sm btn-success">Pay $6</a><?php endif; ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
