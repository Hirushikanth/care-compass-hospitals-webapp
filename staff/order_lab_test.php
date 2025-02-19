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

// Handle form submission
if (isset($_POST['order_test'])) {
    $patientId = $_POST['patient_id'];
    $testId = $_POST['test_id'];
    $staffId = $_SESSION['user_id']; // Assuming logged-in staff is ordering

    // Server-side validation (in addition to client-side validation)
    $errors = [];
    if (empty($patientId)) {
        $errors[] = "Please select a patient.";
    }
    if (empty($testId)) {
        $errors[] = "Please select a lab test.";
    }
    // Add more server-side validation as needed

    // If no errors, proceed with ordering the lab test
    if (empty($errors)) {
        $success = $db->createTestResult($patientId, $testId, $staffId);

        if ($success) {
            $success_message = 'Lab test ordered successfully!';
        } else {
            $error_message = 'Error ordering lab test.';
        }
    } else {
        // Set error messages to display in the form
        $error_message = '<ul>';
        foreach ($errors as $error) {
            $error_message .= "<li>$error</li>";
        }
        $error_message .= '</ul>';
    }
}

// Get all patients
$patients = $db->getAllPatients();

// Get all lab tests
$labTests = $db->getAllLabTests();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Lab Test - Care Compass Connect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #f0f8ff; /* Light background from the palette */
            font-family: 'Nunito', sans-serif;
            color: #343a40;
        }
        .container {
            margin-top: 3rem;
        }
        .page-header {
            background-color: #046A7A; /* Dark teal from the palette */
            color: white;
            padding: 1.5rem;
            border-radius: 0.5rem 0.5rem 0 0;
            margin-bottom: 1.5rem;
        }
        .page-header h2 {
            font-size: 1.75rem;
            margin-bottom: 0;
            font-weight: 600;
        }
        .card {
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
        }
        .card-body {
            padding: 1.5rem;
        }
        .form-label {
            font-weight: 500;
            margin-bottom: 0.3rem;
        }
        .form-control {
            border-radius: 0.3rem;
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
        }
        .btn-secondary:hover, .btn-secondary:focus {
            background-color: #545b62;
            border-color: #4e555b;
            box-shadow: 0 0 0 0.2rem rgba(108, 117, 125, 0.5);
        }
        .alert {
            border-radius: 0.3rem;
            margin-bottom: 1rem;
        }
        .alert-success {
            background-color: #d1e7dd;
            border-color: #badbcc;
            color: #0f5132;
        }
        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c2c7;
            color: #842029;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h2>Order Lab Test</h2>
        </div>

        <div class="card">
            <div class="card-body">
                <?php if (isset($success_message)): ?>
                    <div class="alert alert-success"><?= $success_message ?></div>
                <?php endif; ?>
                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger"><?= $error_message ?></div>
                <?php endif; ?>
                <form method="post" onsubmit="return validateForm()">
                    <div class="mb-3">
                        <label for="patient_id" class="form-label">Patient:</label>
                        <select class="form-control" id="patient_id" name="patient_id" required>
                            <option value="">-- Select Patient --</option>
                            <?php foreach ($patients as $patient): ?>
                                <option value="<?= $patient['id'] ?>"><?= htmlspecialchars($patient['fullname']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div id="patient-id-error" class="text-danger"></div>
                    </div>
                    <div class="mb-3">
                        <label for="test_id" class="form-label">Lab Test:</label>
                        <select class="form-control" id="test_id" name="test_id" required>
                            <option value="">-- Select Test --</option>
                            <?php foreach ($labTests as $test): ?>
                                <option value="<?= $test['id'] ?>"><?= htmlspecialchars($test['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div id="test-id-error" class="text-danger"></div>
                    </div>
                    <button type="submit" name="order_test" class="btn btn-primary">Order Test</button>
                    <a href="dashboard.php" class="btn btn-secondary ms-2">Back to Dashboard</a>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function validateForm() {
            let isValid = true;
            const patientId = document.getElementById('patient_id').value;
            const testId = document.getElementById('test_id').value;

            // Reset error messages
            document.getElementById('patient-id-error').innerText = '';
            document.getElementById('test-id-error').innerText = '';

            // Patient ID validation
            if (patientId === '') {
                document.getElementById('patient-id-error').innerText = 'Please select a patient.';
                isValid = false;
            }

            // Test ID validation
            if (testId === '') {
                document.getElementById('test-id-error').innerText = 'Please select a lab test.';
                isValid = false;
            }

            return isValid;
        }
    </script>
</body>
</html>