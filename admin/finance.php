<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_role(['admin']);

$totalRevenue = (float) $pdo->query("SELECT COALESCE(SUM(total_amount),0) c FROM payments WHERE status='Completed'")->fetch()['c'];
$doctorEarn = (float) $pdo->query("SELECT COALESCE(SUM(doctor_amount),0) c FROM payments WHERE status='Completed'")->fetch()['c'];
$systemEarn = (float) $pdo->query("SELECT COALESCE(SUM(system_amount),0) c FROM payments WHERE status='Completed'")->fetch()['c'];
$dailyRevenue = $pdo->query("SELECT DATE(created_at) day_date, SUM(total_amount) amount FROM payments GROUP BY DATE(created_at) ORDER BY day_date DESC LIMIT 10")->fetchAll();
$monthlyRevenue = $pdo->query("SELECT DATE_FORMAT(created_at,'%Y-%m') month_label, SUM(total_amount) amount FROM payments GROUP BY DATE_FORMAT(created_at,'%Y-%m') ORDER BY month_label DESC LIMIT 12")->fetchAll();
$withdraws = $pdo->query('SELECT wr.*, u.full_name FROM withdraw_requests wr JOIN doctors d ON d.id = wr.doctor_id JOIN users u ON u.id = d.user_id ORDER BY wr.id DESC')->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>
<h1 class="h4 mb-3">Finance Dashboard</h1>
<div class="row g-3 mb-3">
    <div class="col-md-4"><div class="card p-3"><p>Total Revenue</p><h2>$<?= number_format($totalRevenue,2) ?></h2></div></div>
    <div class="col-md-4"><div class="card p-3"><p>Doctor Earnings</p><h2>$<?= number_format($doctorEarn,2) ?></h2></div></div>
    <div class="col-md-4"><div class="card p-3"><p>System Earnings</p><h2>$<?= number_format($systemEarn,2) ?></h2></div></div>
</div>
<h2 class="h5">Daily Revenue</h2>
<table class="table table-sm bg-white"><thead><tr><th>Date</th><th>Amount</th></tr></thead><tbody><?php foreach ($dailyRevenue as $row): ?><tr><td><?= e($row['day_date']) ?></td><td>$<?= number_format((float) $row['amount'],2) ?></td></tr><?php endforeach; ?></tbody></table>
<h2 class="h5">Monthly Revenue</h2>
<table class="table table-sm bg-white"><thead><tr><th>Month</th><th>Amount</th></tr></thead><tbody><?php foreach ($monthlyRevenue as $row): ?><tr><td><?= e($row['month_label']) ?></td><td>$<?= number_format((float) $row['amount'],2) ?></td></tr><?php endforeach; ?></tbody></table>
<h2 class="h5">Withdraw Requests</h2>
<table class="table bg-white"><thead><tr><th>Doctor</th><th>Amount</th><th>Status</th></tr></thead><tbody><?php foreach ($withdraws as $row): ?><tr><td><?= e($row['full_name']) ?></td><td>$<?= number_format((float) $row['amount'],2) ?></td><td><?= e($row['status']) ?></td></tr><?php endforeach; ?></tbody></table>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
