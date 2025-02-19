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

// Get user ID from the URL
$userId = isset($_GET['id']) ? $_GET['id'] : null;

if (!$userId) {
    echo "User ID not provided.";
    exit;
}

// Handle user deletion
if (isset($_POST['delete_user'])) {
    $success = $db->deleteUser($userId); // Implement this in db.php

    if ($success) {
        header("Location: manage_users.php?delete_success=1");
        exit;
    } else {
        $error_message = "Error deleting user.";
    }
}

// Fetch user data (for confirmation)
$user = $db->getUserById($userId);

if (!$user) {
    echo "User not found.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete User - Care Compass Connect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
        .confirmation-card {
            background-color: #fff;
            border-radius: 0.5rem;
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.05);
            padding: 1.5rem;
        }
        .confirmation-text {
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
        }
        .user-details p {
            margin-bottom: 0.5rem;
        }
        .user-details strong {
            font-weight: 600;
        }
        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        .btn-danger:hover {
            background-color: #bb2d3b;
            border-color: #b22b36;
        }
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
        }
        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c2c7;
            color: #842029;
            padding: 0.75rem 1rem;
            margin-bottom: 1rem;
            border-radius: 0.3rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h2>Delete User</h2>
        </div>

        <div class="confirmation-card">
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger" role="alert">
                    <?= $error_message ?>
                </div>
            <?php endif; ?>

            <p class="confirmation-text">Are you sure you want to delete the following user?</p>
            <div class="user-details">
                <p><strong>ID:</strong> <?= htmlspecialchars($user['id']) ?></p>
                <p><strong>Full Name:</strong> <?= htmlspecialchars($user['fullname']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
            </div>

            <form method="post" class="mt-3">
                <button type="submit" name="delete_user" class="btn btn-danger">
                    <i class="bi bi-trash me-1"></i> Yes, Delete User
                </button>
                <a href="manage_users.php" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Cancel
                </a>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>