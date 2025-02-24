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

// Initialize variables for form values and errors
$name = $address = $city = $phone = '';
$errors = [];

// Generate CSRF token BEFORE displaying the form
$csrf_token = generate_csrf_token();

// Handle form submission
if (isset($_POST['add_branch'])) {
    // Verify CSRF token at the VERY BEGINNING of form processing
    if (!verify_csrf_token()) {
        // CSRF token verification failed!  Reject the request.
        die("CSRF token validation failed."); // Or display a user-friendly error message and exit. In production, redirect to an error page.
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
        $success = $db->createBranch($name, $address, $city, $phone);

        if ($success) {
            $_SESSION['success_message'] = 'Branch added successfully!';
            header("Location: manage_branches.php"); // Redirect to manage branches page after success
            exit();
        } else {
            $error_message = "Error adding branch. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Branch - Care Compass Connect Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css"> <!-- Link to your main stylesheet -->
</head>
<body>
    <?php include('../includes/header.php'); ?>

    <div class="container py-5">
        <h2 class="mb-4">Add New Branch</h2>

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
                    <!-- ADD THIS HIDDEN INPUT FIELD for CSRF token -->
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
                    <button type="submit" name="add_branch" class="btn btn-primary">Add Branch</button>
                    <a href="manage_branches.php" class="btn btn-secondary ms-2">Cancel</a>
                </form>
            </div>
        </div>

        <div class="mt-4">
            <a href="manage_branches.php" class="btn btn-secondary"><i class="bi bi-arrow-left me-2"></i> Back to Manage Branches</a>
        </div>
    </div>

    <?php include('../includes/footer.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>