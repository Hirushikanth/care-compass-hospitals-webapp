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

// Get doctor ID from the URL
$doctorId = isset($_GET['id']) ? $_GET['id'] : null;

if (!$doctorId) {
    echo "Doctor ID not provided.";
    exit;
}

// Handle doctor deletion
if (isset($_POST['delete_doctor'])) {
    // Verify CSRF token
    if (!verify_csrf_token()) {
        die("CSRF token validation failed."); // In real app, handle more gracefully
    }

    $success = $db->deleteDoctor($doctorId); // Implement this in db.php

    if ($success) {
        header("Location: manage_doctors.php?delete_success=1");
        exit;
    } else {
        $error_message = "Error deleting doctor.";
    }
}

// Fetch doctor data (for confirmation)
$doctor = $db->getDoctorById($doctorId);

if (!$doctor) {
    echo "Doctor not found.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Doctor - Care Compass Connect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
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
        .dashboard-header {
            background-color: #046A7A; /* Dark teal from the palette */
            color: white;
            padding: 2.5rem 0;
            margin-bottom: 3rem;
            text-align: center;
        }
        .dashboard-header h2 {
            color: white;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0;
        }
        .action-card {
            background-color: #fff;
            border-radius: 0.75rem;
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            overflow: hidden;
        }
        .action-card:hover {
            transform: scale(1.01);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.07);
        }
        .action-card-header {
            background-color: #d9edf7; /* Light blue for header */
            color: #31708f; /* Darker blue for header text */
            padding: 1.25rem;
            border-top-left-radius: 0.75rem;
            border-top-right-radius: 0.75rem;
            border-bottom: 1px solid #bce8f1;
        }
        .action-card-header h3 {
            font-size: 1.25rem;
            margin-bottom: 0;
            font-weight: 600;
        }
        .card-body {
            padding: 1.5rem;
        }
        .btn-danger {
            background-color: #d9534f;
            border-color: #d43f3a;
            transition: background-color 0.2s ease-in-out, border-color 0.2s ease-in-out;
        }
        .btn-danger:hover, .btn-danger:focus {
            background-color: #c9302c;
            border-color: #ac2925;
            box-shadow: 0 0 0 0.2rem rgba(217, 83, 79, 0.5);
        }
        .btn-secondary {
            background-color: #f0f0f0;
            border-color: #ccc;
            color: #333;
            transition: background-color 0.2s ease-in-out, border-color 0.2s ease-in-out;
        }
        .btn-secondary:hover, .btn-secondary:focus {
            background-color: #e0e0e0;
            border-color: #b3b3b3;
            box-shadow: 0 0 0 0.2rem rgba(204, 204, 204, 0.5);
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group strong {
            font-weight: 600;
        }
        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
            padding: 0.75rem 1.25rem;
            margin-bottom: 1rem;
            border-radius: 0.25rem;
        }
    </style>
</head>
<body>
    <header class="dashboard-header">
        <div class="container">
            <h2>Delete Doctor</h2>
        </div>
    </header>

    <main class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="action-card">
                    <div class="action-card-header">
                        <h3 class="mb-0">Confirm Deletion</h3>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error_message)): ?>
                            <div class="alert alert-danger" role="alert">
                                <?= $error_message ?>
                            </div>
                        <?php endif; ?>

                        <p>Are you sure you want to delete the following doctor?</p>
                        <div class="form-group">
                            <strong>ID:</strong> <?= htmlspecialchars($doctor['id']) ?>
                        </div>
                        <div class="form-group">
                            <strong>Full Name:</strong> <?= htmlspecialchars($doctor['fullname']) ?>
                        </div>
                        <div class="form-group">
                            <strong>Specialty:</strong> <?= htmlspecialchars($doctor['specialty']) ?>
                        </div>

                        <form method="post" class="mt-3">
                            <!-- CSRF Token Field -->
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                            <button type="submit" name="delete_doctor" class="btn btn-danger">
                                <i class="bi bi-trash me-1"></i> Yes, Delete Doctor
                            </button>
                            <a href="manage_doctors.php" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-1"></i> Cancel
                            </a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>