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

// Get branch ID from the URL
$branchId = isset($_GET['id']) ? $_GET['id'] : null;

if (!$branchId) {
    echo "Branch ID not provided.";
    exit;
}

// Fetch branch data (for confirmation)
$branch = $db->getBranchById($branchId);

if (!$branch) {
    $_SESSION['error_message'] = 'Branch not found.';
    header("Location: manage_branches.php");
    exit;
}

// Generate CSRF token BEFORE displaying the form
$csrf_token = generate_csrf_token();

// Handle branch deletion
if (isset($_POST['delete_branch'])) {
    // Verify CSRF token at the VERY BEGINNING of form processing
    if (!verify_csrf_token()) {
        // CSRF token verification failed!  Reject the request.
        die("CSRF token validation failed."); // Or display a user-friendly error message and exit.
    }

    $success = $db->deleteBranch($branchId); // Implement this in db.php

    if ($success) {
        $_SESSION['success_message'] = 'Branch deleted successfully!';
        header("Location: manage_branches.php");
        exit;
    } else {
        $_SESSION['error_message'] = 'Error deleting branch.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Branch - Care Compass Connect Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css"> <!-- Link to your main stylesheet -->
</head>
<body>
    <?php include('../includes/header.php'); ?>

    <div class="container py-5">
        <h2 class="mb-4">Delete Branch</h2>

        <div class="card shadow-sm">
            <div class="card-body">
                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error_message']) ?></div>
                    <?php unset($_SESSION['error_message']); ?>
                <?php endif; ?>

                <p>Are you sure you want to delete the following branch?</p>
                <ul class="list-group mb-3">
                    <li class="list-group-item"><strong>ID:</strong> <?= htmlspecialchars($branch['id']) ?></li>
                    <li class="list-group-item"><strong>Name:</strong> <?= htmlspecialchars($branch['name']) ?></li>
                    <li class="list-group-item"><strong>City:</strong> <?= htmlspecialchars($branch['city'] ?: 'N/A') ?></li>
                    <li class="list-group-item"><strong>Phone:</strong> <?= htmlspecialchars($branch['phone'] ?: 'N/A') ?></li>
                    <li class="list-group-item"><strong>Address:</strong><br><?= nl2br(htmlspecialchars($branch['address'] ?: 'N/A')) ?></li>
                </ul>

                <form method="post">
                    <!-- ADD THIS HIDDEN INPUT FIELD for CSRF token -->
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                    <button type="submit" name="delete_branch" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i> Yes, Delete Branch
                    </button>
                    <a href="manage_branches.php" class="btn btn-secondary ms-2">
                        <i class="bi bi-arrow-left me-1"></i> Cancel
                    </a>
                </form>
            </div>
        </div>

        <div class="mt-4">
            <a href="manage_branches.php" class="btn btn-secondary"><i class="bi bi-arrow-left me-2"></i> Back to Manage Branches</a>
        </div>
    </div>

    <?php include('../includes/footer.php'); ?>

    <script src="tps://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>
</body>
</html>