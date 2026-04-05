<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_role(['patient']);

$stmt = $pdo->prepare('SELECT mr.*, u.full_name doctor_name FROM medical_records mr JOIN doctors d ON d.id = mr.doctor_id JOIN users u ON u.id = d.user_id WHERE mr.patient_id = :patient_id ORDER BY mr.id DESC');
$stmt->execute(['patient_id' => $_SESSION['user']['id']]);
$rows = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>
<h1 class="h4">My Medical History</h1>
<table class="table bg-white"><thead><tr><th>Doctor</th><th>Diagnosis</th><th>Prescription</th><th>Date</th></tr></thead><tbody>
<?php foreach ($rows as $row): ?><tr><td><?= e($row['doctor_name']) ?></td><td><?= e($row['diagnosis']) ?></td><td><?= e($row['prescriptions']) ?></td><td><?= e($row['created_at']) ?></td></tr><?php endforeach; ?>
</tbody></table>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
