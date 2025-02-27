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

// Handle lab test deletion
if (isset($_POST['delete_lab_test'])) {
    $success = $db->deleteLabTest($labTestId); // Implement this in db.php

    if ($success) {
        header("Location: manage_lab_tests.php?delete_success=1");
        exit;
    } else {
        echo '<div class="alert alert-danger">Error deleting lab test.</div>';
    }
}

// Fetch lab test data (for confirmation)
$labTest = $db->getLabTestById($labTestId);

if (!$labTest) {
    echo "Lab test not found.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Lab Test - Care Compass Connect</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
    <!-- Add your custom styles if needed -->
</head>
<body>
    <div class="container">
        <h2>Delete Lab Test</h2>

        <p>Are you sure you want to delete the following lab test?</p>
        <p><strong>ID:</strong> <?= $labTest['id'] ?></p>
        <p><strong>Name:</strong> <?= $labTest['name'] ?></p>
        <p><strong>Description:</strong> <?= $labTest['description'] ?></p>
        <p><strong>Cost:</strong> <?= $labTest['cost'] ?></p>

        <form method="post">
            <button type="submit" name="delete_lab_test" class="btn btn-danger">Yes, Delete Lab Test</button>
            <a href="manage_lab_tests.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>
</body>
</html>