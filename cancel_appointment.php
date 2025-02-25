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
$appointment = $db->getAppointmentById($appointmentId);

if (!$appointment) {
    echo "Appointment not found.";
    exit;
}

// Authorization check (similar to view_appointment.php)
// Make sure the user has permission to cancel this appointment
if ($_SESSION['user_role'] == 'patient' && $appointment['patient_id'] != $_SESSION['user_id']) {
    header("Location: patient/dashboard.php"); // Unauthorized access
    exit;
} elseif ($_SESSION['user_role'] == 'staff') {
    // You might add more checks here to see if the staff member is authorized to cancel this patient's appointments
}

// Handle appointment cancellation
if (isset($_POST['cancel_appointment'])) {
    $success = $db->updateAppointmentStatus($appointmentId, 'cancelled');

    if ($success) {
        // Redirect to the appropriate dashboard with a success message
        $redirectUrl = ($_SESSION['user_role'] == 'patient') ? 'patient/dashboard.php' : 'staff/dashboard.php';
        header("Location: " . $redirectUrl . "?cancel_success=1");
        exit;
    } else {
        echo '<div class="alert alert-danger">Error cancelling appointment.</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cancel Appointment - Care Compass Connect</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
    <!-- Add your custom styles if needed -->
</head>
<body>
    <div class="container">
        <h2>Cancel Appointment</h2>

        <?php if (!isset($_POST['cancel_appointment'])): ?>
            <p>Are you sure you want to cancel the following appointment?</p>
            <p><strong>Appointment ID:</strong> <?= $appointment['id'] ?></p>
            <p><strong>Patient Name:</strong> <?= $appointment['patient_name'] ?></p>
            <p><strong>Doctor Name:</strong> <?= $appointment['doctor_name'] ?></p>
            <p><strong>Date:</strong> <?= $appointment['appointment_date'] ?></p>
            <p><strong>Time:</strong> <?= $appointment['appointment_time'] ?></p>

            <form method="post">
                <button type="submit" name="cancel_appointment" class="btn btn-danger">Yes, Cancel Appointment</button>
                <a href="view_appointment.php?id=<?= $appointment['id'] ?><?= (isset($_GET['patient_id'])) ? '&patient_id=' . $_GET['patient_id'] : '' ?>" class="btn btn-secondary">No, Go Back</a>
            </form>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>
</body>
</html>