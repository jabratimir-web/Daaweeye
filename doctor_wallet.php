<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_login('doctor');
verify_csrf();
$doctorUserId = current_user_id();
$dr = $mysqli->query("SELECT id FROM doctors WHERE user_id=".$doctorUserId)->fetch_assoc();
$doctorId = (int)($dr['id'] ?? 0);
if (!$doctorId) exit('Doctor profile not found');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = (float)($_POST['amount'] ?? 0);
    if ($amount > 0) {
        $status = 'Pending';
        $stmt = $mysqli->prepare('INSERT INTO withdraw_requests (doctor_id, amount, status) VALUES (?, ?, ?)');
        $stmt->bind_param('ids', $doctorId, $amount, $status);
        $stmt->execute();
    }
}
$wallet = $mysqli->query("SELECT balance,total_earned FROM doctor_wallet WHERE doctor_id=$doctorId")->fetch_assoc() ?: ['balance'=>0,'total_earned'=>0];
$history = $mysqli->query("SELECT p.id,p.total_amount,p.doctor_share,p.created_at,u.full_name patient_name FROM payments p JOIN appointments a ON a.id=p.appointment_id JOIN users u ON u.id=a.patient_id WHERE a.doctor_id=$doctorId ORDER BY p.id DESC")->fetch_all(MYSQLI_ASSOC);
$pageTitle = 'Doctor Wallet';
require_once __DIR__ . '/includes/header.php';
?>
<h1>Doctor Wallet</h1>
<div class="row g-3 mb-3">
<div class="col-md-6"><div class="card p-3"><h6>Current Balance</h6><h3>$<?= number_format((float)$wallet['balance'],2) ?></h3></div></div>
<div class="col-md-6"><div class="card p-3"><h6>Total Earned</h6><h3>$<?= number_format((float)$wallet['total_earned'],2) ?></h3></div></div>
</div>
<form method="post" class="card p-3 mb-3"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><label class="form-label">Withdraw Amount</label><input type="number" min="1" step="0.01" name="amount" class="form-control mb-2" required><button class="btn btn-primary">Request Withdraw</button></form>
<h4>Payment History</h4>
<table class="table table-striped"><tr><th>ID</th><th>Patient</th><th>Total</th><th>Doctor Share</th><th>Date</th></tr><?php foreach($history as $h):?><tr><td><?= (int)$h['id'] ?></td><td><?= e($h['patient_name']) ?></td><td>$<?= e($h['total_amount']) ?></td><td>$<?= e($h['doctor_share']) ?></td><td><?= e($h['created_at']) ?></td></tr><?php endforeach;?></table>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
