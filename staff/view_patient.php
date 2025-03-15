<?php
// Include necessary files
include('../includes/config.php');
include('../includes/db.php');
include('../includes/functions.php');

// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in and is a staff member
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'staff') {
    header("Location: ../login.php");
    exit;
}

$db = new Database();

// Get patient ID from the URL
$patientId = isset($_GET['id']) ? $_GET['id'] : null;

if (!$patientId) {
    echo "Patient ID not provided.";
    exit;
}

// Fetch patient details
$patient = $db->getPatientById($patientId); // You might already have this function

if (!$patient) {
    echo "Patient not found.";
    exit;
}

// Fetch patient's medical records
$medicalRecords = $db->getMedicalRecordsByPatientId($patientId);

// Fetch patient's lab test results
$labTestResults = $db->getLabTestResultsByPatientId($patientId); // Implement this in db.php

// Fetch patient's upcoming appointments
$upcomingAppointments = $db->getUpcomingAppointmentsByPatientId($patientId);

// Fetch patient's past appointments
$pastAppointments = $db->getPastAppointmentsByPatientId($patientId); // Implement this in db.php

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Patient - Care Compass Connect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css"> <!-- Link to your main stylesheet -->
    <style>
        body {
            background-color: var(--light-background); /* Light background from palette */
            font-family: 'Nunito', sans-serif;
            color: var(--text-color);
        }
        .view-patient-header {
            background-color: var(--primary-teal); /* Dark teal header */
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            text-align: center;
        }

        .view-patient-header h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0;
        }

        .card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
        }

        .card-header {
            background-color: var(--secondary-light-teal); /* Light teal card header */
            color: var(--primary-teal); /* Dark teal header text */
            font-weight: 600;
            padding: 1rem 1.5rem;
            border-bottom: none;
            border-top-left-radius: 0.75rem;
            border-top-right-radius: 0.75rem;
        }

        .card-body {
            padding: 1.5rem;
        }

        .table {
            margin-bottom: 0; /* Remove default table margin */
        }

        .table thead th {
            background-color: var(--light-background); /* Light background for table header */
            border-bottom: 2px solid var(--border-color);
            font-weight: 600;
        }

        .table tbody td {
            border-bottom: 1px solid var(--border-color);
        }

        .table tbody tr:last-child td {
            border-bottom: none; /* Remove border from last row */
        }

        .btn-info, .btn-success, .btn-warning, .btn-danger, .btn-secondary {
            color: white; /* White text for buttons */
        }

        .btn-info {
            background-color: #17a2b8; /* Bootstrap info color */
            border-color: #17a2b8;
        }

        .btn-info:hover {
            background-color: #13849b;
            border-color: #117a8b;
        }

        .btn-success {
            background-color: #28a745; /* Bootstrap success color */
            border-color: #28a745;
        }

        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }

        .btn-warning {
            background-color: #ffc107; /* Bootstrap warning color */
            border-color: #ffc107;
            color: var(--text-color); /* Dark text for warning button */
        }

        .btn-warning:hover {
            background-color: #e0a800;
            border-color: #d39e00;
        }

        .btn-danger {
            background-color: #dc3545; /* Bootstrap danger color */
            border-color: #dc3545;
        }

        .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }

        .btn-secondary {
            background-color: var(--secondary-light-teal); /* Light teal secondary button */
            border-color: var(--secondary-light-teal);
            color: var(--text-color); /* Dark text for secondary button */
        }

        .btn-secondary:hover {
            background-color: var(--light-background);
            border-color: var(--light-background);
            color: var(--text-color);
        }


    </style>
</head>
<body>
    <header class="view-patient-header">
        <div class="container">
            <h2>Patient Details</h2>
        </div>
    </header>

    <div class="container">

        <!-- Patient Information -->
        <div class="card">
            <div class="card-header">
                <h4>Patient Information</h4>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">Full Name:</dt>
                    <dd class="col-sm-9"><?= htmlspecialchars($patient['fullname']) ?></dd>

                    <dt class="col-sm-3">Patient ID:</dt>
                    <dd class="col-sm-9"><?= htmlspecialchars($patient['id']) ?></dd>

                    <dt class="col-sm-3">Email:</dt>
                    <dd class="col-sm-9"><?= htmlspecialchars($patient['email']) ?></dd>

                    <dt class="col-sm-3">Phone:</dt>
                    <dd class="col-sm-9"><?= htmlspecialchars($patient['phone']) ?></dd>

                    <dt class="col-sm-3">Address:</dt>
                    <dd class="col-sm-9"><?= htmlspecialchars($patient['address']) ?></dd>
                    <!-- Add more patient details as needed -->
                </dl>
            </div>
        </div>

        <!-- Medical Records -->
        <div class="card">
            <div class="card-header">
                <h4>Medical Records</h4>
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
                                    <th>Prescription</th>
                                    <th>Notes</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($medicalRecords as $record): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($record['visit_date']) ?></td>
                                        <td><?= htmlspecialchars($record['doctor_name']) ?></td>
                                        <td><?= htmlspecialchars($record['diagnosis']) ?></td>
                                        <td><?= htmlspecialchars($record['prescription']) ?></td>
                                        <td><?= htmlspecialchars($record['notes']) ?></td>
                                        <td class="text-center">
                                            <a href="view_record.php?id=<?= $record['id'] ?>&patient_id=<?= $patientId ?>" class="btn btn-sm btn-info">View</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No medical records found.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Lab Test Results -->
        <div class="card">
            <div class="card-header">
                <h4>Lab Test Results</h4>
            </div>
            <div class="card-body">
                <?php if ($labTestResults): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Test Name</th>
                                    <th>Result Date</th>
                                    <th>Status</th>
                                    <th>Details</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($labTestResults as $result): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($result['test_name']) ?></td>
                                        <td><?= htmlspecialchars($result['result_date']) ?></td>
                                        <td><?= htmlspecialchars($result['status']) ?></td>
                                        <td><?= htmlspecialchars($result['result_details']) ?></td>
                                        <td class="text-center">
                                            <a href="view_test_results.php?id=<?= $result['id'] ?>&patient_id=<?= $patientId ?>" class="btn btn-sm btn-info">View</a>
                                            <!-- Add Update button if staff is allowed to update results -->
                                            <?php if ($result['status'] == 'pending'): ?>
                                                <a href="update_test_result.php?id=<?= $result['id'] ?>&patient_id=<?= $patientId ?>" class="btn btn-sm btn-warning">Update</a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No lab test results found.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Upcoming Appointments -->
        <div class="card">
            <div class="card-header">
                <h4>Upcoming Appointments</h4>
            </div>
            <div class="card-body">
                <?php if ($upcomingAppointments): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Doctor</th>
                                    <th>Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($upcomingAppointments as $appointment): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($appointment['appointment_date']) ?></td>
                                        <td><?= htmlspecialchars($appointment['appointment_time']) ?></td>
                                        <td><?= htmlspecialchars($appointment['doctor_name']) ?></td>
                                        <td><?= htmlspecialchars($appointment['status']) ?></td>
                                        <td class="text-center">
                                            <a href="view_appointment.php?id=<?= $appointment['id'] ?>&patient_id=<?= $patientId ?>" class="btn btn-sm btn-info">View</a>
                                            <?php if ($appointment['status'] == 'pending'): ?>
                                                <a href="confirm_appointment.php?id=<?= $appointment['id'] ?>&patient_id=<?= $patientId ?>" class="btn btn-sm btn-success">Confirm</a>
                                                <a href="cance_appointment.php?id=<?= $appointment['id'] ?>&patient_id=<?= $patientId ?>" class="btn btn-sm btn-danger">Cancel</a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No upcoming appointments found.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Past Appointments -->
        <div class="card">
            <div class="card-header">
                <h4>Past Appointments</h4>
            </div>
            <div class="card-body">
                <?php if ($pastAppointments): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Doctor</th>
                                    <th>Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pastAppointments as $appointment): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($appointment['appointment_date']) ?></td>
                                        <td><?= htmlspecialchars($appointment['appointment_time']) ?></td>
                                        <td><?= htmlspecialchars($appointment['doctor_name']) ?></td>
                                        <td><?= htmlspecialchars($appointment['status']) ?></td>
                                        <td class="text-center">
                                            <a href="view_appointment.php?id=<?= $appointment['id'] ?>&patient_id=<?= $patientId ?>" class="btn btn-sm btn-info">View</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No past appointments found.</p>
                <?php endif; ?>
            </div>
        </div>

        <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>