<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_login();
$invoiceId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$stmt = $mysqli->prepare('SELECT i.invoice_number, i.consultation_fee, i.doctor_fee, i.system_fee, p.created_at payment_date, p.status payment_status, u.full_name patient_name, d.full_name doctor_name FROM invoices i JOIN payments p ON p.id=i.payment_id JOIN appointments a ON a.id=p.appointment_id JOIN users u ON u.id=a.patient_id JOIN doctors d ON d.id=a.doctor_id WHERE i.id=?');
$stmt->bind_param('i', $invoiceId);
$stmt->execute();
$invoice = $stmt->get_result()->fetch_assoc();
if (!$invoice) exit('Invoice not found');
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="'.$invoice['invoice_number'].'.pdf"');
$html = "Invoice {$invoice['invoice_number']}\nPatient: {$invoice['patient_name']}\nDoctor: {$invoice['doctor_name']}\nConsultation Fee: $6\nDoctor Fee: $4\nSystem Fee: $2\nDate: {$invoice['payment_date']}\nStatus: {$invoice['payment_status']}";
echo $html;
