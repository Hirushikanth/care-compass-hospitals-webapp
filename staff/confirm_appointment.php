<?php
// Include necessary files
include('../includes/config.php');
include('../includes/db.php');
include('../includes/functions.php');

// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in and is a staff member
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'staff') {
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
$appointment = $db->getAppointmentById($appointmentId);

if (!$appointment) {
    echo "Appointment not found.";
    exit;
}

// Confirm the appointment
$success = $db->updateAppointmentStatus($appointmentId, 'confirmed'); // Implement this in db.php

if ($success) {
    // Optionally, send a confirmation email/SMS to the patient here
    echo '<div class="alert alert-success">Appointment confirmed successfully!</div>';
} else {
    echo '<div class="alert alert-danger">Error confirming appointment.</div>';
}

// You can add a button/link to go back to the staff dashboard or view the appointment details
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Appointment - Care Compass Connect</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <a href="staff/dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        <!-- Or you could redirect automatically using: -->
        <?php // header("refresh:3;url=staff/dashboard.php"); ?>
    </div>
</body>
</html>