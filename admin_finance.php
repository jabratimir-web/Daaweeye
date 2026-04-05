<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_login('admin');
$totals = $mysqli->query("SELECT COALESCE(SUM(total_amount),0) total_revenue, COALESCE(SUM(doctor_share),0) doctor_earnings, COALESCE(SUM(system_share),0) system_earnings FROM payments WHERE status='Completed'")->fetch_assoc();
$daily = $mysqli->query("SELECT DATE(created_at) day, SUM(total_amount) amount FROM payments WHERE status='Completed' GROUP BY DATE(created_at) ORDER BY day DESC LIMIT 7")->fetch_all(MYSQLI_ASSOC);
$monthly = $mysqli->query("SELECT DATE_FORMAT(created_at, '%Y-%m') month, SUM(total_amount) amount FROM payments WHERE status='Completed' GROUP BY DATE_FORMAT(created_at, '%Y-%m') ORDER BY month DESC LIMIT 6")->fetch_all(MYSQLI_ASSOC);
$withdraws = $mysqli->query('SELECT w.id,d.full_name,w.amount,w.status,w.created_at FROM withdraw_requests w JOIN doctors d ON d.id=w.doctor_id ORDER BY w.id DESC')->fetch_all(MYSQLI_ASSOC);
$pageTitle = 'Admin Finance Dashboard';
require_once __DIR__ . '/includes/header.php';
?>
<h1>Admin Finance Dashboard</h1>
<div class="row g-3 mb-4">
<div class="col-md-4"><div class="card p-3"><h6>Total Revenue</h6><h4>$<?= number_format((float)$totals['total_revenue'],2) ?></h4></div></div>
<div class="col-md-4"><div class="card p-3"><h6>Doctor Earnings</h6><h4>$<?= number_format((float)$totals['doctor_earnings'],2) ?></h4></div></div>
<div class="col-md-4"><div class="card p-3"><h6>System Earnings</h6><h4>$<?= number_format((float)$totals['system_earnings'],2) ?></h4></div></div>
</div>
<h4>Daily Revenue</h4><ul><?php foreach($daily as $d):?><li><?= e($d['day']) ?>: $<?= e($d['amount']) ?></li><?php endforeach;?></ul>
<h4>Monthly Revenue</h4><ul><?php foreach($monthly as $m):?><li><?= e($m['month']) ?>: $<?= e($m['amount']) ?></li><?php endforeach;?></ul>
<h4>Withdraw Requests</h4>
<table class="table table-bordered"><tr><th>ID</th><th>Doctor</th><th>Amount</th><th>Status</th><th>Date</th></tr><?php foreach($withdraws as $w):?><tr><td><?= (int)$w['id'] ?></td><td><?= e($w['full_name']) ?></td><td>$<?= e($w['amount']) ?></td><td><?= e($w['status']) ?></td><td><?= e($w['created_at']) ?></td></tr><?php endforeach;?></table>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
