<?php
// Include necessary files
include('../includes/config.php');
include('../includes/db.php');
include('../includes/functions.php');

// Start session (if not already started)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

$db = new Database();

// Get admin information
$admin = $db->getAdminById($_SESSION['user_id']); // Assuming you have this function

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: ../index.php");
    exit;
}

// Fetch all users
$users = $db->getAllUsers(); // You have this function

// Fetch all doctors
$doctors = $db->getAllDoctors(); // Implement this function in db.php
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Care Compass Connect</title>
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
        .dashboard-card-header h3 {
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
        .btn-outline-primary {
            color: #046A7A;
            border-color: #046A7A;
            transition: background-color 0.2s ease-in-out, border-color 0.2s ease-in-out, color 0.2s ease-in-out;
        }
        .btn-outline-primary:hover, .btn-outline-primary:focus {
            background-color: #cce8ed; /* Light tint */
            border-color: #046A7A;
            color: #034e5a;
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
            padding: 0.75rem 0;
            border-bottom: 1px solid #e9ecef;
        }
        .table td:last-child {
            text-align: right;
        }
        .text-muted {
            font-size: 0.9rem;
        }
        footer {
            background-color: #f8f9fa;
            padding: 1.5rem 0;
            border-top: 1px solid #e9ecef;
        }
    </style>
</head>
<body>
    <header class="dashboard-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2>Welcome, <?= htmlspecialchars($admin['fullname']) ?>!</h2>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="?logout=true" class="btn btn-light">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <main class="container">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <h3 class="mb-0"><i class="bi bi-people me-2"></i>User Management</h3>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2 mb-3">
                            <a href="manage_users.php" class="btn btn-primary">Manage Users</a>
                        </div>
                        <?php if ($users): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($users as $user): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($user['fullname']) ?></td>
                                                <td><?= htmlspecialchars($user['email']) ?></td>
                                                <td><?= htmlspecialchars($user['user_type']) ?></td>
                                                <td class="text-end">
                                                    <a href="edit_user.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil-square"></i> Edit</a>
                                                    <a href="delete_user.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')"><i class="bi bi-trash"></i> Delete</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">No users found.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <h3 class="mb-0"><i class="bi bi-stethoscope me-2"></i>Doctor Management</h3>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2 mb-3">
                            <a href="manage_doctors.php" class="btn btn-primary">Manage Doctors</a>
                        </div>
                        <?php if ($doctors): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Specialty</th>
                                            <th>Branch</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($doctors as $doctor): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($doctor['fullname']) ?></td>
                                                <td><?= htmlspecialchars($doctor['specialty'] ?? 'N/A') ?></td>
                                                <td><?= htmlspecialchars($doctor['branch_name'] ?: 'N/A') ?></td>
                                                <td class="text-end">
                                                    <a href="edit_doctor.php?id=<?= $doctor['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil-square"></i> Edit</a>
                                                    <a href="delete_doctor.php?id=<?= $doctor['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')"><i class="bi bi-trash"></i> Delete</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">No doctors found.</p>
                        <?php endif; ?>
                        <div class="mt-3">
                            <a href="doctor_search.php" class="btn btn-outline-primary">Search Doctors</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <h3 class="mb-0"><i class="bi bi-gear me-2"></i>System Settings</h3>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="system_settings.php" class="btn btn-secondary">Manage System Settings</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <h3 class="mb-0"><i class="bi bi-question-circle me-2"></i>Queries</h3>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="view_queries.php" class="btn btn-info">View Queries</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="mt-5 py-3 text-center text-muted">
        <div class="container">
            <p>© 2023 Care Compass Connect. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>