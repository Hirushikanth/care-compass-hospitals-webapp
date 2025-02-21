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


// Fetch all branches
$branches = $db->getAllBranches();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Branches - Care Compass Connect Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css"> <!-- Link to your main stylesheet -->
</head>
<body>
    <?php include('../includes/header.php'); // Assuming you have a header include file in includes folder ?>

    <div class="container py-5">
        <h2 class="mb-4">Manage Branches</h2>

        <?php if ($success_message): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="mb-3">
                    <a href="add_branch.php" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i> Add New Branch</a>
                </div>

                <?php if ($branches): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>City</th>
                                    <th>Phone</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($branches as $branch): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($branch['id']) ?></td>
                                        <td><?= htmlspecialchars($branch['name']) ?></td>
                                        <td><?= htmlspecialchars($branch['city'] ?: 'N/A') ?></td>
                                        <td><?= htmlspecialchars($branch['phone'] ?: 'N/A') ?></td>
                                        <td class="text-end">
                                            <a href="edit_branch.php?id=<?= $branch['id'] ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil-square"></i> Edit</a>
                                            <a href="delete_branch.php?id=<?= $branch['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this branch?')"><i class="bi bi-trash"></i> Delete</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No branches found.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="mt-4">
            <a href="dashboard.php" class="btn btn-secondary"><i class="bi bi-arrow-left me-2"></i> Back to Dashboard</a>
        </div>
    </div>

    <?php include('../includes/footer.php'); // Assuming you have a footer include file in includes folder ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>