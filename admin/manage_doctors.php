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
        .page-header {
            background-color: #046A7A; /* Dark teal from the palette */
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        .page-header h2 {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0;
        }
        .content-area {
            background-color: #fff;
            border-radius: 0.75rem;
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.05);
            padding: 2rem;
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
        .btn-outline-danger {
            color: #dc3545;
            border-color: #dc3545;
            transition: background-color 0.2s ease-in-out, border-color 0.2s ease-in-out, color 0.2s ease-in-out;
        }
        .btn-outline-danger:hover, .btn-outline-danger:focus {
            background-color: #f8d7da;
            border-color: #dc3545;
            color: #b02a37;
        }
        .btn-outline-warning {
            color: #ffc107;
            border-color: #ffc107;
            transition: background-color 0.2s ease-in-out, border-color 0.2s ease-in-out, color 0.2s ease-in-out;
        }
        .btn-outline-warning:hover, .btn-outline-warning:focus {
            background-color: #fff3cd;
            border-color: #ffc107;
            color: #b38b00;
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
    </style>
</head>
<body>
    <header class="page-header">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h2>Manage Doctors</h2>
                </div>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="content-area">
            <div class="mb-3">
                <a href="add_doctor.php" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i> Add New Doctor</a>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Full Name</th>
                            <th>Specialty</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($doctors): ?>
                            <?php foreach ($doctors as $doctor): ?>
                                <tr>
                                    <td><?= htmlspecialchars($doctor['id']) ?></td>
                                    <td><?= htmlspecialchars($doctor['fullname']) ?></td>
                                    <td><?= htmlspecialchars($doctor['specialty']) ?></td>
                                    <td class="text-end">
                                        <a href="edit_doctor.php?id=<?= $doctor['id'] ?>" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil-square"></i> Edit</a>
                                        <a href="delete_doctor.php?id=<?= $doctor['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this doctor?')"><i class="bi bi-trash"></i> Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center">No doctors found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                <a href="dashboard.php" class="btn btn-secondary"><i class="bi bi-arrow-left me-1"></i> Back to Dashboard</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>