<?php
// Include necessary files
include('../includes/config.php');
include('../includes/db.php');
include('../includes/functions.php');

// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Initialize variables for form values and errors
$fullname = $email = $password = $userType = $phone = $address = $branchId = '';
$staffDepartment = $staffPosition = ''; // Initialize $staffDepartment and $staffPosition
$errors = [];

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

$db = new Database();
// Fetch all branches for the dropdown
$branches = $db->getAllBranches();

// Initialize error and success messages
$error_message = "";
$success = false;

// Handle form submission
if (isset($_POST['add_user'])) {
    $fullname = sanitize_input($_POST['fullname']);
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password'];
    $userType = $_POST['user_type'];
    $phone = sanitize_input($_POST['phone']);
    $address = sanitize_input($_POST['address']);
    $staffDepartment = sanitize_input($_POST['staff_department']);
    $staffPosition = sanitize_input($_POST['staff_position']);
    $branchId = $_POST['branch_id']; // Get branch ID for staff

    // Validation (Server-side - Add more as needed)
    $errors = [];
    if (empty($fullname)) $errors[] = "Full Name is required";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid Email is required";
    if (empty($password) || strlen($password) < 8) $errors[] = "Password must be at least 8 characters long";
    if (empty($userType)) $errors[] = "User Type is required";
    if ($userType == 'staff' && empty($branchId)) $errors[] = "Branch is required for Staff users"; // Validate branch for staff

    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        // Start transaction
        $db->beginTransaction();
        try {
            $userId = $db->createUser($fullname, $email, $hashedPassword, $userType, $phone, $address);
            if ($userId) {
                if ($userType == 'staff') {
                    if (!$db->createStaff($userId, $staffDepartment, $staffPosition, $branchId)) { // Pass branchId
                        throw new Exception('Failed to create staff record');
                    }
                }
                $db->commitTransaction();
                $success = true;
            } else {
                throw new Exception('Failed to create user record');
            }
        } catch (Exception $e) {
            $db->rollbackTransaction();
            $error_message = "Error creating user: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User - Care Compass Connect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background-color: #f0f8ff;
            font-family: 'Nunito', sans-serif;
            color: #343a40;
        }
        .form-container {
            background-color: #fff;
            border-radius: 0.75rem;
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.05);
            padding: 2rem;
            margin-top: 2rem;
            margin-bottom: 2rem;
        }
        .form-header {
            color: #046A7A;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .form-header h2 {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }
        .form-label {
            font-size: 0.95rem;
            margin-bottom: 0.2rem;
        }
        .form-control {
            border-radius: 0.3rem;
        }
        .form-control:focus {
            border-color: #046A7A;
            box-shadow: 0 0 0 0.2rem rgba(4, 106, 122, 0.25);
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
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }
        .btn-secondary:hover, .btn-secondary:focus {
            background-color: #545b62;
            border-color: #545b62;
            box-shadow: 0 0 0 0.2rem rgba(108, 117, 125, 0.5);
        }
        .alert-danger, .alert-success {
            border-radius: 0.3rem;
            margin-bottom: 1rem;
        }
        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="form-container">
                    <div class="form-header">
                        <h2>Add New User</h2>
                    </div>
                    <form method="post" onsubmit="return validateForm()">
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <ul>
                                    <?php foreach ($errors as $error): ?>
                                        <li><?= $error ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        <?php if ($success): ?>
                            <div class="alert alert-success">User added successfully!</div>
                        <?php endif; ?>
                        <?php if (!empty($error_message)): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
                        <?php endif; ?>
                        <div class="mb-3">
                            <label for="fullname" class="form-label">Full Name:</label>
                            <input type="text" class="form-control" id="fullname" name="fullname" value="<?= htmlspecialchars($fullname ?? '') ?>" required>
                            <div id="fullname-error" class="error-message"></div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email:</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" required>
                            <div id="email-error" class="error-message"></div>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password:</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <div id="password-error" class="error-message"></div>
                        </div>
                        <div class="mb-3">
                            <label for="user_type" class="form-label">User Type:</label>
                            <select class="form-control" id="user_type" name="user_type" required onchange="toggleStaffFields()">
                                <option value="">-- Select User Type --</option>
                                <option value="admin" <?= (isset($_POST['user_type']) && $_POST['user_type'] == 'admin') ? 'selected' : '' ?>>Admin</option>
                                <option value="staff" <?= (isset($_POST['user_type']) && $_POST['user_type'] == 'staff') ? 'selected' : '' ?>>Staff</option>
                                <option value="patient" <?= (isset($_POST['user_type']) && $_POST['user_type'] == 'patient') ? 'selected' : '' ?>>Patient</option>
                            </select>
                            <div id="user-type-error" class="error-message"></div>
                        </div>
                        <!-- Staff Specific Fields (Initially Hidden) -->
                        <div class="mb-3" id="staff-fields" style="display: none;">
                            <div class="mb-3">
                                <label for="staff_department" class="form-label">Staff Department:</label>
                                <input type="text" class="form-control" id="staff_department" name="staff_department" value="<?= htmlspecialchars($staffDepartment ?? '') ?>">
                                <div id="staff-department-error" class="error-message"></div>
                            </div>
                            <div class="mb-3">
                                <label for="staff_position" class="form-label">Staff Position:</label>
                                <input type="text" class="form-control" id="staff_position" name="staff_position" value="<?= htmlspecialchars($staffPosition ?? '') ?>">
                                <div id="staff-position-error" class="error-message"></div>
                            </div>
                             <div class="mb-3">
                                <label for="branch_id" class="form-label">Branch:</label>
                                <select class="form-control" id="branch_id" name="branch_id" required>
                                    <option value="">-- Select Branch --</option>
                                    <?php foreach ($branches as $branch): ?>
                                        <option value="<?= htmlspecialchars($branch['id']) ?>" <?= (isset($_POST['branch_id']) && $_POST['branch_id'] == $branch['id']) ? 'selected' : '' ?>><?= htmlspecialchars($branch['name'] ?? '') ?></option>
                                    <?php endforeach; ?>
                                </select>
                                 <div id="branch-error" class="error-message"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone:</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($phone ?? '') ?>">
                            <div id="phone-error" class="error-message"></div>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Address:</label>
                            <textarea class="form-control" id="address" name="address"><?= htmlspecialchars($address ?? '') ?></textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" name="add_user" class="btn btn-primary">Add User</button>
                            <a href="manage_users.php" class="btn btn-secondary">Back to Manage Users</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function validateForm() {
        let isValid = true;
        const fullname = document.getElementById('fullname').value;
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const userType = document.getElementById('user_type').value;
        const phone = document.getElementById('phone').value;
        let branchId = '';

        if (userType === 'staff') {
             branchId = document.getElementById('branch_id').value;
             if (branchId === '') {
                document.getElementById('branch-error').innerText = 'Branch is required for staff users.';
                isValid = false;
            }else{
                document.getElementById('branch-error').innerText = '';
            }
        }


        // Reset error messages
        document.getElementById('fullname-error').innerText = '';
        document.getElementById('email-error').innerText = '';
        document.getElementById('password-error').innerText = '';
        document.getElementById('user-type-error').innerText = '';
        document.getElementById('phone-error').innerText = '';
        document.getElementById('branch-error').innerText = ''; // ADDED: Reset branch-error message


        // Full name validation
        if (fullname.trim() === '') {
            document.getElementById('fullname-error').innerText = 'Full name is required.';
            isValid = false;
        }

        // Email validation
        if (email.trim() === '') {
            document.getElementById('email-error').innerText = 'Email is required.';
            isValid = false;
        } else if (!/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email)) {
            document.getElementById('email-error').innerText = 'Invalid email format.';
            isValid = false;
        }

        // Password validation
        if (password === '') {
            document.getElementById('password-error').innerText = 'Password is required.';
            isValid = false;
        } else if (password.length < 8) {
            document.getElementById('password-error').innerText = 'Password must be at least 8 characters long.';
            isValid = false;
        }

        // User type validation
        if (userType === '') {
            document.getElementById('user-type-error').innerText = 'Please select a user type.';
            isValid = false;
        }


        // Phone number validation (basic example, adjust regex as needed)
        if (phone.trim() !== '' && !/^\d{10}$/.test(phone)) {
            document.getElementById('phone-error').innerText = 'Invalid phone number format (10 digits expected).';
            isValid = false;
        }

        return isValid;
    }

    function toggleStaffFields() {
        var userType = document.getElementById("user_type").value;
        var staffFields = document.getElementById("staff-fields");


        if (userType === 'staff') {
            staffFields.style.display = 'block';
        } else {
            staffFields.style.display = 'none';
        }
    }

    // Call toggleStaffFields on page load to set initial state based on PHP-set user_type (if any)
    window.onload = toggleStaffFields;
</script>
</body>
</html>