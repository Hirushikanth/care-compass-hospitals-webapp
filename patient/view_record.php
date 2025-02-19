<?php
// Include necessary files
include('includes/config.php');
include('includes/db.php');
include('includes/functions.php');

// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in (either patient or staff)
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$db = new Database();

// Check if a record ID is provided in the URL
if (!isset($_GET['id'])) {
    header("Location: dashboard.php"); // Redirect if no ID is provided
    exit;
}

$recordId = $_GET['id'];

// Get the medical record details
$record = $db->getMedicalRecordById($recordId); // Implement this function in db.php

// Authorization check: Make sure the logged-in user has access to this record
if ($_SESSION['user_role'] == 'patient' && $record['patient_id'] != $_SESSION['user_id']) {
    header("Location: patient/dashboard.php"); // Unauthorized access
    exit;
} elseif ($_SESSION['user_role'] == 'staff') {
    // You might want additional checks here to see if the staff member is authorized to view this patient's records
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Medical Record - Care Compass Connect</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <!-- Add your custom styles if needed -->
</head>
<body>
    <div class="container">
        <h2>Medical Record Details</h2>

        <?php if ($record): ?>
            <p><strong>Patient Name:</strong> <?= $record['patient_name'] ?></p>
            <p><strong>Doctor Name:</strong> <?= $record['doctor_name'] ?></p>
            <p><strong>Visit Date:</strong> <?= $record['visit_date'] ?></p>
            <p><strong>Diagnosis:</strong> <?= $record['diagnosis'] ?></p>
            <p><strong>Prescription:</strong> <?= $record['prescription'] ?></p>
            <p><strong>Notes:</strong> <?= $record['notes'] ?></p>

            <!-- Add more details as needed -->
        <?php else: ?>
            <p>Medical record not found.</p>
        <?php endif; ?>

        <a href="<?php echo ($_SESSION['user_role'] == 'patient') ? 'patient/dashboard.php' : 'dashboard.php'; ?>" class="btn btn-secondary">Back to Dashboard</a>
    </div>

    <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>