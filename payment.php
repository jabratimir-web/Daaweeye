<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_login('patient');
verify_csrf();
$msg = null;
$patientId = current_user_id();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointmentId = filter_input(INPUT_POST, 'appointment_id', FILTER_VALIDATE_INT);
    $methodId = filter_input(INPUT_POST, 'payment_method_id', FILTER_VALIDATE_INT);
    if ($appointmentId && $methodId) {
        $total = 6.00; $doctorShare = 4.00; $systemShare = 2.00; $status = 'Completed';
        $ins = $mysqli->prepare('INSERT INTO payments (appointment_id, patient_id, payment_method_id, total_amount, doctor_share, system_share, status) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $ins->bind_param('iiiddds', $appointmentId, $patientId, $methodId, $total, $doctorShare, $systemShare, $status);
        $ins->execute();
        $paymentId = $ins->insert_id;

        $doctorIdRow = $mysqli->query("SELECT doctor_id FROM appointments WHERE id=".(int)$appointmentId)->fetch_assoc();
        $doctorId = (int)$doctorIdRow['doctor_id'];
        $mysqli->query("INSERT INTO doctor_wallet (doctor_id, balance, total_earned) VALUES ($doctorId, 4, 4) ON DUPLICATE KEY UPDATE balance=balance+4, total_earned=total_earned+4");
        $mysqli->query("INSERT INTO system_wallet (id, balance, total_earned) VALUES (1, 2, 2) ON DUPLICATE KEY UPDATE balance=balance+2, total_earned=total_earned+2");

        $inv = $mysqli->prepare('INSERT INTO invoices (payment_id, invoice_number, consultation_fee, doctor_fee, system_fee, status) VALUES (?, ?, 6.00, 4.00, 2.00, ?)');
        $invoiceNo = 'INV-' . date('Ymd') . '-' . $paymentId;
        $inv->bind_param('iss', $paymentId, $invoiceNo, $status);
        $inv->execute();

        $n = $mysqli->prepare('INSERT INTO notifications (user_id, type, message) VALUES (?, ?, ?)');
        $type = 'payment_completed'; $message = 'Payment completed and invoice generated.';
        $n->bind_param('iss', $patientId, $type, $message);
        $n->execute();
        $msg = 'Payment completed. Invoice: ' . $invoiceNo;
    }
}
$apps = $mysqli->query("SELECT a.id, d.full_name doctor_name, a.appointment_date FROM appointments a JOIN doctors d ON d.id=a.doctor_id WHERE a.patient_id=$patientId AND a.status IN ('Approved','Completed') ORDER BY a.id DESC")->fetch_all(MYSQLI_ASSOC);
$methods = $mysqli->query('SELECT id, method_name FROM payment_methods WHERE is_active=1')->fetch_all(MYSQLI_ASSOC);
$pageTitle = 'Patient Payment';
require_once __DIR__ . '/includes/header.php';
?>
<h1>Patient Payment</h1>
<?php if ($msg): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
<form method="post" class="card p-3">
<input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
<div class="mb-3"><label class="form-label">Appointment</label><select class="form-select" name="appointment_id" required><?php foreach($apps as $a): ?><option value="<?= (int)$a['id'] ?>">#<?= (int)$a['id'] ?> - <?= e($a['doctor_name']) ?> (<?= e($a['appointment_date']) ?>)</option><?php endforeach; ?></select></div>
<div class="mb-3"><label class="form-label">Payment Method</label><select class="form-select" name="payment_method_id" required><?php foreach($methods as $m): ?><option value="<?= (int)$m['id'] ?>"><?= e($m['method_name']) ?></option><?php endforeach; ?></select></div>
<div class="alert alert-info">Consultation Fee: $6.00 (Doctor $4.00 / System $2.00)</div>
<button class="btn btn-success">Pay $6.00</button>
</form>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
