<?php
// Include necessary files
include('includes/config.php');
include('includes/db.php');
include('includes/functions.php');

$db = new Database();

// Fetch all doctors for the directory
$doctors = $db->getAllDoctors();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Doctors - CareCompass Connect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            color: var(--text-color);
            background-color: var(--light-background);
        }
        .doctors-header {
            background: linear-gradient(rgba(4, 106, 122, 0.7), rgba(4, 106, 122, 0.2)), url('assets/images/DoctorsHero.jpg'); /* Replace with your doctors hero image */
            background-size: cover;
            background-position: center;
            color: white;
            padding: 150px 0;
            text-align: center;
        }

        .doctors-header h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .doctors-grid {
            padding: 4rem 0;
            background-color: white;
        }

        .doctor-card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            overflow: hidden;
            text-align: center;
        }

        .doctor-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.07);
        }

        .doctor-card img {
            border-top-left-radius: 0.75rem;
            border-top-right-radius: 0.75rem;
            max-height: 200px; /* Limit image height for uniformity */
            object-fit: cover; /* Ensure images cover the area */
        }

        .doctor-card-body {
            padding: 1.5rem;
        }

        .doctor-card-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--primary-teal);
            margin-bottom: 0.5rem;
        }

        .doctor-card-specialty {
            font-size: 1rem;
            color: var(--text-color);
            opacity: 0.8;
        }

        .btn-outline-primary {
            color: var(--primary-teal);
            border-color: var(--primary-teal);
            transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
        }

        .btn-outline-primary:hover {
            background-color: var(--secondary-light-teal);
            border-color: var(--primary-teal);
            color: var(--primary-teal);
        }
        .footer {
            background-color: var(--primary-teal); /* Teal footer */
            color: white;
            padding-top: 3rem;
            padding-bottom: 3rem;
        }

        .footer h4 {
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        .footer p, .footer a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer a:hover {
            color: white;
        }

        .footer .bi {
            font-size: 1.2rem;
            margin-right: 0.5rem;
        }
    </style>
</head>
<body>

    <!-- Doctors Header -->
    <header class="doctors-header">
        <div class="container">
            <h1>Meet Our Dedicated Doctors</h1>
            <p class="lead">Expert Care, Compassionate Hearts</p>
        </div>
    </header>

    <!-- Doctors Grid Section -->
    <section class="doctors-grid py-5">
        <div class="container">
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <?php if ($doctors): ?>
                    <?php foreach ($doctors as $doctor): ?>
                        <div class="col">
                            <div class="doctor-card h-100">
                                <img src="assets/images/DoctorPlaceholder.jpg" class="card-img-top" alt="Dr. <?= htmlspecialchars($doctor['fullname']) ?>"> <!-- Replace placeholder image if needed -->
                                <div class="doctor-card-body">
                                    <h3 class="doctor-card-title">Dr. <?= htmlspecialchars($doctor['fullname']) ?></h3>
                                    <p class="doctor-card-specialty"><?= htmlspecialchars($doctor['specialty'] ?? '') ?></p>
                                    <a href="doctor_profile.php?id=<?= $doctor['id'] ?>" class="btn btn-outline-primary">View Profile</a> <!-- Link to doctor profile page (not created yet) -->
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col">
                        <p class="text-muted">No doctors found.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <?php> include('includes/footer.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>