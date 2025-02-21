<?php
// Include necessary files
include('../includes/config.php');
include('../includes/db.php');
include('../includes/functions.php');

// Start session (if not already started)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in and is a staff member
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'staff') {
    header("Location: ../login.php");
    exit;
}

$db = new Database();

// Get staff information
$staff = $db->getStaffById($_SESSION['user_id']); // Assuming you have this function

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
    <title>Staff Dashboard - Care Compass Connect</title>
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
        .btn-outline-secondary {
            color: #6c757d;
            border-color: #6c757d;
            transition: background-color 0.2s ease-in-out, border-color 0.2s ease-in-out, color 0.2s ease-in-out;
        }
        .btn-outline-secondary:hover, .btn-outline-secondary:focus {
            background-color: #e2e3e5;
            border-color: #6c757d;
        }
        .btn-info {
            background-color: #046A7A;
            border-color: #046A7A;
            color: white;
            transition: background-color 0.2s ease-in-out, border-color 0.2s ease-in-out;
        }
        .btn-info:hover, .btn-info:focus {
            background-color: #034e5a;
            border-color: #034e5a;
            box-shadow: 0 0 0 0.2rem rgba(4, 106, 122, 0.5);
        }
        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
            transition: background-color 0.2s ease-in-out, border-color 0.2s ease-in-out;
        }
        .btn-danger:hover, .btn-danger:focus {
            background-color: #bb2d3b;
            border-color: #b2242f;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.5);
        }
        .btn-warning {
            background-color: #ffc107;
            border-color: #ffc107;
            transition: background-color 0.2s ease-in-out, border-color 0.2s ease-in-out;
        }
        .btn-warning:hover, .btn-warning:focus {
            background-color: #e0a800;
            border-color: #d39e00;
            box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.5);
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
        .list-group-item a {
            color: #046A7A;
            text-decoration: none;
            transition: color 0.2s ease-in-out;
        }
        .list-group-item a:hover {
            text-decoration: underline;
            color: #034e5a;
        }
        footer {
            background-color: #f8f9fa;
            padding: 1.5rem 0;
            border-top: 1px solid #e9ecef;
            text-align: center;
        }
    </style>
</head>
<body>
    <header class="dashboard-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2>Welcome, <?= htmlspecialchars($staff['fullname']) ?>!</h2>
                    <?php if ($staff['branch_name']): ?>  <!-- Conditionally display branch name -->
                        <p class="text-muted">Branch: <?= htmlspecialchars($staff['branch_name']) ?></p>
                    <?php endif; ?>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="?logout=true" class="btn btn-light">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <main class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <h3 class="mb-0"><i class="bi bi-plus-square me-2"></i> Book Appointment</h3>
                    </div>
                    <div class="card-body text-center">
                        <a href="../book_appointment.php" class="btn btn-primary btn-lg"><i class="bi bi-calendar-plus me-1"></i> Book Now</a>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <h3 class="mb-0"><i class="bi bi-calendar-day me-2"></i> Today's Appointments</h3>
                    </div>
                    <div class="card-body">
                        <?php
                        $appointments = $db->getAppointmentsForToday();
                        if ($appointments):
                        ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Time</th>
                                            <th>Patient</th>
                                            <th>Doctor</th>
                                            <th>Status</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($appointments as $appointment): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($appointment['appointment_time']) ?></td>
                                                <td><?= htmlspecialchars($appointment['patient_name']) ?></td>
                                                <td><?= htmlspecialchars($appointment['doctor_name']) ?></td>
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

            <div class="col-md-6">
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <h3 class="mb-0"><i class="bi bi-clock-history me-2"></i> Pending Test Results</h3>
                    </div>
                    <div class="card-body">
                        <?php
                        $pendingTests = $db->getPendingTestResults();
                        if ($pendingTests):
                        ?>
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
                                                <td><?= $test['id'] ?></td>
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

            <div class="col-md-6">
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <h3 class="mb-0"><i class="bi bi-search me-2"></i> Patient Search</h3>
                    </div>
                    <div class="card-body">
                        <form method="get" action="patient_search.php">
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" name="search_query" placeholder="Search by name or ID">
                                <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
                            </div>
                        </form>
                        <?php
                        if (isset($_GET['search_query'])):
                            $searchQuery = sanitize_input($_GET['search_query']);
                            $patients = $db->searchPatients($searchQuery);
                            if ($patients):
                            ?>
                                <ul class="list-group mt-3">
                                    <?php foreach ($patients as $patient): ?>
                                        <li class="list-group-item">
                                            <a href="view_patient.php?id=<?= $patient['id'] ?>"><?= htmlspecialchars($patient['fullname']) ?></a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <p class="text-muted">No patients found matching your query.</p>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <h3 class="mb-0"><i class="bi bi-gear me-2"></i> Quick Actions</h3>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2">
                            <a href="view_all_appointments.php" class="btn btn-primary"><i class="bi bi-calendar-week me-1"></i> View Appointments</a>
                            <a href="add_medical_record.php" class="btn btn-success"><i class="bi bi-file-medical-fill me-1"></i> Add Medical Record</a>
                            <a href="order_lab_test.php" class="btn btn-primary"><i class="bi bi-clipboard-plus me-1"></i> Order Lab Test</a>
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