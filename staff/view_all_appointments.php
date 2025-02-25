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

// Fetch all appointments
$appointments = $db->getAllAppointments(); // You have this function in db.php
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View All Appointments - Care Compass Connect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css"> <!-- Link to your main stylesheet -->
</head>
<body>
    <?php include('../includes/header.php'); ?>

    <div class="container py-5">
        <h2 class="mb-4">All Appointments</h2>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="mb-3">
                    <a href="appointment_search.php" class="btn btn-primary"><i class="bi bi-search me-1"></i> Search Appointments</a>
                </div>

                <?php if ($appointments): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Patient</th>
                                    <th>Doctor</th>
                                    <th>Branch</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($appointments as $appointment): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($appointment['id']) ?></td>
                                        <td><?= htmlspecialchars($appointment['patient_name']) ?></td>
                                        <td><?= htmlspecialchars($appointment['doctor_name']) ?></td>
                                        <td><?= htmlspecialchars($appointment['branch_name'] ?: 'N/A') ?></td>
                                        <td><?= htmlspecialchars($appointment['appointment_date']) ?></td>
                                        <td><?= htmlspecialchars($appointment['appointment_time']) ?></td>
                                        <td><?= htmlspecialchars($appointment['status']) ?></td>
                                        <td class="text-end">
                                            <a href="view_appointment.php?id=<?= $appointment['id'] ?>" class="btn btn-sm btn-info"><i class="bi bi-eye-fill"></i> View</a>
                                            <?php if ($appointment['status'] == 'pending'): ?>
                                                <a href="confirm_appointment.php?id=<?= $appointment['id'] ?>" class="btn btn-sm btn-success"><i class="bi bi-check-lg"></i> Confirm</a>
                                                <a href="cancel_appointment.php?id=<?= $appointment['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to cancel this appointment?')"><i class="bi bi-x-lg"></i> Cancel</a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No appointments found.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="mt-4">
            <a href="dashboard.php" class="btn btn-secondary"><i class="bi bi-arrow-left me-2"></i> Back to Dashboard</a>
        </div>
    </div>

    <?php include('../includes/footer.php'); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>
</body>
</html>