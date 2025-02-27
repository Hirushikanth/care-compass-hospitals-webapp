<?php
// Include necessary files
include('includes/config.php');
include('includes/db.php');
include('includes/functions.php');

// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

$db = new Database();

// Get lab test ID from the URL
$labTestId = isset($_GET['id']) ? $_GET['id'] : null;

if (!$labTestId) {
    echo "Lab test ID not provided.";
    exit;
}

// Fetch lab test data
$labTest = $db->getLabTestById($labTestId); // Implement this function in db.php

if (!$labTest) {
    echo "Lab test not found.";
    exit;
}

// Handle form submission
if (isset($_POST['edit_lab_test'])) {
    $testName = sanitize_input($_POST['name']);
    $testDescription = sanitize_input($_POST['description']);
    $testCost = sanitize_input($_POST['cost']);

    $success = $db->updateLabTest($labTestId, $testName, $testDescription, $testCost); // Implement this in db.php

    if ($success) {
        echo '<div class="alert alert-success">Lab test updated successfully!</div>';
        // Refetch lab test data to update the form
        $labTest = $db->getLabTestById($labTestId);
    } else {
        echo '<div class="alert alert-danger">Error updating lab test.</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Lab Test - Care Compass Connect</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
    <!-- Add your custom styles if needed -->
</head>
<body>
    <div class="container">
        <h2>Edit Lab Test</h2>

        <form method="post">
            <div class="mb-3">
                <label for="name" class="form-label">Test Name:</label>
                <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($labTest['name']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description:</label>
                <textarea class="form-control" id="description" name="description"><?= htmlspecialchars($labTest['description']) ?></textarea>
            </div>
            <div class="mb-3">
                <label for="cost" class="form-label">Cost:</label>
                <input type="number" class="form-control" id="cost" name="cost" step="0.01" value="<?= htmlspecialchars($labTest['cost']) ?>" required>
            </div>
            <button type="submit" name="edit_lab_test" class="btn btn-primary">Update Lab Test</button>
        </form>

        <a href="manage_lab_tests.php" class="btn btn-secondary mt-3">Back to Manage Lab Tests</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>
</body>
</html>