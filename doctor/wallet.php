<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_role(['doctor']);

$doctor = $pdo->prepare('SELECT id FROM doctors WHERE user_id = :user_id');
$doctor->execute(['user_id' => $_SESSION['user']['id']]);
$doctorId = (int) ($doctor->fetch()['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf()) {
    $amount = (float) ($_POST['amount'] ?? 0);
    $pdo->prepare("INSERT INTO withdraw_requests (doctor_id, amount, status) VALUES (:doctor_id, :amount, 'Pending')")
        ->execute(['doctor_id' => $doctorId, 'amount' => $amount]);
    flash('success', 'Withdraw request submitted.');
    header('Location: /doctor/wallet.php');
    exit;
}

$sum = $pdo->prepare('SELECT COALESCE(SUM(credit),0) total FROM doctor_wallet WHERE doctor_id = :doctor_id');
$sum->execute(['doctor_id' => $doctorId]);
$total = (float) $sum->fetch()['total'];

$wd = $pdo->prepare("SELECT COALESCE(SUM(amount),0) total FROM withdraw_requests WHERE doctor_id = :doctor_id AND status='Approved'");
$wd->execute(['doctor_id' => $doctorId]);
$withdrawn = (float) $wd->fetch()['total'];
$balance = $total - $withdrawn;

$history = $pdo->prepare('SELECT * FROM doctor_wallet WHERE doctor_id = :doctor_id ORDER BY id DESC');
$history->execute(['doctor_id' => $doctorId]);
$rows = $history->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>
<h1 class="h4">Doctor Wallet</h1>
<div class="row g-3 mb-3">
<div class="col-md-4"><div class="card p-3"><p class="mb-1">Current Balance</p><h2>$<?= number_format($balance,2) ?></h2></div></div>
<div class="col-md-4"><div class="card p-3"><p class="mb-1">Total Earned</p><h2>$<?= number_format($total,2) ?></h2></div></div>
</div>
<form method="post" class="card p-3 mb-3">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <div class="row g-2 align-items-end">
        <div class="col-md-3"><label class="form-label">Withdraw Amount</label><input class="form-control" name="amount" type="number" step="0.01" min="1" max="<?= e((string)$balance) ?>" required></div>
        <div class="col-md-3"><button class="btn btn-warning" type="submit">Withdraw Request</button></div>
    </div>
</form>
<table class="table bg-white"><thead><tr><th>Amount</th><th>Description</th><th>Date</th></tr></thead><tbody>
<?php foreach ($rows as $r): ?><tr><td>$<?= number_format((float) $r['credit'],2) ?></td><td><?= e($r['description']) ?></td><td><?= e($r['created_at']) ?></td></tr><?php endforeach; ?>
</tbody></table>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
