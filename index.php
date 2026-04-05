<?php
require_once __DIR__ . '/includes/bootstrap.php';
$pageTitle = 'Home - DAAWEYE TELEMEDICINE SYSTEM';
$stmt = $mysqli->prepare('SELECT id, full_name, specialization, experience_years, short_bio, photo FROM doctors ORDER BY full_name');
$stmt->execute();
$doctors = fetch_all_assoc($stmt);
require_once __DIR__ . '/includes/header.php';
?>
<section class="hero mb-5">
  <h1>Welcome to DAAWEYE TELEMEDICINE SYSTEM</h1>
  <p class="mb-4">Consult trusted doctors online, book appointments, and manage your health records securely.</p>
  <a href="doctors.php" class="btn btn-light btn-lg">Find a Doctor</a>
</section>
<section id="doctors" class="mb-5">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Our Doctors</h2>
    <a href="doctors.php" class="btn btn-outline-primary">View All</a>
  </div>
  <div class="row g-4">
    <?php foreach (array_slice($doctors, 0, 6) as $doctor): ?>
      <div class="col-md-4">
        <div class="card card-doctor h-100 shadow-sm">
          <img src="<?= e($doctor['photo'] ?: 'https://via.placeholder.com/600x400?text=Doctor') ?>" class="card-img-top" alt="Doctor photo">
          <div class="card-body">
            <h5 class="card-title"><?= e($doctor['full_name']) ?></h5>
            <p class="mb-1 text-primary"><?= e($doctor['specialization']) ?></p>
            <p class="mb-1"><?= (int) $doctor['experience_years'] ?> Years Experience</p>
            <p class="small text-muted"><?= e($doctor['short_bio']) ?></p>
            <a href="doctor_profile.php?id=<?= (int) $doctor['id'] ?>" class="btn btn-primary">View Profile</a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>
<section id="about" class="mb-5">
  <h2>About DAAWEYE</h2>
  <p>DAAWEYE connects patients to licensed clinicians through secure digital care pathways.</p>
  <a href="about.php" class="btn btn-outline-secondary">Learn More</a>
</section>
<section id="contact">
  <h2>Contact Preview</h2>
  <p>Need support? Our team is available 24/7 for scheduling and platform help.</p>
  <a href="contact.php" class="btn btn-outline-secondary">Contact Us</a>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
