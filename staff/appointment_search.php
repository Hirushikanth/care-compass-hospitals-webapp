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

// Handle search query
$searchQuery = isset($_GET['search_query']) ? sanitize_input($_GET['search_query']) : '';
$searchDate = isset($_GET['search_date']) ? sanitize_input($_GET['search_date']) : '';
$appointments = [];

if ($searchQuery || $searchDate) {
    $appointments = $db->searchAppointments($searchQuery, $searchDate); // Implement this in db.php
} else {
    // Fetch all appointments if no search query is provided (optional)
    // $appointments = $db->getAllAppointments();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Search - Care Compass Connect</title>
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
        .search-header {
            background-color: #046A7A; /* Dark teal from the palette */
            color: white;
            padding: 2.5rem 0;
            margin-bottom: 3rem;
        }
        .search-header h2 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0;
        }
        .search-card {
            background-color: #fff;
            border-radius: 0.75rem;
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }
        .search-card-body {
            padding: 2rem;
        }
        .form-control {
            border-radius: 0.3rem;
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
        .table {
            font-size: 0.95rem;
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
        .btn-info, .btn-success, .btn-danger {
            font-size: 0.85rem;
        }
        .back-link {
            margin-top: 2rem;
        }
    </style>
</head>
<body>
    <header class="search-header">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="mb-0">Appointment Search</h2>
                </div>
            </div>
        </div>
    </header>

    <main class="container">
        <div class="search-card">
            <div class="search-card-body">
                <form method="get" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <input type="text" class="form-control" name="search_query" placeholder="Search by patient or doctor name" value="<?= htmlspecialchars($searchQuery) ?>">
                        </div>
                        <div class="col-md-5">
                            <input type="date" class="form-control" name="search_date" value="<?= htmlspecialchars($searchDate) ?>">
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary w-100" type="submit">Search</button>
                        </div>
                    </div>
                </form>

                <?php if ($searchQuery || $searchDate): ?>
                    <h3 class="mb-3">Search Results:</h3>
                    <?php if ($appointments): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Patient</th>
                                        <th>Doctor</th>
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
                                            <td><?= htmlspecialchars($appointment['appointment_date']) ?></td>
                                            <td><?= htmlspecialchars($appointment['appointment_time']) ?></td>
                                            <td><?= htmlspecialchars($appointment['status']) ?></td>
                                            <td>
                                                <a href="../view_appointment.php?id=<?= $appointment['id'] ?>" class="btn btn-sm btn-info"><i class="bi bi-eye"></i> View</a>
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

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>