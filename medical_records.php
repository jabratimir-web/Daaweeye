<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_login();
verify_csrf();
$role = $_SESSION['user']['role'];
$userId = current_user_id();
$msg = null;
if ($role === 'doctor' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $patientId = filter_input(INPUT_POST, 'patient_id', FILTER_VALIDATE_INT);
    $diagnosis = trim($_POST['diagnosis'] ?? '');
    $notes = trim($_POST['notes'] ?? '');
    $prescriptions = trim($_POST['prescriptions'] ?? '');
    $history = trim($_POST['patient_history'] ?? '');
    $doctorId = (int)($mysqli->query("SELECT id FROM doctors WHERE user_id=$userId")->fetch_assoc()['id'] ?? 0);
    $stmt = $mysqli->prepare('INSERT INTO medical_records (patient_id, doctor_id, diagnosis, notes, prescriptions, patient_history) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->bind_param('iissss', $patientId, $doctorId, $diagnosis, $notes, $prescriptions, $history);
    $stmt->execute();
    $type='prescription_created';$text='New medical record and prescription added.';
    $n=$mysqli->prepare('INSERT INTO notifications (user_id,type,message) VALUES (?,?,?)');
    $n->bind_param('iss',$patientId,$type,$text);$n->execute();
    $msg = 'Medical record saved.';
}
if ($role === 'doctor') {
    $records = $mysqli->query("SELECT mr.*, u.full_name patient_name FROM medical_records mr JOIN users u ON u.id=mr.patient_id WHERE mr.doctor_id=(SELECT id FROM doctors WHERE user_id=$userId) ORDER BY mr.id DESC")->fetch_all(MYSQLI_ASSOC);
    $patients = $mysqli->query("SELECT id, full_name FROM users WHERE role='patient' ORDER BY full_name")->fetch_all(MYSQLI_ASSOC);
} else {
    $records = $mysqli->query("SELECT mr.*, d.full_name doctor_name FROM medical_records mr JOIN doctors d ON d.id=mr.doctor_id WHERE mr.patient_id=$userId ORDER BY mr.id DESC")->fetch_all(MYSQLI_ASSOC);
}
$pageTitle = 'Medical Records';
require_once __DIR__ . '/includes/header.php';
?>
<h1>Medical Records</h1>
<?php if ($msg): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
<?php if ($role === 'doctor'): ?>
<form method="post" class="card p-3 mb-3">
<input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
<select class="form-select mb-2" name="patient_id" required><?php foreach($patients as $p):?><option value="<?= (int)$p['id'] ?>"><?= e($p['full_name']) ?></option><?php endforeach;?></select>
<input class="form-control mb-2" name="diagnosis" placeholder="Diagnosis" required>
<textarea class="form-control mb-2" name="notes" placeholder="Notes"></textarea>
<textarea class="form-control mb-2" name="prescriptions" placeholder="Prescriptions"></textarea>
<textarea class="form-control mb-2" name="patient_history" placeholder="Patient History"></textarea>
<button class="btn btn-primary">Save Record</button>
</form>
<?php endif; ?>
<table class="table table-striped"><tr><th>Date</th><th><?= $role === 'doctor' ? 'Patient' : 'Doctor' ?></th><th>Diagnosis</th><th>Notes</th><th>Prescription</th><th>History</th></tr><?php foreach($records as $r):?><tr><td><?= e($r['created_at']) ?></td><td><?= e($r['patient_name'] ?? $r['doctor_name']) ?></td><td><?= e($r['diagnosis']) ?></td><td><?= e($r['notes']) ?></td><td><?= e($r['prescriptions']) ?></td><td><?= e($r['patient_history']) ?></td></tr><?php endforeach;?></table>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
