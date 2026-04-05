<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$stmt = $pdo->query("SELECT d.id, u.full_name, d.specialization, d.experience_years, d.short_description, d.photo_path FROM doctors d JOIN users u ON u.id = d.user_id ORDER BY u.full_name");
$doctors = $stmt->fetchAll();
require_once __DIR__ . '/includes/header.php';
?>
<h1 class="h3 mb-3">All Doctors</h1>
<div class="row g-3">
    <?php foreach ($doctors as $doctor): ?>
        <div class="col-md-4">
            <div class="card h-100 shadow-sm">
                <img class="doctor-photo card-img-top" src="<?= e($doctor['photo_path'] ?: 'https://placehold.co/640x420?text=Doctor') ?>" alt="Doctor photo">
                <div class="card-body">
                    <h2 class="h5"><?= e($doctor['full_name']) ?></h2>
                    <p class="text-primary mb-1"><?= e($doctor['specialization']) ?></p>
                    <p class="small text-muted"><?= (int) $doctor['experience_years'] ?> Years Experience</p>
                    <p><?= e($doctor['short_description'] ?? '') ?></p>
                    <a class="btn btn-primary btn-sm" href="/doctor_profile.php?id=<?= (int) $doctor['id'] ?>">View Profile</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
