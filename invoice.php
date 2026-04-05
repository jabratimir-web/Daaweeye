<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_role(['patient','admin']);

$paymentId = filter_input(INPUT_GET, 'payment_id', FILTER_VALIDATE_INT);
$stmt = $pdo->prepare('SELECT i.invoice_no, i.payment_status, p.created_at payment_date, p.total_amount, p.doctor_amount, p.system_amount, pu.full_name patient_name, du.full_name doctor_name FROM invoices i JOIN payments p ON p.id=i.payment_id JOIN users pu ON pu.id=p.patient_id JOIN doctors d ON d.id=p.doctor_id JOIN users du ON du.id=d.user_id WHERE p.id=:id');
$stmt->execute(['id' => $paymentId]);
$inv = $stmt->fetch();
if (!$inv) exit('Invoice not found');
header('Content-Type: text/html; charset=utf-8');
?>
<!doctype html><html><head><title>Invoice <?= e($inv['invoice_no']) ?></title><style>body{font-family:Arial;padding:20px;} .box{border:1px solid #333;padding:20px;}</style></head><body>
<div class="box">
<h1>Invoice <?= e($inv['invoice_no']) ?></h1>
<p><strong>Patient:</strong> <?= e($inv['patient_name']) ?></p>
<p><strong>Doctor:</strong> <?= e($inv['doctor_name']) ?></p>
<p><strong>Consultation Fee:</strong> $<?= number_format((float) $inv['total_amount'],2) ?></p>
<p><strong>Doctor Fee:</strong> $<?= number_format((float) $inv['doctor_amount'],2) ?></p>
<p><strong>System Fee:</strong> $<?= number_format((float) $inv['system_amount'],2) ?></p>
<p><strong>Payment Date:</strong> <?= e($inv['payment_date']) ?></p>
<p><strong>Payment Status:</strong> <?= e($inv['payment_status']) ?></p>
</div>
<script>window.print();</script>
</body></html>
