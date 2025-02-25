<?php
// Include necessary files
include('includes/config.php');
include('includes/db.php');
include('includes/functions.php');

// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in and is a patient
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'patient') {
    header("Location: login.php");
    exit;
}

$db = new Database();

// Get appointment ID from URL parameter
$appointmentId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($appointmentId <= 0) {
    header("Location: dashboard.php"); // Redirect to patient dashboard if invalid ID
    exit;
}

// Fetch appointment details including price
$appointment = $db->getAppointmentById($appointmentId);

if (!$appointment) {
    echo "Appointment not found.";
    exit;
}

// Authorization check: Ensure patient is viewing their own bill
if ($appointment['patient_id'] != $_SESSION['user_id']) {
    header("Location: dashboard.php"); // Unauthorized access
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Bill - Care Compass Connect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include('../includes/header.php'); ?>

    <div class="container py-5">
        <h2>Appointment Bill</h2>

        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="card-title">Bill Information</h4>
                <dl class="row">
                    <dt class="col-sm-3">Bill ID:</dt>
                    <dd class="col-sm-9"><?= htmlspecialchars($appointment['id']) ?></dd>

                    <dt class="col-sm-3">Patient Name:</dt>
                    <dd class="col-sm-9"><?= htmlspecialchars($appointment['patient_name']) ?></dd>

                    <dt class="col-sm-3">Doctor Name:</dt>
                    <dd class="col-sm-9"><?= htmlspecialchars($appointment['doctor_name']) ?></dd>

                    <dt class="col-sm-3">Appointment Date:</dt>
                    <dd class="col-sm-9"><?= htmlspecialchars($appointment['appointment_date']) ?></dd>

                    <dt class="col-sm-3">Appointment Time:</dt>
                    <dd class="col-sm-9"><?= htmlspecialchars($appointment['appointment_time']) ?></dd>

                    <dt class="col-sm-3">Service:</dt>
                    <dd class="col-sm-9">Consultation</dd>  <!-- You can make service dynamic later if needed -->

                    <dt class="col-sm-3">Price:</dt>
                    <dd class="col-sm-9">$<?= number_format($appointment['price'], 2) ?></dd>
                </dl>

                <div class="mt-4">
                    <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
                </div>
            </div>
        </div>
    </div>

    <?php include('includes/footer.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>
</body>
</html>