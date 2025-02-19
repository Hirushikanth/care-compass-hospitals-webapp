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

// Handle form submissions for adding, editing, and deleting lab tests
if (isset($_POST['add_test'])) {
    $testName = sanitize_input($_POST['name']);
    $testDescription = sanitize_input($_POST['description']);
    $testCost = sanitize_input($_POST['cost']);

    $success = $db->createLabTest($testName, $testDescription, $testCost); // Implement this in db.php

    if ($success) {
        echo '<div class="alert alert-success">Lab test added successfully!</div>';
    } else {
        echo '<div class="alert alert-danger">Error adding lab test.</div>';
    }
} elseif (isset($_POST['edit_test'])) {
    // ... Handle lab test editing (similar to adding)
} elseif (isset($_POST['delete_test'])) {
    // ... Handle lab test deletion (similar to adding)
}

// Fetch all lab tests
$labTests = $db->getAllLabTests(); // Implement this in db.php
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Lab Tests - Care Compass Connect</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <!-- Add your custom styles if needed -->
</head>
<body>
    <div class="container">
        <h2>Manage Lab Tests</h2>

        <!-- Add Lab Test Form -->
        <div class="card mb-3">
            <div class="card-header">Add New Lab Test</div>
            <div class="card-body">
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
                    <button type="submit" name="add_test" class="btn btn-primary">Add Test</button>
                </form>
            </div>
        </div>

        <!-- List of Lab Tests -->
        <div class="card">
            <div class="card-header">Available Lab Tests</div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Cost</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($labTests as $test): ?>
                            <tr>
                                <td><?= $test['id'] ?></td>
                                <td><?= $test['name'] ?></td>
                                <td><?= $test['description'] ?></td>
                                <td><?= $test['cost'] ?></td>
                                <td>
                                    <a href="edit_lab_test.php?id=<?= $test['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <a href="delete_lab_test.php?id=<?= $test['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <a href="admin/dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>