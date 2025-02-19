<?php
// Include necessary files
include('../includes/config.php');
include('../includes/db.php');
include('../includes/functions.php');

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

// Handle form submission
if (isset($_POST['add_lab_test'])) {
    $testName = sanitize_input($_POST['name']);
    $testDescription = sanitize_input($_POST['description']);
    $testCost = sanitize_input($_POST['cost']);

    $success = $db->createLabTest($testName, $testDescription, $testCost); // You implemented this earlier

    if ($success) {
        echo '<div class="alert alert-success">Lab test added successfully!</div>';
    } else {
        echo '<div class="alert alert-danger">Error adding lab test.</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Lab Test - Care Compass Connect</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <!-- Add your custom styles if needed -->
</head>
<body>
    <div class="container">
        <h2>Add Lab Test</h2>

        <form method="post">
            <div class="mb-3">
                <label for="name" class="form-label">Test Name:</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description:</label>
                <textarea class="form-control" id="description" name="description"></textarea>
            </div>
            <div class="mb-3">
                <label for="cost" class="form-label">Cost:</label>
                <input type="number" class="form-control" id="cost" name="cost" step="0.01" required>
            </div>

            <button type="submit" name="add_lab_test" class="btn btn-primary">Add Lab Test</button>
        </form>

        <a href="manage_lab_tests.php" class="btn btn-secondary mt-3">Back to Manage Lab Tests</a>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>