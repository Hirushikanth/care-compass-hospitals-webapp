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

// Handle success/error messages from other pages (e.g., add_branch, edit_branch, delete_branch)
$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';

// Clear session messages after displaying them
unset($_SESSION['success_message']);
unset($_SESSION['error_message']);


// Fetch all doctors
$doctors = $db->getAllDoctors();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Doctors - Care Compass Connect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <!-- Add your custom styles if needed -->
</head>
<body>
    <?php include('../includes/header.php'); ?>

    <div class="container py-5">
        <div class="row">
            <div class="col-md-12">
                <h2>Manage Doctors</h2>
            </div>
        </div>

        <?php if ($success_message): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="mb-3">
                    <a href="add_doctor.php" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i> Add New Doctor</a>
                </div>

                <?php if ($doctors): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Specialty</th>
                                    <th>Branch</th> <!-- New Branch Column Header -->
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($doctors as $doctor): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($doctor['id']) ?></td>
                                        <td><?= htmlspecialchars($doctor['fullname']) ?></td>
                                        <td><?= htmlspecialchars($doctor['email']) ?></td>
                                        <td><?= htmlspecialchars($doctor['specialty'] ?? 'N/A') ?></td>
                                        <td><?= htmlspecialchars($doctor['branch_name'] ?: 'N/A') ?></td> 
                                        <td class="text-end">
                                            <a href="edit_doctor.php?id=<?= $doctor['id'] ?>" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil-square"></i> Edit</a>
                                            <a href="delete_doctor.php?id=<?= $doctor['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this doctor?')"><i class="bi bi-trash"></i> Delete</a>
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

        <div class="mt-4">
            <a href="dashboard.php" class="btn btn-secondary"><i class="bi bi-arrow-left me-2"></i> Back to Dashboard</a>
        </div>
    </div>

    <?php include('../includes/footer.php'); ?>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>