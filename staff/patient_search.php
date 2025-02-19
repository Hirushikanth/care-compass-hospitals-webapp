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
$patients = [];

if ($searchQuery) {
    // Use the searchPatients() function from db.php to search by name or ID
    $patients = $db->searchPatients($searchQuery);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Search - Care Compass Connect</title>
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
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            overflow: hidden;
        }
        .search-card:hover {
            transform: scale(1.01);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.07);
        }
        .search-card-header {
            background-color: #046A7A;
            color: white;
            padding: 1.25rem;
            border-top-left-radius: 0.75rem;
            border-top-right-radius: 0.75rem;
        }
        .search-card-header h3 {
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
        .btn-outline-info {
            color: #17a2b8;
            border-color: #17a2b8;
            transition: background-color 0.2s ease-in-out, border-color 0.2s ease-in-out, color 0.2s ease-in-out;
        }
        .btn-outline-info:hover, .btn-outline-info:focus {
            background-color: #cff4fc;
            border-color: #17a2b8;
            color: #117a8b;
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
    </style>
</head>
<body>
    <header class="search-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <h2 class="mb-0">Patient Search</h2>
                </div>
            </div>
        </div>
    </header>

    <main class="container">
        <div class="search-card mb-4">
            <div class="card-body">
                <form method="get">
                    <div class="input-group">
                        <input type="text" class="form-control" name="search_query" placeholder="Search by name or ID" value="<?= htmlspecialchars($searchQuery) ?>">
                        <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i> Search</button>
                    </div>
                </form>
            </div>
        </div>

        <?php if ($searchQuery): ?>
            <div class="search-card">
                <div class="search-card-header">
                    <h3 class="mb-0">Search Results</h3>
                </div>
                <div class="card-body">
                    <?php if ($patients): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Full Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($patients as $patient): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($patient['id']) ?></td>
                                            <td><?= htmlspecialchars($patient['fullname']) ?></td>
                                            <td><?= htmlspecialchars($patient['email']) ?></td>
                                            <td><?= htmlspecialchars($patient['phone']) ?></td>
                                            <td class="text-end">
                                                <a href="view_patient.php?id=<?= $patient['id'] ?>" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i> View</a>
                                                <!-- Add more actions here as needed -->
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">No patients found matching your query.</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="mt-4">
            <a href="dashboard.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>