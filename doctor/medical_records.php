<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_role(['doctor']);

$doctorRow = $pdo->prepare('SELECT id FROM doctors WHERE user_id = :user_id');
$doctorRow->execute(['user_id' => $_SESSION['user']['id']]);
$doctorId = (int) ($doctorRow->fetch()['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf()) {
    $patientId = (int) ($_POST['patient_id'] ?? 0);
    $diagnosis = trim((string) ($_POST['diagnosis'] ?? ''));
    $notes = trim((string) ($_POST['notes'] ?? ''));
    $prescriptions = trim((string) ($_POST['prescriptions'] ?? ''));
    $history = trim((string) ($_POST['patient_history'] ?? ''));

    $stmt = $pdo->prepare('INSERT INTO medical_records (patient_id, doctor_id, diagnosis, notes, prescriptions, patient_history) VALUES (:patient_id,:doctor_id,:diagnosis,:notes,:prescriptions,:patient_history)');
    $stmt->execute([
        'patient_id' => $patientId,
        'doctor_id' => $doctorId,
        'diagnosis' => $diagnosis,
        'notes' => $notes,
        'prescriptions' => $prescriptions,
        'patient_history' => $history,
    ]);
    $pdo->prepare('INSERT INTO notifications (user_id, title, body) VALUES (:user_id, :title, :body)')->execute(['user_id' => $patientId, 'title' => 'Prescription created', 'body' => 'Your doctor added a new medical record.']);
    flash('success', 'Medical record saved.');
    header('Location: /doctor/medical_records.php');
    exit;
}

$patients = $pdo->query("SELECT id, full_name FROM users WHERE role='patient' ORDER BY full_name")->fetchAll();
$records = $pdo->prepare('SELECT mr.*, u.full_name patient_name FROM medical_records mr JOIN users u ON u.id = mr.patient_id WHERE mr.doctor_id = :doctor_id ORDER BY mr.id DESC');
$records->execute(['doctor_id' => $doctorId]);
$rows = $records->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>
<h1 class="h4">Medical Records</h1>
<form method="post" class="card p-3 mb-3">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <div class="mb-2"><label class="form-label">Patient</label><select name="patient_id" class="form-select" required><?php foreach ($patients as $p): ?><option value="<?= (int) $p['id'] ?>"><?= e($p['full_name']) ?></option><?php endforeach; ?></select></div>
    <div class="mb-2"><input class="form-control" name="diagnosis" placeholder="Diagnosis" required></div>
    <div class="mb-2"><textarea class="form-control" name="notes" placeholder="Notes"></textarea></div>
    <div class="mb-2"><textarea class="form-control" name="prescriptions" placeholder="Prescriptions"></textarea></div>
    <div class="mb-2"><textarea class="form-control" name="patient_history" placeholder="Patient History"></textarea></div>
    <button class="btn btn-primary">Save Record</button>
</form>
<table class="table bg-white"><thead><tr><th>Patient</th><th>Diagnosis</th><th>Date</th></tr></thead><tbody><?php foreach ($rows as $row): ?><tr><td><?= e($row['patient_name']) ?></td><td><?= e($row['diagnosis']) ?></td><td><?= e($row['created_at']) ?></td></tr><?php endforeach; ?></tbody></table>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
