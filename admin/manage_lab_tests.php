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

// Generate CSRF token
$csrf_token = generate_csrf_token();

$success_message = ""; // Initialize success message
$error_message = "";   // Initialize error message
$errors = [];          // Initialize errors array

// Handle form submissions for adding, editing, and deleting lab tests
if (isset($_POST['add_test'])) {
    // Verify CSRF token
    if (!verify_csrf_token()) {
        die("CSRF token validation failed."); // Or handle error more gracefully
    }

    $testName = sanitize_input($_POST['name']);
    $testDescription = sanitize_input($_POST['description']);
    $testCost = $_POST['cost']; // Do not sanitize cost yet, validate numerically

    // Server-side validation
    if (empty($testName)) {
        $errors[] = "Test Name is required.";
    }
    if (!is_numeric($testCost)) {
        $errors[] = "Cost must be a numeric value.";
    }

    if (empty($errors)) {
        $success = $db->createLabTest($testName, $testDescription, $testCost); // Implement this in db.php

        if ($success) {
            $success_message = 'Lab test added successfully!';
        } else {
            $error_message = 'Error adding lab test to database.'; // More specific error message
        }
    }
} elseif (isset($_POST['edit_test'])) {
    // ... (Handle lab test editing - you'll need to implement this in edit_lab_test.php) ...
} elseif (isset($_POST['delete_test'])) {
    // ... (Handle lab test deletion - you'll need to implement this in delete_lab_test.php) ...
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include('../includes/header.php'); ?>

    <div class="container">
        <h2>Manage Lab Tests</h2>

        <?php if ($success_message): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Add Lab Test Form -->
        <div class="card mb-3">
            <div class="card-header">Add New Lab Test</div>
            <div class="card-body">
                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
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
                <div class="table-responsive">
                    <table class="table table-hover">
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
                            <?php if ($labTests): ?>
                                <?php foreach ($labTests as $test): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($test['id']) ?></td>
                                        <td><?= htmlspecialchars($test['name']) ?></td>
                                        <td><?= htmlspecialchars($test['description']) ?></td>
                                        <td><?= htmlspecialchars($test['cost']) ?></td>
                                        <td>
                                            <a href="edit_lab_test.php?id=<?= $test['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                            <a href="delete_lab_test.php?id=<?= $test['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">No lab tests available.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <a href="dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
    </div>

    <?php include('../includes/footer.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>
</body>
</html>