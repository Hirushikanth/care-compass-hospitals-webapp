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

// Generate CSRF Token
$csrf_token = generate_csrf_token();

// Handle updating query status
if (isset($_POST['update_status'])) {
    // Verify CSRF Token
    if (!verify_csrf_token()) {
        die("CSRF token validation failed."); // Or handle the error more gracefully
    }

    $queryId = $_POST['query_id'];
    $newStatus = $_POST['status'];

    $success = $db->updateQueryStatus($queryId, $newStatus); // Implement this in db.php

    if ($success) {
        $_SESSION['success_message'] = 'Query status updated successfully!';
        header("Location: view_queries.php"); // Redirect to refresh the page
        exit();
    } else {
        $_SESSION['error_message'] = 'Error updating status.';
    }
}

// Fetch all queries
$queries = $db->getAllQueries(); // Implement this in db.php
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Queries - Care Compass Connect</title>
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
        }
        .dashboard-header h2 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0;
        }
        .dashboard-card {
            background-color: #fff;
            border-radius: 0.75rem;
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            overflow: hidden;
        }
        .dashboard-card:hover {
            transform: scale(1.01);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.07);
        }
        .dashboard-card-header {
            background-color: #046A7A;
            color: white;
            padding: 1.25rem;
            border-top-left-radius: 0.75rem;
            border-top-right-radius: 0.75rem;
        }
        .dashboard-card-header h2 {
            font-size: 1.5rem;
            margin-bottom: 0;
            font-weight: 600;
        }
        .card-body {
            padding: 1.5rem;
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
            transition: background-color 0.2s ease-in-out, border-color 0.2s ease-in-out;
        }
        .btn-secondary:hover, .btn-secondary:focus {
            background-color: #545b62;
            border-color: #4e555b;
            box-shadow: 0 0 0 0.2rem rgba(108, 117, 125, 0.5);
        }
        .table {
            font-size: 0.95rem;
            margin-bottom: 0;
        }
        .table th {
            font-weight: 600;
            color: #046A7A;
            border-bottom: 2px solid rgba(4, 106, 122, 0.2);
        }
        .table tbody tr:hover {
            background-color: #f8f9fa;
        }
        .table td {
            padding: 0.75rem;
            border-bottom: 1px solid #e9ecef;
            vertical-align: middle;
        }
        .table td:last-child {
            text-align: right;
        }
        .form-select-sm {
            max-width: 150px;
            display: inline-block;
            margin-right: 0.5rem;
        }
        .alert-success {
            background-color: #d1e7dd;
            border-color: #badbcc;
            color: #0f5132;
            padding: 0.75rem 1rem;
            margin-bottom: 1rem;
            border-radius: 0.3rem;
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
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h2 class="mb-0"><i class="bi bi-question-circle me-2"></i>View Queries</h2>
            </div>
            <div class="card-body">
                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success">
                        <?= $_SESSION['success_message'] ?>
                    </div>
                    <?php unset($_SESSION['success_message']); ?>
                <?php endif; ?>
                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-danger">
                        <?= $_SESSION['error_message'] ?>
                    </div>
                    <?php unset($_SESSION['error_message']); ?>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Submitted By</th>
                                <th>Subject</th>
                                <th>Message</th>
                                <th>Status</th>
                                <th>Submitted On</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($queries): ?>
                                <?php foreach ($queries as $query): ?>
                                    <tr>
                                        <td><?= $query['id'] ?></td>
                                        <td><?= htmlspecialchars($query['submitted_by']) ?></td>
                                        <td><?= htmlspecialchars($query['subject']) ?></td>
                                        <td><?= htmlspecialchars(substr($query['message'], 0, 50)) ?>...</td>
                                        <td><?= htmlspecialchars(str_replace('_', ' ', $query['status'])) ?></td>
                                        <td><?= date('F j, Y, g:i a', strtotime($query['created_at'])) ?></td>
                                        <td class="text-end">
                                            <form method="post" class="d-flex justify-content-end align-items-center">
                                                <!-- CSRF Token Field Added Here -->
                                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                                                <input type="hidden" name="query_id" value="<?= $query['id'] ?>">
                                                <select name="status" class="form-select form-select-sm me-2">
                                                    <option value="pending" <?= ($query['status'] == 'pending') ? 'selected' : '' ?>>Pending</option>
                                                    <option value="in_progress" <?= ($query['status'] == 'in_progress') ? 'selected' : '' ?>>In Progress</option>
                                                    <option value="resolved" <?= ($query['status'] == 'resolved') ? 'selected' : '' ?>>Resolved</option>
                                                </select>
                                                <button type="submit" name="update_status" class="btn btn-sm btn-primary">Update</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">No queries found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    <a href="dashboard.php" class="btn btn-secondary"><i class="bi bi-arrow-left me-1"></i> Back to Dashboard</a>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>