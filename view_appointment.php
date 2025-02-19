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
    header("Location: ../login.php");
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
$appointment = $db->getAppointmentById($appointmentId); // Implement this in db.php

if (!$appointment) {
    echo "Appointment not found.";
    exit;
}

// Authorization check (make sure the user has permission to view this appointment)
if ($_SESSION['user_role'] == 'patient' && $appointment['patient_id'] != $_SESSION['user_id']) {
    header("Location: patient/dashboard.php"); // Unauthorized access
    exit;
} elseif ($_SESSION['user_role'] == 'staff') {
    // You might add more checks here to see if the staff member is authorized to view this patient's appointments
}

// Fetch patient and doctor details (you might already have functions for these)
$patient = $db->getUserById($appointment['patient_id']);
$doctor = $db->getDoctorById($appointment['doctor_id']); // Assuming you have this from before

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Appointment - Care Compass Connect</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <!-- Add your custom styles if needed -->
</head>
<body>
    <div class="container">
        <h2>Appointment Details</h2>

        <p><strong>Appointment ID:</strong> <?= $appointment['id'] ?></p>
        <p><strong>Patient Name:</strong> <?= $patient['fullname'] ?></p>
        <p><strong>Doctor Name:</strong> <?= $doctor['fullname'] ?></p>
        <p><strong>Specialty:</strong> <?= $doctor['specialty'] ?></p>
        <p><strong>Date:</strong> <?= $appointment['appointment_date'] ?></p>
        <p><strong>Time:</strong> <?= $appointment['appointment_time'] ?></p>
        <p><strong>Status:</strong> <?= $appointment['status'] ?></p>
        <p><strong>Reason:</strong> <?= $appointment['reason'] ?></p>

        <!-- Actions based on user role and appointment status -->
        <?php if ($_SESSION['user_role'] == 'staff'): ?>
            <?php if ($appointment['status'] == 'pending'): ?>
                <a href="confirm_appointment.php?id=<?= $appointment['id'] ?>" class="btn btn-success">Confirm</a>
                <a href="cancel_appointment.php?id=<?= $appointment['id'] ?>" class="btn btn-danger">Cancel</a>
            <?php endif; ?>
        <?php elseif ($_SESSION['user_role'] == 'patient'): ?>
            <?php if ($appointment['status'] == 'pending'): ?>
                <a href="cancel_appointment.php?id=<?= $appointment['id'] ?>" class="btn btn-danger">Cancel</a>
            <?php endif; ?>
        <?php endif; ?>

        <a href="<?= ($_SESSION['user_role'] == 'patient') ? 'patient/dashboard.php' : 'staff/dashboard.php' ?>" class="btn btn-secondary">Back to Dashboard</a>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>