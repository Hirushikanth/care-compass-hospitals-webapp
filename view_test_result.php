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

// Get test result ID and patient ID from the URL
$testResultId = isset($_GET['id']) ? $_GET['id'] : null;
$patientId = isset($_GET['patient_id']) ? $_GET['patient_id'] : null; // Get patient ID for "Back" link

// Handle missing test result ID
if (!$testResultId) {
    echo '<div class="alert alert-danger">Test result ID not provided.</div>';
    exit;
}

// Get the test result details
$testResult = $db->getTestResultById($testResultId);

// Handle test result not found
if (!$testResult) {
    echo '<div class="alert alert-danger">Test result not found.</div>';
    exit;
}

// Authorization check: Make sure the logged-in user has access to this record
if ($_SESSION['user_role'] == 'patient' && $testResult['patient_id'] != $_SESSION['user_id']) {
    header("Location: patient/dashboard.php"); // Unauthorized access
    exit;
} elseif ($_SESSION['user_role'] == 'staff') {
    // You might want additional checks here for staff authorization
    if (!$patientId) {
        echo '<div class="alert alert-danger">Patient ID not provided for staff view.</div>';
        exit;
    }
}

// Fetch patient details
$patient = $db->getPatientById($testResult['patient_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Test Result - Care Compass Connect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #f0f8ff; /* Light background */
            font-family: 'Nunito', sans-serif;
            color: #343a40;
        }
        .view-header {
            background-color: #046A7A; /* Dark teal */
            color: white;
            padding: 1rem 0;
            margin-bottom: 2rem;
            text-align: center;
        }
        .view-header h2 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0;
        }
        .test-result-card {
            background-color: #fff;
            border-radius: 0.75rem;
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            overflow: hidden;
            max-width: 700px;
            margin: auto;
        }
        .test-result-card:hover {
            transform: scale(1.01);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.07);
        }
        .card-body {
            padding: 1.5rem;
        }
        .result-detail {
            margin-bottom: 1rem;
        }
        .result-label {
            font-weight: 600;
            color: #046A7A; /* Dark teal for labels */
        }
        .result-value {
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
            transition: background-color 0.2s ease-in-out, border-color 0.2s ease-in-out;
        }
        .btn-secondary:hover, .btn-secondary:focus {
            background-color: #5a6268;
            border-color: #545b62;
            box-shadow: 0 0 0 0.2rem rgba(108, 117, 125, 0.5);
        }
        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c2c7;
            color: #842029;
            border-radius: 0.3rem;
            padding: 0.75rem 1rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <header class="view-header">
        <div class="container">
            <h2>Test Result Details</h2>
        </div>
    </header>

    <main class="container">
        <div class="test-result-card">
            <div class="card-body">
                <?php if (isset($patient['fullname'])): ?>
                    <div class="result-detail">
                        <p class="result-label">Patient Name:</p>
                        <p class="result-value"><?= htmlspecialchars($patient['fullname']) ?></p>
                    </div>
                <?php endif; ?>

                <div class="result-detail">
                    <p class="result-label">Test Name:</p>
                    <p class="result-value"><?= htmlspecialchars($testResult['test_name']) ?></p>
                </div>

                <div class="result-detail">
                    <p class="result-label">Result Date:</p>
                    <p class="result-value"><?= htmlspecialchars($testResult['result_date']) ?></p>
                </div>

                <div class="result-detail">
                    <p class="result-label">Status:</p>
                    <p class="result-value"><?= htmlspecialchars($testResult['status']) ?></p>
                </div>

                <div class="result-detail">
                    <p class="result-label">Result Details:</p>
                    <p class="result-value"><?= nl2br(htmlspecialchars($testResult['result_details'])) ?></p>
                </div>

                <!-- Back Button -->
                <div class="mt-4">
                    <?php if ($_SESSION['user_role'] == 'staff' && $patientId): ?>
                        <a href="view_patient.php?id=<?= $patientId ?>" class="btn btn-secondary">Back to Patient</a>
                    <?php elseif ($_SESSION['user_role'] == 'patient'): ?>
                        <a href="patient/dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
                    <?php else: ?>
                        <a href="staff/dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>