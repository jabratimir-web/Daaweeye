<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_role(['patient']);

$appointmentId = filter_input(INPUT_GET, 'appointment_id', FILTER_VALIDATE_INT) ?: filter_input(INPUT_POST, 'appointment_id', FILTER_VALIDATE_INT);
$appt = $pdo->prepare('SELECT * FROM appointments WHERE id = :id AND patient_id = :patient_id');
$appt->execute(['id' => $appointmentId, 'patient_id' => $_SESSION['user']['id']]);
$appointment = $appt->fetch();
if (!$appointment) exit('Appointment not found.');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf()) {
    $pdo->beginTransaction();
    try {
        $pay = $pdo->prepare("INSERT INTO payments (appointment_id, patient_id, doctor_id, total_amount, doctor_amount, system_amount, status) VALUES (:appointment_id, :patient_id, :doctor_id, 6, 4, 2, 'Completed')");
        $pay->execute(['appointment_id' => $appointmentId, 'patient_id' => $_SESSION['user']['id'], 'doctor_id' => $appointment['doctor_id']]);
        $paymentId = (int) $pdo->lastInsertId();

        $pdo->prepare('INSERT INTO doctor_wallet (doctor_id, payment_id, credit, description) VALUES (:doctor_id, :payment_id, 4, :description)')
            ->execute(['doctor_id' => $appointment['doctor_id'], 'payment_id' => $paymentId, 'description' => 'Consultation payment']);

        $pdo->prepare('INSERT INTO system_wallet (payment_id, credit, description) VALUES (:payment_id, 2, :description)')
            ->execute(['payment_id' => $paymentId, 'description' => 'Platform service fee']);

        $pdo->prepare("INSERT INTO invoices (payment_id, invoice_no, payment_status) VALUES (:payment_id, :invoice_no, 'Completed')")
            ->execute(['payment_id' => $paymentId, 'invoice_no' => 'INV-' . date('Ymd') . '-' . $paymentId]);

        $pdo->prepare('INSERT INTO notifications (user_id, title, body) VALUES (:user_id, :title, :body)')
            ->execute(['user_id' => $appointment['doctor_id'], 'title' => 'Payment completed', 'body' => 'A consultation payment has been completed.']);

        $pdo->prepare("UPDATE appointments SET status = 'Completed' WHERE id = :id")->execute(['id' => $appointmentId]);

        $pdo->commit();
        flash('success', 'Payment completed and invoice generated.');
        header('Location: /patient/my_appointments.php');
        exit;
    } catch (Throwable $e) {
        $pdo->rollBack();
        flash('error', 'Payment failed. Please retry.');
    }
}

require_once __DIR__ . '/../includes/header.php';
?>
<div class="card shadow-sm p-4">
    <h1 class="h4">Consultation Payment</h1>
    <p>Patient pays <strong>$6</strong>. Doctor earns $4. System fee $2.</p>
    <form method="post">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="appointment_id" value="<?= (int) $appointmentId ?>">
        <button class="btn btn-success" type="submit">Confirm Payment $6</button>
    </form>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
