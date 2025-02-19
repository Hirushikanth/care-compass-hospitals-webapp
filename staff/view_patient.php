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
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <!-- Add your custom styles if needed -->
</head>
<body>
    <div class="container">
        <h2>Patient Details</h2>

        <!-- Patient Information -->
        <div class="card mb-3">
            <div class="card-header">
                <h4>Patient Information</h4>
            </div>
            <div class="card-body">
                <p><strong>Full Name:</strong> <?= $patient['fullname'] ?></p>
                <p><strong>ID:</strong> <?= $patient['id'] ?></p>
                <p><strong>Email:</strong> <?= $patient['email'] ?></p>
                <p><strong>Phone:</strong> <?= $patient['phone'] ?></p>
                <p><strong>Address:</strong> <?= $patient['address'] ?></p>
                <!-- Add more patient details as needed -->
            </div>
        </div>

        <!-- Medical Records -->
        <div class="card mb-3">
            <div class="card-header">
                <h4>Medical Records</h4>
            </div>
            <div class="card-body">
                <?php if ($medicalRecords): ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Doctor</th>
                                <th>Diagnosis</th>
                                <th>Prescription</th>
                                <th>Notes</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($medicalRecords as $record): ?>
                                <tr>
                                    <td><?= $record['visit_date'] ?></td>
                                    <td><?= $record['doctor_name'] ?></td>
                                    <td><?= $record['diagnosis'] ?></td>
                                    <td><?= $record['prescription'] ?></td>
                                    <td><?= $record['notes'] ?></td>
                                    <td>
                                        <a href="view_record.php?id=<?= $record['id'] ?>" class="btn btn-sm btn-info">View</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No medical records found.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Lab Test Results -->
        <div class="card mb-3">
            <div class="card-header">
                <h4>Lab Test Results</h4>
            </div>
            <div class="card-body">
                <?php if ($labTestResults): ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Test Name</th>
                                <th>Result Date</th>
                                <th>Status</th>
                                <th>Details</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($labTestResults as $result): ?>
                                <tr>
                                    <td><?= $result['test_name'] ?></td>
                                    <td><?= $result['result_date'] ?></td>
                                    <td><?= $result['status'] ?></td>
                                    <td><?= $result['result_details'] ?></td>
                                    <td>
                                        <a href="view_test_result.php?id=<?= $result['id'] ?>" class="btn btn-sm btn-info">View</a>
                                        <!-- Add Update button if staff is allowed to update results -->
                                        <?php if ($result['status'] == 'pending'): ?>
                                            <a href="update_test_result.php?id=<?= $result['id'] ?>" class="btn btn-sm btn-warning">Update</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No lab test results found.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Upcoming Appointments -->
        <div class="card mb-3">
            <div class="card-header">
                <h4>Upcoming Appointments</h4>
            </div>
            <div class="card-body">
                <?php if ($upcomingAppointments): ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Doctor</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($upcomingAppointments as $appointment): ?>
                                <tr>
                                    <td><?= $appointment['appointment_date'] ?></td>
                                    <td><?= $appointment['appointment_time'] ?></td>
                                    <td><?= $appointment['doctor_name'] ?></td>
                                    <td><?= $appointment['status'] ?></td>
                                    <td>
                                        <a href="view_appointment.php?id=<?= $appointment['id'] ?>" class="btn btn-sm btn-info">View</a>
                                        <?php if ($appointment['status'] == 'pending'): ?>
                                            <a href="confirm_appointment.php?id=<?= $appointment['id'] ?>" class="btn btn-sm btn-success">Confirm</a>
                                            <a href="cancel_appointment.php?id=<?= $appointment['id'] ?>" class="btn btn-sm btn-danger">Cancel</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No upcoming appointments found.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Past Appointments -->
        <div class="card mb-3">
            <div class="card-header">
                <h4>Past Appointments</h4>
            </div>
            <div class="card-body">
                <?php if ($pastAppointments): ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Doctor</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pastAppointments as $appointment): ?>
                                <tr>
                                    <td><?= $appointment['appointment_date'] ?></td>
                                    <td><?= $appointment['appointment_time'] ?></td>
                                    <td><?= $appointment['doctor_name'] ?></td>
                                    <td><?= $appointment['status'] ?></td>
                                    <td>
                                        <a href="view_appointment.php?id=<?= $appointment['id'] ?>" class="btn btn-sm btn-info">View</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No past appointments found.</p>
                <?php endif; ?>
            </div>
        </div>

        <a href="staff/dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>