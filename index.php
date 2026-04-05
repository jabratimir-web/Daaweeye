<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$stmt = $pdo->query("SELECT d.id, u.full_name, d.specialization, d.experience_years, d.short_description, d.photo_path FROM doctors d JOIN users u ON u.id = d.user_id ORDER BY u.full_name");
$doctors = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>
<section class="hero mb-4">
    <h1>Welcome to DAAWEYE TELEMEDICINE SYSTEM</h1>
    <p class="lead mb-0">Consult certified doctors online, book appointments, and keep your medical history secure.</p>
</section>

<section class="mb-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h4">Our Doctors</h2>
        <a class="btn btn-outline-primary btn-sm" href="/doctors.php">View all doctors</a>
    </div>
    <div class="row g-3">
        <?php foreach ($doctors as $doctor): ?>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm">
                    <img class="doctor-photo card-img-top" src="<?= e($doctor['photo_path'] ?: 'https://placehold.co/640x420?text=Doctor') ?>" alt="Doctor photo">
                    <div class="card-body">
                        <h3 class="h5 mb-1"><?= e($doctor['full_name']) ?></h3>
                        <p class="text-primary mb-1"><?= e($doctor['specialization']) ?></p>
                        <p class="small text-muted mb-2"><?= (int) $doctor['experience_years'] ?> Years Experience</p>
                        <p class="small"><?= e($doctor['short_description'] ?? '') ?></p>
                        <a class="btn btn-primary btn-sm" href="/doctor_profile.php?id=<?= (int) $doctor['id'] ?>">View Profile</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="row g-4">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-body">
                <h2 class="h5">About Us</h2>
                <p>DAAWEYE connects patients with doctors through trusted virtual care, appointment management, and digital health records.</p>
                <a href="/about.php" class="btn btn-outline-primary btn-sm">Read More</a>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-body">
                <h2 class="h5">Contact</h2>
                <p>Need support? Reach out to our team for help with booking, consultations, billing, or technical assistance.</p>
                <a href="/contact.php" class="btn btn-outline-primary btn-sm">Contact Us</a>
            </div>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
