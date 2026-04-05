<?php
require_once __DIR__ . '/includes/bootstrap.php';
$pageTitle = 'Doctors';
$stmt = $mysqli->prepare('SELECT id, full_name, specialization, experience_years, short_bio, photo FROM doctors ORDER BY full_name');
$stmt->execute();
$doctors = fetch_all_assoc($stmt);
require_once __DIR__ . '/includes/header.php';
?>
<h1 class="mb-4">Doctors</h1>
<div class="row g-4">
<?php foreach ($doctors as $doctor): ?>
<div class="col-md-4">
  <div class="card card-doctor h-100">
    <img src="<?= e($doctor['photo'] ?: 'https://via.placeholder.com/600x400?text=Doctor') ?>" class="card-img-top" alt="Doctor photo">
    <div class="card-body">
      <h5><?= e($doctor['full_name']) ?></h5>
      <p class="text-primary mb-1"><?= e($doctor['specialization']) ?></p>
      <p class="mb-1"><?= (int)$doctor['experience_years'] ?> Years Experience</p>
      <p><?= e($doctor['short_bio']) ?></p>
      <a href="doctor_profile.php?id=<?= (int)$doctor['id'] ?>" class="btn btn-primary">View Profile</a>
    </div>
  </div>
</div>
<?php endforeach; ?>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
