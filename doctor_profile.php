<?php
// Include necessary files
include('includes/config.php');
include('includes/db.php');
include('includes/functions.php');

$db = new Database();

// Get doctor ID from URL parameter
$doctorId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($doctorId <= 0) {
    // Invalid doctor ID, redirect to doctors directory or display error
    header("Location: doctors.php"); // Redirect to doctors directory
    exit;
}

// Fetch doctor details from database
$doctor = $db->getDoctorById($doctorId); // You already have this function in db.php

if (!$doctor) {
    // Doctor not found, redirect to doctors directory or display error
    header("Location: doctors.php"); // Redirect to doctors directory
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dr. <?= htmlspecialchars($doctor['fullname']) ?> - Doctor Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css"> <!-- Link to your main stylesheet -->
</head>
<body>

    <?php include('includes/header.php'); ?> <!-- Include header -->

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="row g-0">
                        <div class="col-md-4">
                            <img src="assets/images/DoctorPlaceholder.jpg" alt="Dr. <?= htmlspecialchars($doctor['fullname']) ?>" class="img-fluid rounded-start"> <!-- Placeholder image -->
                        </div>
                        <div class="col-md-8">
                            <div class="card-body">
                                <h2 class="card-title"><?= htmlspecialchars($doctor['fullname']) ?></h2>
                                <p class="card-text"><strong>Specialty:</strong> <?= htmlspecialchars($doctor['specialty'] ?: 'N/A') ?></p>
                                <p class="card-text"><strong>Qualifications:</strong> <?= nl2br(htmlspecialchars($doctor['qualifications'] ?: 'N/A')) ?></p>
                                <p class="card-text"><strong>Availability:</strong> <?= nl2br(htmlspecialchars($doctor['availability'] ?: 'N/A')) ?></p>
                                <p class="card-text"><strong>Phone:</strong> <?= htmlspecialchars($doctor['phone'] ?: 'N/A') ?></p>
                                <p class="card-text"><strong>Address:</strong> <?= htmlspecialchars($doctor['address'] ?: 'N/A') ?></p>

                                <div class="mt-3">
                                    <a href="book_appointment.php?doctor_id=<?= $doctor['id'] ?>" class="btn btn-primary">Book Appointment</a>
                                    <a href="doctors.php" class="btn btn-secondary ms-2">Back to Doctors</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include('includes/footer.php'); ?> <!-- Include footer -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>