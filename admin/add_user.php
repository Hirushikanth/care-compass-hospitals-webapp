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

// Handle form submission
if (isset($_POST['add_user'])) {
    $fullname = sanitize_input($_POST['fullname']);
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password']; // Hash this password!
    $userType = $_POST['user_type'];
    $phone = sanitize_input($_POST['phone']);
    $address = sanitize_input($_POST['address']);

    // Basic input validation (server-side)
    $errors = [];
    if (empty($fullname)) {
        $errors[] = "Full name is required.";
    }
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    }
    // Add more server-side validation as needed

    // Check if email already exists (only if it's not empty and is valid)
    if (empty($errors) && !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        if ($db->getUserByEmail($email)) {
            $errors[] = "Email already exists.";
        }
    }

    // If no errors, proceed with adding the user
    if (empty($errors)) {
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert user into the database
        $success = $db->createUser($fullname, $email, $hashedPassword, $userType, $phone, $address);

        if ($success) {
            echo '<div class="alert alert-success">User added successfully!</div>';
        } else {
            echo '<div class="alert alert-danger">Error adding user.</div>';
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
    <title>Add User - Care Compass Connect</title>
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
        .form-container {
            background-color: #fff;
            border-radius: 0.75rem;
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.05);
            padding: 2rem;
            margin-top: 2rem;
            margin-bottom: 2rem;
        }
        .form-header {
            color: #046A7A; /* Dark teal from the palette */
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
                        <?php
                        if (isset($errors) && !empty($errors)) {
                            echo '<div class="alert alert-danger"><ul>';
                            foreach ($errors as $error) {
                                echo "<li>" . htmlspecialchars($error) . "</li>";
                            }
                            echo '</ul></div>';
                        } elseif (isset($success) && $success) {
                            echo '<div class="alert alert-success">User added successfully!</div>';
                        }
                        ?>
                        <div class="mb-3">
                            <label for="fullname" class="form-label">Full Name:</label>
                            <input type="text" class="form-control" id="fullname" name="fullname" required>
                            <div id="fullname-error" class="error-message"></div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email:</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                            <div id="email-error" class="error-message"></div>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password:</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <div id="password-error" class="error-message"></div>
                        </div>
                        <div class="mb-3">
                            <label for="user_type" class="form-label">User Type:</label>
                            <select class="form-control" id="user_type" name="user_type" required>
                                <option value="">-- Select User Type --</option>
                                <option value="admin">Admin</option>
                                <option value="staff">Staff</option>
                                <option value="patient">Patient</option>
                            </select>
                            <div id="user-type-error" class="error-message"></div>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone:</label>
                            <input type="text" class="form-control" id="phone" name="phone">
                            <div id="phone-error" class="error-message"></div>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Address:</label>
                            <textarea class="form-control" id="address" name="address"></textarea>
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

            // Reset error messages
            document.getElementById('fullname-error').innerText = '';
            document.getElementById('email-error').innerText = '';
            document.getElementById('password-error').innerText = '';
            document.getElementById('user-type-error').innerText = '';
            document.getElementById('phone-error').innerText = '';

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
    </script>
</body>
</html>