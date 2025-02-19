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

// Get record ID and patient ID from the URL
$recordId = isset($_GET['id']) ? $_GET['id'] : null;
$patientId = isset($_GET['patient_id']) ? $_GET['patient_id'] : null; // Get patient ID for "Back" link

// Handle missing record ID
if (!$recordId) {
    echo '<div class="alert alert-danger">Record ID not provided.</div>';
    exit;
}

// Get the medical record details
$record = $db->getMedicalRecordById($recordId);

// Handle record not found
if (!$record) {
    echo '<div class="alert alert-danger">Medical record not found.</div>';
    exit;
}

// Authorization check: Make sure the logged-in user has access to this record
if ($_SESSION['user_role'] == 'patient' && $record['patient_id'] != $_SESSION['user_id']) {
    header("Location: patient/dashboard.php"); // Unauthorized access
    exit;
} elseif ($_SESSION['user_role'] == 'staff') {
    // You might want additional checks here to see if the staff member 
    // is authorized to view this patient's records based on your 
    // specific authorization rules (e.g., department, assigned patients)
    if (!$patientId) {
        echo '<div class="alert alert-danger">Patient ID not provided for staff view.</div>';
        exit;
    }
}

// Fetch patient and doctor details for display
$patient = $db->getPatientById($record['patient_id']); // Assuming you have this function
$doctor = $db->getDoctorById($record['doctor_id']); // Assuming you have this function

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
            <p><strong>Patient Name:</strong> <?= isset($patient['fullname']) ? $patient['fullname'] : 'N/A' ?></p>
            <p><strong>Doctor Name:</strong> <?= isset($doctor['fullname']) ? $doctor['fullname'] : 'N/A' ?></p>
            <p><strong>Visit Date:</strong> <?= $record['visit_date'] ?></p>
            <p><strong>Diagnosis:</strong> <?= $record['diagnosis'] ?></p>
            <p><strong>Prescription:</strong> <?= $record['prescription'] ?></p>
            <p><strong>Notes:</strong> <?= $record['notes'] ?></p>

            <!-- Add more details as needed -->
        <?php else: ?>
            <p>Medical record not found.</p>
        <?php endif; ?>

        <!-- "Back" link with patient ID for staff -->
        <?php if ($_SESSION['user_role'] == 'staff' && $patientId): ?>
            <a href="view_patient.php?id=<?= $patientId ?>" class="btn btn-secondary">Back to Patient</a>
        <?php else: ?>
            <a href="<?php echo ($_SESSION['user_role'] == 'patient') ? 'patient/dashboard.php' : 'staff/dashboard.php'; ?>" class="btn btn-secondary">Back to Dashboard</a>
        <?php endif; ?>
    </div>

    <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>