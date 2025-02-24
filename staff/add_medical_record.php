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

// Generate CSRF token BEFORE displaying the form
$csrf_token = generate_csrf_token();

// Handle form submission
if (isset($_POST['add_record'])) {
    // Verify CSRF token at the VERY BEGINNING of form processing
    if (!verify_csrf_token()) {
        // CSRF token verification failed!  Reject the request.
        die("CSRF token validation failed."); // Or display a user-friendly error message and exit.
    }

    $patientId = $_POST['patient_id'];
    $doctorId = $_SESSION['user_id']; // Assuming logged-in staff member is the doctor
    $diagnosis = sanitize_input($_POST['diagnosis']);
    $prescription = sanitize_input($_POST['prescription']);
    $notes = sanitize_input($_POST['notes']);
    $visitDate = $_POST['visit_date'];

    // Validate input (ensure visit date is not in the future, etc.)

    $success = $db->createMedicalRecord($patientId, $doctorId, $diagnosis, $prescription, $notes, $visitDate); // Implement this function in db.php

    if ($success) {
        echo '<div class="alert alert-success">Medical record added successfully!</div>';
    } else {
        echo '<div class="alert alert-danger">Error adding medical record. Please try again.</div>';
    }
}

// Fetch list of patients (for selecting in the form)
$patients = $db->getAllPatients(); // Implement this function in db.php
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Medical Record - Care Compass Connect</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #f0f8ff; /* Light background from the palette */
            font-family: 'Nunito', sans-serif;
            color: #343a40;
        }
        .form-header {
            background-color: #046A7A; /* Dark teal from the palette */
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            text-align: center;
        }
        .form-header h2 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0;
        }
        .form-card {
            background-color: #fff;
            border-radius: 0.75rem;
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.05);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .form-label {
            font-weight: 600;
            margin-bottom: 0.3rem;
            display: block;
        }
        .form-control {
            border-radius: 0.3rem;
        }
        .form-control:focus {
            border-color: #046A7A;
            box-shadow: 0 0 0 0.2rem rgba(4, 106, 122, 0.25);
        }
        .btn-primary {
            background-color: #046A7A;
            border-color: #046A7A;
            transition: background-color 0.2s ease-in-out, border-color 0.2s ease-in-out;
        }
        .btn-primary:hover, .btn-primary:focus {
            background-color: #034e5a;
            border-color: #034e5a;
            box-shadow: 0 0 0 0.2rem rgba(4, 106, 122, 0.5);
        }
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
            transition: background-color 0.2s ease-in-out, border-color 0.2s ease-in-out;
        }
        .btn-secondary:hover, .btn-secondary:focus {
            background-color: #545b62;
            border-color: #4e555b;
            box-shadow: 0 0 0 0.2rem rgba(108, 117, 125, 0.5);
        }
        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
            padding: 0.75rem 1.25rem;
            margin-bottom: 1rem;
            border-radius: 0.3rem;
        }
        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
            padding: 0.75rem 1.25rem;
            margin-bottom: 1rem;
            border-radius: 0.3rem;
        }
    </style>
</head>
<body>
    <header class="form-header">
        <div class="container">
            <h2>Add Medical Record</h2>
        </div>
    </header>

    <div class="container">
        <?php if (isset($_POST['add_record']) && isset($success) && $success): ?>
            <div class="alert alert-success">Medical record added successfully!</div>
        <?php elseif (isset($_POST['add_record']) && isset($success) && !$success): ?>
            <div class="alert alert-danger">Error adding medical record. Please try again.</div>
        <?php endif; ?>

        <div class="form-card">
            <form method="post">
                <!-- CSRF Token Field -->
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

                <div class="mb-3">
                    <label for="patient" class="form-label">Select Patient:</label>
                    <select class="form-control" id="patient" name="patient_id" required>
                        <option value="">-- Select Patient --</option>
                        <?php foreach ($patients as $patient): ?>
                            <option value="<?= $patient['id'] ?>"><?= htmlspecialchars($patient['fullname']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="visit_date" class="form-label">Visit Date:</label>
                    <input type="date" class="form-control" id="visit_date" name="visit_date" required>
                </div>

                <div class="mb-3">
                    <label for="diagnosis" class="form-label">Diagnosis:</label>
                    <textarea class="form-control" id="diagnosis" name="diagnosis" rows="3"></textarea>
                </div>

                <div class="mb-3">
                    <label for="prescription" class="form-label">Prescription:</label>
                    <textarea class="form-control" id="prescription" name="prescription" rows="3"></textarea>
                </div>

                <div class="mb-3">
                    <label for="notes" class="form-label">Notes:</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                </div>

                <button type="submit" name="add_record" class="btn btn-primary">Add Record</button>
                <a href="dashboard.php" class="btn btn-secondary ms-2">Back to Dashboard</a>
            </form>
        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>