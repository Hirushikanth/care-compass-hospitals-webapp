<?php
// Include necessary files
include('../includes/config.php');
include('../includes/db.php');
include('../includes/functions.php');

// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$db = new Database();

// Get appointment ID from the URL
$appointmentId = isset($_GET['id']) ? $_GET['id'] : null;

if (!$appointmentId) {
    echo "Appointment ID not provided.";
    exit;
}

// Fetch appointment details
$appointment = $db->getAppointmentById($appointmentId); // Assuming you have this in db.php

if (!$appointment) {
    echo "Appointment not found.";
    exit;
}

// Authorization check (make sure the user has permission to view this appointment)
if ($_SESSION['user_role'] == 'patient' && $appointment['patient_id'] != $_SESSION['user_id']) {
    header("Location: patient/dashboard.php"); // Unauthorized access for patients
    exit;
} elseif ($_SESSION['user_role'] == 'staff') {
    // Staff are generally authorized to view all appointments for now
    // You might add more granular authorization checks for staff if needed
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Appointment - Care Compass Connect</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css"> <!-- Link to your main stylesheet -->
</head>
<body>
    <?php include('../includes/header.php'); ?>

    <div class="container py-5">
        <h2>Appointment Details</h2>

        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="card-title">Appointment Information</h4>
                <dl class="row">
                    <dt class="col-sm-3">Appointment ID:</dt>
                    <dd class="col-sm-9"><?= htmlspecialchars($appointment['id']) ?></dd>

                    <dt class="col-sm-3">Patient Name:</dt>
                    <dd class="col-sm-9"><?= htmlspecialchars($appointment['patient_name']) ?></dd>

                    <dt class="col-sm-3">Doctor Name:</dt>
                    <dd class="col-sm-9"><?= htmlspecialchars($appointment['doctor_name']) ?></dd>

                    <dt class="col-sm-3">Specialty:</dt>
                    <dd class="col-sm-9"><?= htmlspecialchars($appointment['doctor_specialty']) ?></dd>

                    <dt class="col-sm-3">Date:</dt>
                    <dd class="col-sm-9"><?= htmlspecialchars($appointment['appointment_date']) ?></dd>

                    <dt class="col-sm-3">Time:</dt>
                    <dd class="col-sm-9"><?= htmlspecialchars($appointment['appointment_time']) ?></dd>

                    <dt class="col-sm-3">Status:</dt>
                    <dd class="col-sm-9"><?= htmlspecialchars($appointment['status']) ?></dd>

                    <dt class="col-sm-3">Reason:</dt>
                    <dd class="col-sm-9"><?= nl2br(htmlspecialchars($appointment['reason'])) ?></dd>
                </dl>

                <div class="mt-4">
                    <!-- Actions based on user role and appointment status -->
                    <?php if ($_SESSION['user_role'] == 'staff'): ?>
                        <?php if ($appointment['status'] == 'pending'): ?>
                            <a href="confirm_appointment.php?id=<?= $appointment['id'] ?>" class="btn btn-success">Confirm Appointment</a>
                            <a href="cancel_appointment.php?id=<?= $appointment['id'] ?>" class="btn btn-danger ms-2">Cancel Appointment</a>
                        <?php endif; ?>
                        <a href="appointment_search.php" class="btn btn-secondary ms-2">Back to Search</a>
                        <a href="view_all_appointments.php" class="btn btn-secondary ms-2">View All Appointments</a>
                        <a href="dashboard.php" class="btn btn-secondary ms-2">Back to Dashboard</a>
                    <?php elseif ($_SESSION['user_role'] == 'patient'): ?>
                        <?php if ($appointment['status'] == 'pending'): ?>
                            <a href="cancel_appointment.php?id=<?= $appointment['id'] ?>" class="btn btn-danger">Cancel Appointment</a>
                        <?php endif; ?>
                        <a href="patient/dashboard.php" class="btn btn-secondary ms-2">Back to Dashboard</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php include('../includes/footer.php'); ?>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>