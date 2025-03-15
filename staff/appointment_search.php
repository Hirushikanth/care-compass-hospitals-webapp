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

// Fetch branches for the branch filter dropdown
$branches = $db->getAllBranches();

// Handle search query
$searchQuery = isset($_GET['search_query']) ? sanitize_input($_GET['search_query']) : '';
$searchDate = isset($_GET['search_date']) ? sanitize_input($_GET['search_date']) : '';
$branchFilter = isset($_GET['branch_filter']) ? intval($_GET['branch_filter']) : 0; // Get branch filter ID
$appointments = [];

if ($searchQuery || $searchDate || $branchFilter) { // Include branchFilter in condition
    $appointments = $db->searchAppointments($searchQuery, $searchDate, $branchFilter); // Pass branchFilter to searchAppointments
} else {
    // Optionally, Fetch all appointments (or a limited set) if no search criteria is provided
    // $appointments = $db->getAllAppointments();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Search - Care Compass Connect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* --- Booking Card Style (from book_appointment.php) - Ideally move to style.css --- */
        .booking-card {
            background-color: #fff;
            border-radius: 0.75rem;
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            overflow: hidden;
            max-width: 700px; /* Adjust max-width as needed */
            margin: auto; /* Center card on page */
            margin-bottom: 2rem; /* Add some margin below */
        }
        .booking-card:hover {
            transform: scale(1.01);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.07);
        }
        .card-body { /* Reusing card body style from book_appointment.php - ensure consistent padding in style.css */
            padding: 1.5rem;
        }

        .booking-card-header h2 {
            color: white;
            font-size: 1.5rem;
            margin-bottom: 0;
            font-weight: 600;
        }

        .booking-card-header {
            background-color: #046A7A;
            color: white;
            padding: 1rem;
            border-top-left-radius: 0.75rem;
            border-top-right-radius: 0.75rem;
        }
    </style>
</head>
<body>
    <?php include('../includes/header_user.php'); ?>

    <main class="container">
        <div class="booking-card">  <!-- ADDED: booking-card wrapper -->
            <div class="card-body">  <!-- ADDED: card-body wrapper -->
                <div class="booking-card-header">
                    <h2 class="mb-4">Appointment Search</h2>
                </div>
                <form method="get" class="mb-4">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label for="search_query" class="form-label">Search Patient/Doctor</label>
                            <input type="text" class="form-control" id="search_query" name="search_query" placeholder="Patient or doctor name" value="<?= htmlspecialchars($searchQuery) ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="search_date" class="form-label">Date</label>
                            <input type="date" class="form-control" id="search_date" name="search_date" value="<?= htmlspecialchars($searchDate) ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="branch_filter" class="form-label">Filter by Branch</label> <!-- Branch Filter Dropdown -->
                            <select class="form-control" id="branch_filter" name="branch_filter">
                                <option value="0">-- All Branches --</option>
                                <?php foreach ($branches as $branch): ?>
                                    <option value="<?= htmlspecialchars($branch['id']) ?>" <?= ($branchFilter == $branch['id'] ) ? 'selected' : '' ?>><?= htmlspecialchars($branch['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary w-100" type="submit">Search</button>
                        </div>
                    </div>
                </form>

                <?php if ($searchQuery || $searchDate || $branchFilter): ?>
                    <h3 class="mb-3">Search Results:</h3>
                    <?php if ($appointments): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Patient</th>
                                        <th>Doctor</th>
                                        <th>Branch</th> <!-- New Branch Column -->
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($appointments as $appointment): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($appointment['patient_name']) ?></td>
                                            <td><?= htmlspecialchars($appointment['doctor_name']) ?></td>
                                            <td><?= htmlspecialchars($appointment['branch_name'] ?: 'N/A') ?></td> <!-- Display Branch Name -->
                                            <td><?= htmlspecialchars($appointment['appointment_date']) ?></td>
                                            <td><?= htmlspecialchars($appointment['appointment_time']) ?></td>
                                            <td><?= htmlspecialchars($appointment['status']) ?></td>
                                            <td>
                                                <a href="view_appointment.php?id=<?= $appointment['id'] ?>" class="btn btn-sm btn-info"><i class="bi bi-eye"></i> View</a>
                                                <?php if ($appointment['status'] == 'pending'): ?>
                                                    <a href="confirm_appointment.php?id=<?= $appointment['id'] ?>" class="btn btn-sm btn-success"><i class="bi bi-check-circle"></i> Confirm</a>
                                                    <a href="cancel_appointment.php?id=<?= $appointment['id'] ?>" class="btn btn-sm btn-danger"><i class="bi bi-x-circle"></i> Cancel</a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">No appointments found matching your criteria.</p>
                    <?php endif; ?>
                <?php endif; ?>

                <div class="back-link">
                    <a href="dashboard.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
                </div>
            </div> <!-- ADDED: closing card-body -->
        </div>     <!-- ADDED: closing booking-card -->
    </main>

    <?php include('../includes/footer.php'); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>
</body>
</html>