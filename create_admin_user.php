<?php
// Include necessary files
include('includes/config.php');
include('includes/db.php');
include('includes/functions.php');

// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$db = new Database();

// Initialize variables for form values and errors
$fullname = $email = $password = '';
$errors = [];
$success_message = '';
$error_message = '';

// Generate CSRF token BEFORE displaying the form
$csrf_token = generate_csrf_token();

// Handle form submission
if (isset($_POST['create_admin_user'])) {
    // Verify CSRF token at the VERY BEGINNING of form processing
    if (!verify_csrf_token()) {
        // CSRF token verification failed!  Reject the request.
        die("CSRF token validation failed."); // Or display a user-friendly error message and exit.
    }

    $fullname = sanitize_input($_POST['fullname']);
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password'];

    // Validation (Server-side - Add more as needed)
    $errors = [];
    if (empty($fullname)) $errors[] = "Full Name is required";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid Email is required";
    if (empty($password) || strlen($password) < 8) $errors[] = "Password must be at least 8 characters long";

    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $userType = 'admin'; // Set user type to admin
        $success = $db->createUser($fullname, $email, $hashedPassword, $userType);

        if ($success) {
            $success_message = "Admin user created successfully!";
        } else {
            $error_message = "Error creating admin user. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Admin User - Care Compass Connect (Hidden Page)</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
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
            max-width: 500px; /* Limit form width */
            margin-left: auto;
            margin-right: auto;
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
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <div class="form-header">
                <h2>Create Admin User (Hidden Page)</h2>
                <p class="text-muted">Use this page to create a new admin user account.</p>
            </div>
            <form method="post" onsubmit="return validateForm()">
                <!-- CSRF Token Field -->
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success_message)): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div>
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

                <div class="d-grid gap-2">
                    <button type="submit" name="create_admin_user" class="btn btn-primary">Create Admin User</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <script>
        function validateForm() {
            let isValid = true;
            const fullname = document.getElementById('fullname').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            // Reset error messages
            document.getElementById('fullname-error').innerText = '';
            document.getElementById('email-error').innerText = '';
            document.getElementById('password-error').innerText = '';

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


            return isValid;
        }
    </script>
</body>
</html>