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
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include('../includes/header.php'); ?>

    <main class="container">
        <div class="search-card">
            <div class="search-card-body">
                <h2 class="mb-4">Appointment Search</h2>
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
            </div>
        </div>
    </main>

    <?php include('../includes/footer.php'); ?>
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>