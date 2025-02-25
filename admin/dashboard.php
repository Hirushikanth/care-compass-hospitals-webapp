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
$admin = $db->getAdminById($_SESSION['user_id']);

// Fetch data for Staff Dashboard Features (now included in Admin Dashboard)
$appointmentsToday = $db->getAppointmentsForToday();
$pendingTests = $db->getPendingTestResults();


// Fetch Admin specific data (still needed for Admin Dashboard)
$users = $db->getAllUsers();
$doctors = $db->getAllDoctors();

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: ../index.php");
    exit;
}
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
                    <h2>Welcome, <?= htmlspecialchars($admin['fullname']) ?> (Admin)!</h2>  <!-- Added (Admin) to title -->
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="?logout=true" class="btn btn-light">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <main class="container">
        <div class="row g-4">

            <!-- Combined Quick Actions (includes Staff and Admin actions) -->
            <div class="col-12">
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <h3 class="mb-0"><i class="bi bi-gear me-2"></i> Quick Actions</h3>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2">
                            <!-- Staff Quick Actions -->
                            <a href="../book_appointment.php" class="btn btn-primary"><i class="bi bi-calendar-plus me-1"></i> Book Appointment</a>
                            <a href="add_medical_record.php" class="btn btn-success"><i class="bi bi-file-medical-fill me-1"></i> Add Medical Record</a>
                            <a href="../staff/order_lab_test.php" class="btn btn-primary"><i class="bi bi-clipboard-plus me-1"></i> Order Lab Test</a>
                            <a href="view_feedback.php" class="btn btn-info"><i class="bi bi-question-circle me-1"></i> View Feedback</a> <!-- Staff can also view queries -->
                            <a href="add_branch.php" class="btn btn-outline-primary"><i class="bi bi-building-fill-add me-1"></i> Add Branch</a>
                            <a href="manage_lab_tests.php" class="btn btn-outline-primary"><i class="bi bi-test-tube-fill me-1"></i> Manage Lab Tests</a>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Today's Appointments (Staff Dashboard Feature) -->
            <div class="col-md-8">
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <h3 class="mb-0"><i class="bi bi-calendar-day me-2"></i> Today's Appointments</h3>
                    </div>
                    <div class="card-body">
                        <?php if ($appointmentsToday): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Time</th>
                                            <th>Patient</th>
                                            <th>Doctor</th>
                                            <th>Branch</th>
                                            <th>Status</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($appointmentsToday as $appointment): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($appointment['appointment_time']) ?></td>
                                                <td><?= htmlspecialchars($appointment['patient_name']) ?></td>
                                                <td><?= htmlspecialchars($appointment['doctor_name']) ?></td>
                                                <td><?= htmlspecialchars($appointment['branch_name'] ?: 'N/A') ?></td>
                                                <td><?= htmlspecialchars($appointment['status']) ?></td>
                                                <td class="text-end">
                                                    <a href="view_appointment.php?id=<?= $appointment['id'] ?>" class="btn btn-sm btn-info"><i class="bi bi-eye-fill"></i></a>
                                                    <?php if ($appointment['status'] == 'pending'): ?>
                                                        <a href="confirm_appointment.php?id=<?= $appointment['id'] ?>" class="btn btn-sm btn-primary"><i class="bi bi-check-lg"></i></a>
                                                        <a href="cancel_appointment.php?id=<?= $appointment['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to cancel?')"><i class="bi bi-x-lg"></i></a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">No appointments scheduled for today.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>


            <!-- Pending Test Results (Staff Dashboard Feature) -->
            <div class="col-md-4">
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <h3 class="mb-0"><i class="bi bi-clock-history me-2"></i> Pending Test Results</h3>
                    </div>
                    <div class="card-body">
                        <?php if ($pendingTests): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Patient</th>
                                            <th>Test</th>
                                            <th>Ordered On</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($pendingTests as $test): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($test['id']) ?></td>
                                                <td><?= htmlspecialchars($test['patient_name']) ?></td>
                                                <td><?= htmlspecialchars($test['test_name']) ?></td>
                                                <td><?= htmlspecialchars($test['result_date']) ?></td>
                                                <td class="text-end">
                                                    <a href="update_test_result.php?id=<?= $test['id'] ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil-square"></i></a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">No pending test results.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- User Management (Admin Dashboard Feature) -->
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

            <!-- Doctor Management (Admin Dashboard Feature) -->
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


            <!-- System Settings (Admin Dashboard Feature) -->
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

            <!-- Queries (Admin Dashboard Feature - Staff can also view queries) -->
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