<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_role(['admin']);

$metrics = [
    'patients' => (int) $pdo->query("SELECT COUNT(*) c FROM users WHERE role='patient'")->fetch()['c'],
    'doctors' => (int) $pdo->query("SELECT COUNT(*) c FROM users WHERE role='doctor'")->fetch()['c'],
    'appointments' => (int) $pdo->query('SELECT COUNT(*) c FROM appointments')->fetch()['c'],
    'pending' => (int) $pdo->query("SELECT COUNT(*) c FROM appointments WHERE status='Pending'")->fetch()['c'],
    'completed' => (int) $pdo->query("SELECT COUNT(*) c FROM appointments WHERE status='Completed'")->fetch()['c'],
    'revenue' => (float) $pdo->query("SELECT COALESCE(SUM(total_amount),0) c FROM payments WHERE status='Completed'")->fetch()['c'],
];

$daily = $pdo->query("SELECT DATE(created_at) d, SUM(total_amount) amount FROM payments GROUP BY DATE(created_at) ORDER BY d DESC LIMIT 7")->fetchAll();
$daily = array_reverse($daily);

require_once __DIR__ . '/../includes/header.php';
?>
<h1 class="h4 mb-3">Admin Dashboard</h1>
<div class="row g-3 mb-4">
    <?php foreach ($metrics as $k => $v): ?><div class="col-md-4"><div class="card stat-card p-3"><p class="mb-1 text-capitalize">Total <?= e($k) ?></p><h2 class="h4 mb-0"><?= is_float($v) ? '$' . number_format($v,2) : (int) $v ?></h2></div></div><?php endforeach; ?>
</div>
<div class="card p-3 mb-3">
    <h2 class="h5">Revenue (Last 7 days)</h2>
    <canvas id="revChart" height="100"></canvas>
</div>
<a href="/admin/finance.php" class="btn btn-primary">Open Finance Dashboard</a>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const labels = <?= json_encode(array_column($daily, 'd')) ?>;
const values = <?= json_encode(array_map('floatval', array_column($daily, 'amount'))) ?>;
new Chart(document.getElementById('revChart'), {
  type: 'line',
  data: { labels, datasets: [{ label: 'Revenue', data: values, borderColor: '#0d6efd', fill: false }] }
});
</script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
