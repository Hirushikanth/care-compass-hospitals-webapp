<?php
// Include necessary files
include('../includes/config.php');
include('../includes/db.php');
include('../includes/functions.php');

// Start session (if not already started)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in and is a patient
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'patient') {
    header("Location: ../login.php"); // Redirect to login page
    exit;
}

$db = new Database();

// Get patient information
$patient = $db->getPatientById($_SESSION['user_id']);

// Fetch upcoming appointments for the patient
$appointments = $db->getUpcomingAppointmentsByPatientId($_SESSION['user_id']);

// Fetch medical records for the patient
$medicalRecords = $db->getMedicalRecordsByPatientId($_SESSION['user_id']);

// Fetch lab test results for the patient
$labTestResults = $db->getLabTestResultsByPatientId($_SESSION['user_id']); // You need to implement this

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
    <title>Patient Dashboard - Care Compass Connect</title>
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
            padding: 1rem 0;
            margin-bottom: 2rem;
            text-align: center;
        }
        .dashboard-header h1 {
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
            margin-bottom: 30px;
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
        .update-profile-link {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <header class="dashboard-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1>Welcome, <?= htmlspecialchars($patient['fullname']) ?>!</h1>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="?logout=true" class="btn btn-light">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <main class="container">
        <div class="row">
            <!-- Upcoming Appointments -->
            <div class="col-md-6">
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <h2><i class="bi bi-calendar-event me-2"></i>Upcoming Appointments</h2>
                    </div>
                    <div class="card-body">
                        <?php if ($appointments): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Time</th>
                                            <th>Doctor</th>
                                            <th>Branch</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($appointments as $appointment): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($appointment['appointment_date']) ?></td>
                                                <td><?= htmlspecialchars($appointment['appointment_time']) ?></td>
                                                <td><?= htmlspecialchars($appointment['doctor_name']) ?></td>
                                                <td><?= htmlspecialchars($appointment['branch_name'] ?: 'N/A') ?></td>
                                                <td><?= htmlspecialchars($appointment['status']) ?></td>
                                                <td>
                                                    <a href="view_appointment.php?id=<?= $appointment['id'] ?>" class="btn btn-sm btn-outline-primary">View</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">You have no upcoming appointments.</p>
                        <?php endif; ?>
                        <div class="mt-3">
                            <a href="../book_appointment.php" class="btn btn-primary">Book New Appointment</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Medical Records -->
            <div class="col-md-6">
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <h2 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>Medical Records</h2>
                    </div>
                    <div class="card-body">
                        <?php if ($medicalRecords): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Doctor</th>
                                            <th>Diagnosis</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($medicalRecords as $record): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($record['visit_date']) ?></td>
                                                <td><?= htmlspecialchars($record['doctor_name']) ?></td>
                                                <td><?= htmlspecialchars($record['diagnosis']) ?></td>
                                                <td>
                                                    <a href="../view_record.php?id=<?= $record['id'] ?>" class="btn btn-sm btn-outline-primary">View</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">You have no medical records yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Test Results -->
            <div class="col-md-6">
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <h2 class="mb-0"><i class="bi bi-clipboard-data me-2"></i>Test Results</h2>
                    </div>
                    <div class="card-body">
                        <?php if ($labTestResults): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Test Name</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($labTestResults as $result): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($result['test_name']) ?></td>
                                                <td><?= htmlspecialchars($result['result_date']) ?></td>
                                                <td><?= htmlspecialchars($result['status']) ?></td>
                                                <td>
                                                    <a href="../view_test_results.php?id=<?= $result['id'] ?>" class="btn btn-sm btn-outline-primary">View</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">You have no lab test results yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Update Profile Link -->
            <div class="col-md-6">
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <h2 class="mb-0"><i class="bi bi-person-fill me-2"></i>Profile Management</h2>
                    </div>
                    <div class="card-body">
                        <p>Update your profile information here.</p>
                        <a href="update_profile.php" class="btn btn-outline-primary update-profile-link">Update Profile</a>
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