<?php
// Include necessary files
include('includes/config.php');
include('includes/db.php');
include('includes/functions.php');

// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in and is a staff member
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'staff') {
    header("Location: login.php");
    exit;
}

$db = new Database();

// Generate CSRF token BEFORE displaying the form
$csrf_token = generate_csrf_token();

// Handle form submission
if (isset($_POST['update_result'])) {
    // Verify CSRF token at the VERY BEGINNING of form processing
    if (!verify_csrf_token()) {
        // CSRF token verification failed!  Reject the request.
        die("CSRF token validation failed."); // Or display a user-friendly error message and exit.
    }

    $testResultId = $_POST['test_result_id'];
    $resultDetails = sanitize_input($_POST['result_details']);
    $status = $_POST['status'];

    $success = $db->updateTestResult($testResultId, $resultDetails, $status); // Implement in db.php

    if ($success) {
        echo '<div class="alert alert-success">Test result updated successfully!</div>';
    } else {
        echo '<div class="alert alert-danger">Error updating test result.</div>';
    }
}

// Get test result details based on ID in the URL
if (isset($_GET['id'])) {
    $testResultId = $_GET['id'];
    $testResult = $db->getTestResultById($testResultId); // Implement in db.php

    if (!$testResult) {
        echo '<div class="alert alert-danger">Test result not found.</div>';
        exit;
    }
} else {
    echo '<div class="alert alert-danger">Test result ID not provided.</div>';
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Test Result - Care Compass Connect</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <!-- Add your custom styles if needed -->
</head>
<body>
    <div class="container">
        <h2>Update Test Result</h2>

        <form method="post">
            <!-- ADD THIS HIDDEN INPUT FIELD for CSRF token -->
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            <input type="hidden" name="test_result_id" value="<?= $testResult['id'] ?>">

            <div class="mb-3">
                <label class="form-label">Patient:</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($testResult['patient_name']) ?>" readonly>
            </div>
            <div class="mb-3">
                <label class="form-label">Test Name:</label>
                <input type="text" class="form-control" value="<?= $testResult['test_name'] ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="result_details" class="form-label">Result Details:</label>
                <textarea class="form-control" id="result_details" name="result_details"><?= htmlspecialchars($testResult['result_details']) ?></textarea>
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">Status:</label>
                <select class="form-control" id="status" name="status">
                    <option value="pending" <?= ($testResult['status'] == 'pending') ? 'selected' : '' ?>>Pending</option>
                    <option value="completed" <?= ($testResult['status'] == 'completed') ? 'selected' : '' ?>>Completed</option>
                </select>
            </div>
            <button type="submit" name="update_result" class="btn btn-primary">Update Result</button>
        </form>

        <a href="staff/dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
    </div>

    <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>