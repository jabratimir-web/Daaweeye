<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_login();
$role = $_SESSION['user']['role'];
$pageTitle = 'Dashboard';

$totalPatients = $mysqli->query("SELECT COUNT(*) c FROM users WHERE role='patient'")->fetch_assoc()['c'] ?? 0;
$totalDoctors = $mysqli->query("SELECT COUNT(*) c FROM doctors")->fetch_assoc()['c'] ?? 0;
$totalAppointments = $mysqli->query('SELECT COUNT(*) c FROM appointments')->fetch_assoc()['c'] ?? 0;
$pendingAppointments = $mysqli->query("SELECT COUNT(*) c FROM appointments WHERE status='Pending'")->fetch_assoc()['c'] ?? 0;
$completedAppointments = $mysqli->query("SELECT COUNT(*) c FROM appointments WHERE status='Completed'")->fetch_assoc()['c'] ?? 0;
$totalRevenue = $mysqli->query("SELECT COALESCE(SUM(total_amount),0) s FROM payments WHERE status='Completed'")->fetch_assoc()['s'] ?? 0;

require_once __DIR__ . '/includes/header.php';
?>
<h1><?= ucfirst(e($role)) ?> Dashboard</h1>
<div class="row g-3 mb-4">
  <div class="col-md-4"><div class="card stat-card p-3"><h6>Total Patients</h6><h3><?= (int)$totalPatients ?></h3></div></div>
  <div class="col-md-4"><div class="card stat-card p-3"><h6>Total Doctors</h6><h3><?= (int)$totalDoctors ?></h3></div></div>
  <div class="col-md-4"><div class="card stat-card p-3"><h6>Total Appointments</h6><h3><?= (int)$totalAppointments ?></h3></div></div>
  <div class="col-md-4"><div class="card stat-card p-3"><h6>Pending Requests</h6><h3><?= (int)$pendingAppointments ?></h3></div></div>
  <div class="col-md-4"><div class="card stat-card p-3"><h6>Completed Appointments</h6><h3><?= (int)$completedAppointments ?></h3></div></div>
  <div class="col-md-4"><div class="card stat-card p-3"><h6>Total Revenue</h6><h3>$<?= number_format((float)$totalRevenue,2) ?></h3></div></div>
</div>
<canvas id="statsChart" height="90"></canvas>
<script>
new Chart(document.getElementById('statsChart'), {
    type: 'bar',
    data: {
        labels: ['Patients', 'Doctors', 'Appointments', 'Pending', 'Completed'],
        datasets: [{
            label: 'System Overview',
            data: [<?= (int)$totalPatients ?>, <?= (int)$totalDoctors ?>, <?= (int)$totalAppointments ?>, <?= (int)$pendingAppointments ?>, <?= (int)$completedAppointments ?>],
            backgroundColor: ['#0d6efd','#198754','#6610f2','#ffc107','#20c997']
        }]
    }
});
</script>
<div class="mt-4 d-flex flex-wrap gap-2">
  <a class="btn btn-outline-primary" href="book_appointment.php">Book Appointment</a>
  <a class="btn btn-outline-success" href="payment.php">Pay Consultation</a>
  <a class="btn btn-outline-dark" href="doctor_wallet.php">Doctor Wallet</a>
  <a class="btn btn-outline-secondary" href="admin_finance.php">Admin Finance</a>
  <a class="btn btn-outline-info" href="medical_records.php">Medical Records</a>
  <a class="btn btn-outline-warning" href="notifications.php">Notifications</a>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
