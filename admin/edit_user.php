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

// Get user ID from the URL
$userId = isset($_GET['id']) ? $_GET['id'] : null;

if (!$userId) {
    echo "User ID not provided.";
    exit;
}

// Fetch user data
$user = $db->getUserById($userId);

if (!$user) {
    echo "User not found.";
    exit;
}

// Fetch all branches for the dropdown
$branches = $db->getAllBranches(); // Fetch branches for dropdown

// Initialize variables for form values and errors
$fullname = $user['fullname'];
$email = $user['email'];
$userType = $user['user_type'];
$phone = $user['phone'];
$address = $user['address'];
$staffDepartment = '';
$staffPosition = '';
$branchId = null; // Initialize branchId to null

if ($userType == 'staff') {
    $staffData = $db->getStaffById($userId); // Fetch staff-specific data
    if ($staffData) {
        $staffDepartment = $staffData['staff_department'];
        $staffPosition = $staffData['staff_position'];
        $branchId = $staffData['branch_id']; // Get branchId for staff user
    }
}

$errors = [];
$success = false;

// Handle form submission
if (isset($_POST['edit_user'])) {
    $fullname = sanitize_input($_POST['fullname']);
    $email = sanitize_input($_POST['email']);
    $userType = $_POST['user_type'];
    $phone = sanitize_input($_POST['phone']);
    $address = sanitize_input($_POST['address']);
    $staffDepartment = sanitize_input($_POST['staff_department']); // Get staff department from form
    $staffPosition = sanitize_input($_POST['staff_position']);     // Get staff position from form
    $branchId = $_POST['branch_id']; // Get branch ID from form


    // Server-side validation (in addition to client-side validation)
    $errors = [];
    if (empty($fullname)) {
        $errors[] = "Full name is required.";
    }
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    if (empty($userType)) {
        $errors[] = "User Type is required.";
    }
     if ($userType == 'staff' && empty($branchId)) { // Validate branch for staff
        $errors[] = "Branch is required for Staff users";
    }
    // Add more server-side validation as needed

    // If no errors, proceed with updating the user
    if (empty($errors)) {
        $success = false; // Initialize success to false

        if ($userType != 'staff') {
             $success = $db->updateUser($userId, $fullname, $email, $userType, $phone, $address); // Update non-staff user
        } else {
            // Update staff user including branch
            $success = $db->updateStaffUser($userId, $fullname, $email, $userType, $phone, $address, $staffDepartment, $staffPosition, $branchId); // Call new updateStaffUser()
        }


        if ($success) {
            echo '<div class="alert alert-success">User updated successfully!</div>';
            // Refetch user data to update the form
            $user = $db->getUserById($userId);
             if ($userType == 'staff') {
                $staffData = $db->getStaffById($userId);
                if ($staffData) {
                    $staffDepartment = $staffData['staff_department'];
                    $staffPosition = $staffData['staff_position'];
                    $branchId = $staffData['branch_id']; // Refetch branchId after update
                }
            }
        } else {
            echo '<div class="alert alert-danger">Error updating user.</div>';
        }
    } else {
        // Display errors
        echo '<div class="alert alert-danger"><ul>';
        foreach ($errors as $error) {
            echo "<li>$error</li>";
        }
        echo '</ul></div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - Care Compass Connect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background-color: #f0f8ff; /* Light background from the palette */
            font-family: 'Nunito', sans-serif;
            color: #343a40;
        }
        .edit-user-header {
            background-color: #046A7A; /* Dark teal from the palette */
            color: white;
            padding: 2rem 0;
            margin-bottom: 2.5rem;
            text-align: center;
        }
        .edit-user-header h2 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0;
        }
        .edit-user-card {
            background-color: #fff;
            border-radius: 0.75rem;
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.05);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .form-label {
            font-weight: 600;
            margin-bottom: 0.5rem;
            display: block;
        }
        .form-control {
            border-radius: 0.3rem;
            padding: 0.75rem;
            margin-bottom: 1.5rem;
            border: 1px solid #ced4da;
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
            transition: background-color 0.2s ease-in-out, border-color 0.2s ease-in-out;
        }
        .btn-secondary:hover, .btn-secondary:focus {
            background-color: #545b62;
            border-color: #545b62;
            box-shadow: 0 0 0 0.2rem rgba(108, 117, 125, 0.5);
        }
        .alert-success, .alert-danger {
            margin-bottom: 1.5rem;
        }
        .text-danger {
            font-size: 0.875rem;
            margin-top: -1rem;
            margin-bottom: 1rem;
            display: block;
        }
    </style>
</head>
<body>
    <header class="edit-user-header">
        <div class="container">
            <h2>Edit User</h2>
        </div>
    </header>

    <div class="container">
        <div class="edit-user-card">
            <?php if (isset($_POST['edit_user']) && empty($errors) && isset($success) && $success): ?>
                <div class="alert alert-success">User updated successfully!</div>
            <?php elseif (isset($_POST['edit_user']) && !empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <form method="post" onsubmit="return validateForm()">
                <div class="mb-3">
                    <label for="fullname" class="form-label">Full Name:</label>
                    <input type="text" class="form-control" id="fullname" name="fullname" value="<?= htmlspecialchars($fullname) ?>" required>
                    <div id="fullname-error" class="text-danger"></div>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email:</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
                    <div id="email-error" class="text-danger"></div>
                </div>
                <div class="mb-3">
                    <label for="user_type" class="form-label">User Type:</label>
                    <select class="form-control" id="user_type" name="user_type" required onchange="toggleStaffFields()">
                        <option value="admin" <?= ($userType == 'admin') ? 'selected' : '' ?>>Admin</option>
                        <option value="staff" <?= ($userType == 'staff') ? 'selected' : '' ?>>Staff</option>
                        <option value="patient" <?= ($userType == 'patient') ? 'selected' : '' ?>>Patient</option>
                    </select>
                    <div id="user-type-error" class="text-danger"></div>
                </div>
                 <!-- Staff Specific Fields (Conditionally Displayed) -->
                 <div class="mb-3" id="staff-fields" style="<?php echo ($userType == 'staff') ? 'display: block;' : 'display: none;'; ?>">
                    <div class="mb-3">
                        <label for="staff_department" class="form-label">Staff Department:</label>
                        <input type="text" class="form-control" id="staff_department" name="staff_department" value="<?= htmlspecialchars($staffDepartment) ?>">
                        <div id="staff-department-error" class="text-danger"></div>
                    </div>
                    <div class="mb-3">
                        <label for="staff_position" class="form-label">Staff Position:</label>
                        <input type="text" class="form-control" id="staff_position" name="staff_position" value="<?= htmlspecialchars($staffPosition) ?>">
                        <div id="staff-position-error" class="text-danger"></div>
                    </div>
                    <div class="mb-3">
                        <label for="branch_id" class="form-label">Branch:</label>
                        <select class="form-control" id="branch_id" name="branch_id" required>
                            <option value="">-- Select Branch --</option>
                            <?php foreach ($branches as $branchOption): ?>
                                <option value="<?= htmlspecialchars($branchOption['id']) ?>" <?= ($branchId == $branchOption['id']) ? 'selected' : '' ?>><?= htmlspecialchars($branchOption['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div id="branch-error" class="text-danger"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone:</label>
                    <input type="tel" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($user['phone']) ?>">
                    <div id="phone-error" class="text-danger"></div>
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label">Address:</label>
                    <textarea class="form-control" id="address" name="address"><?= htmlspecialchars($user['address']) ?></textarea>
                </div>
                <button type="submit" name="edit_user" class="btn btn-primary">Update User</button>
                <a href="manage_users.php" class="btn btn-secondary ms-2">Back to Manage Users</a>
            </form>
        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <script>
        function validateForm() {
            let isValid = true;
            const fullname = document.getElementById('fullname').value;
            const email = document.getElementById('email').value;
            const userType = document.getElementById('user_type').value;
            const phone = document.getElementById('phone').value;
            let branchId = '';


            // Reset error messages
            document.getElementById('fullname-error').innerText = '';
            document.getElementById('email-error').innerText = '';
            document.getElementById('user-type-error').innerText = '';
            document.getElementById('phone-error').innerText = '';
            document.getElementById('branch-error').innerText = '';


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

            // User type validation
            if (userType === '') {
                document.getElementById('user-type-error').innerText = 'Please select a user type.';
                isValid = false;
            }
             if (userType === 'staff') {
                branchId = document.getElementById('branch_id').value;
                if (branchId === '') {
                    document.getElementById('branch-error').innerText = 'Branch is required for staff users.';
                    isValid = false;
                }else{
                    document.getElementById('branch-error').innerText = '';
                }
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