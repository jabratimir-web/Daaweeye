<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_login();
$invoiceId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$stmt = $mysqli->prepare('SELECT i.*, p.created_at payment_date, p.status payment_status, u.full_name patient_name, d.full_name doctor_name FROM invoices i JOIN payments p ON p.id=i.payment_id JOIN appointments a ON a.id=p.appointment_id JOIN users u ON u.id=a.patient_id JOIN doctors d ON d.id=a.doctor_id WHERE i.id=?');
$stmt->bind_param('i', $invoiceId);
$stmt->execute();
$invoice = $stmt->get_result()->fetch_assoc();
if (!$invoice) exit('Invoice not found');
$pageTitle = 'Invoice';
require_once __DIR__ . '/includes/header.php';
?>
<div class="card p-4">
<h2>Invoice <?= e($invoice['invoice_number']) ?></h2>
<p>Patient: <?= e($invoice['patient_name']) ?><br>Doctor: <?= e($invoice['doctor_name']) ?><br>Date: <?= e($invoice['payment_date']) ?><br>Status: <?= e($invoice['payment_status']) ?></p>
<table class="table"><tr><th>Consultation Fee</th><td>$6.00</td></tr><tr><th>Doctor Fee</th><td>$4.00</td></tr><tr><th>System Fee</th><td>$2.00</td></tr></table>
<a class="btn btn-outline-primary" href="invoice_pdf.php?id=<?= (int)$invoice['id'] ?>">Download PDF</a>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
