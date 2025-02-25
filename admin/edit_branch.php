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

// Get branch ID from URL parameter
$branchId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($branchId <= 0) {
    // Invalid branch ID, redirect to manage_branches page
    header("Location: manage_branches.php");
    exit;
}

// Fetch branch data for editing
$branch = $db->getBranchById($branchId);

if (!$branch) {
    // Branch not found, redirect to manage_branches page
    $_SESSION['error_message'] = 'Branch not found.';
    header("Location: manage_branches.php");
    exit;
}

// Initialize variables for form values and errors
$name = $branch['name'];
$address = $branch['address'];
$city = $branch['city'];
$phone = $branch['phone'];
$errors = [];

// Generate CSRF token
$csrf_token = generate_csrf_token();

// Handle form submission
if (isset($_POST['edit_branch'])) {
    // Verify CSRF token
    if (!verify_csrf_token()) {
        die("CSRF token validation failed."); // Or handle error more gracefully
    }

    $name = sanitize_input($_POST['name']);
    $address = sanitize_input($_POST['address']);
    $city = sanitize_input($_POST['city']);
    $phone = sanitize_input($_POST['phone']);

    // Server-side validation
    if (empty($name)) {
        $errors[] = "Branch name is required.";
    }

    if (empty($errors)) {
        $success = $db->updateBranch($branchId, $name, $address, $city, $phone);

        if ($success) {
            $_SESSION['success_message'] = 'Branch updated successfully!';
            header("Location: manage_branches.php"); // Redirect to manage branches page after success
            exit();
        } else {
            $error_message = "Error updating branch. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Branch - Care Compass Connect Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css"> <!-- Link to your main stylesheet -->
</head>
<body>
    <?php include('../includes/header.php'); ?>

    <div class="container py-5">
        <h2 class="mb-4">Edit Branch</h2>

        <div class="card shadow-sm">
            <div class="card-body">
                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
                <?php endif; ?>
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                <form method="post">
                    <!-- CSRF Token Field -->
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

                    <div class="mb-3">
                        <label for="name" class="form-label">Branch Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($name) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address"><?= htmlspecialchars($address) ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="city" class="form-label">City</label>
                        <input type="text" class="form-control" id="city" name="city" value="<?= htmlspecialchars($city) ?>">
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($phone) ?>">
                    </div>
                    <button type="submit" name="edit_branch" class="btn btn-primary">Update Branch</button>
                    <a href="manage_branches.php" class="btn btn-secondary ms-2">Cancel</a>
                </form>
            </div>
        </div>

        <div class="mt-4">
            <a href="manage_branches.php" class="btn btn-secondary"><i class="bi bi-arrow-left me-2"></i> Back to Manage Branches</a>
        </div>
    </div>

    <?php include('../includes/footer.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>
</body>
</html>