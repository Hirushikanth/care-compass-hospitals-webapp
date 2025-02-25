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

// Handle search query
$searchQuery = isset($_GET['search_query']) ? sanitize_input($_GET['search_query']) : '';
$doctors = [];

if ($searchQuery) {
    // Search doctors by name or specialty
    $doctors = $db->searchDoctors($searchQuery); // Implement this function in db.php
} else {
    // Fetch all doctors if no search query is provided
    $doctors = $db->getAllDoctors(); // You might already have this function
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Search - Care Compass Connect</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css"> <!-- Link to your main stylesheet -->
</head>
<body>
    <?php include('../includes/header.php'); ?>

    <div class="container">
        <h2>Doctor Search</h2>

        <form method="get">
            <div class="input-group mb-3">
                <input type="text" class="form-control" name="search_query" placeholder="Search by name or specialty" value="<?= htmlspecialchars($searchQuery) ?>">
                <button class="btn btn-primary" type="submit">Search</button>
            </div>
        </form>

        <h3>Search Results:</h3>
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
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($doctors as $doctor): ?>
                            <tr>
                                <td><?= $doctor['id'] ?></td>
                                <td><?= htmlspecialchars($doctor['fullname']) ?></td>
                                <td><?= htmlspecialchars($doctor['email']) ?></td>
                                <td><?= htmlspecialchars($doctor['specialty']) ?></td>
                                <td><?= htmlspecialchars($doctor['branch_name'] ?: 'N/A') ?></td> <!-- Display Branch Name -->
                                <td>
                                    <a href="edit_doctor.php?id=<?= $doctor['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <a href="delete_doctor.php?id=<?= $doctor['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this doctor?')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>No doctors found matching your query.</p>
        <?php endif; ?>

        <a href="dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
    </div>

    <?php include('../includes/footer.php'); ?>

    <script src="tps://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>
</body>
</html>