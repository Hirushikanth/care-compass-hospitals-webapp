<?php
// Include necessary files (config.php, db.php, functions.php)
include('includes/config.php');
include('includes/db.php');
include('includes/functions.php');

// Initialize error variable
$errors = [];

// Handle user registration form submission
if (isset($_POST['register'])) {
    $fullname = sanitize_input($_POST['fullname']);
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $user_type = 'patient'; // Set user type to patient
    $phone = sanitize_input($_POST['phone']);
    $address = sanitize_input($_POST['address']);

    // Server-side validation (in addition to client-side validation)
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
    if ($password != $confirm_password) {
        $errors[] = "Passwords do not match.";
    }
    // Add more server-side validation as needed

    // Check if email already exists
    $db = new Database();
    if ($db->getUserByEmail($email)) {
        $errors[] = "Email already exists.";
    }

    // If no errors, proceed with registration
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $success = $db->createUser($fullname, $email, $hashed_password, $user_type, $phone, $address);

        if ($success) {
            // Redirect to login page or display a success message
            header("Location: login.php?registration_success=1");
            exit;
        } else {
            $errors[] = "Error registering user. Please try again later.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Care Compass Connect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #f0f8ff; /* Light background from the palette */
            font-family: 'Nunito', sans-serif;
            color: #343a40;
        }
        .register-card {
            background-color: #fff;
            border-radius: 0.75rem;
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.05);
            overflow: hidden;
            width: 100%;
            max-width: 500px;
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .register-card:hover {
            transform: scale(1.01);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.07);
        }
        .register-header {
            background-color: #046A7A; /* Dark teal from the palette */
            color: white;
            padding: 1.5rem;
            text-align: center;
        }
        .register-header h2 {
            font-size: 1.75rem;
            margin-bottom: 0.25rem;
            font-weight: 700;
        }
        .register-form {
            padding: 1.5rem;
        }
        .form-label {
            font-size: 0.95rem;
            margin-bottom: 0.3rem;
        }
        .form-control {
            border: 1px solid #ced4da;
            border-radius: 0.3rem;
            padding: 0.6rem 0.75rem;
            font-size: 1rem;
        }
        .form-control:focus {
            border-color: #046A7A; /* Focus color */
            box-shadow: 0 0 0 0.2rem rgba(4, 106, 122, 0.25); /* Focus shadow */
        }
        .btn-primary {
            background-color: #046A7A;
            border-color: #046A7A;
            padding: 0.75rem 1.25rem;
            font-size: 1.05rem;
            border-radius: 0.3rem;
            transition: background-color 0.2s ease-in-out, border-color 0.2s ease-in-out;
        }
        .btn-primary:hover, .btn-primary:focus {
            background-color: #034e5a;
            border-color: #034e5a;
            box-shadow: 0 0 0 0.2rem rgba(4, 106, 122, 0.5);
        }
        .register-footer {
            text-align: center;
            padding: 1rem 1.5rem;
            border-top: 1px solid #e9ecef;
        }
        .register-footer p {
            margin-bottom: 0;
            font-size: 0.9rem;
            color: #6c757d;
        }
        .register-footer a {
            color: #046A7A;
            text-decoration: none;
            transition: color 0.2s ease-in-out;
        }
        .register-footer a:hover {
            text-decoration: underline;
            color: #034e5a;
        }
        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c2c7;
            color: #842029;
            padding: 0.75rem 1rem;
            margin-bottom: 1rem;
            border-radius: 0.3rem;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="register-card">
                    <div class="register-header">
                        <h2 class="mb-0">Sign Up</h2>
                        <p class="mb-0">Create your Care Compass Connect account</p>
                    </div>
                    <div class="register-form">
                        <?php if (!empty($errors)): ?>
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
                                <label for="fullname" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="fullname" name="fullname" required>
                                <div id="fullname-error" class="text-danger"></div>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                                <div id="email-error" class="text-danger"></div>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <div id="password-error" class="text-danger"></div>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                <div id="confirm-password-error" class="text-danger"></div>
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="tel" class="form-control" id="phone" name="phone">
                                <div id="phone-error" class="text-danger"></div>
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control" id="address" name="address"></textarea>
                            </div>
                            <div class="d-grid">
                                <button type="submit" name="register" class="btn btn-primary">Register</button>
                            </div>
                        </form>
                    </div>
                    <div class="register-footer">
                        <p>Already have an account? <a href="login.php">Login</a></p>
                    </div>
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
        const confirm_password = document.getElementById('confirm_password').value;
        const phone = document.getElementById('phone').value;

        // Reset error messages
        document.getElementById('fullname-error').innerText = '';
        document.getElementById('email-error').innerText = '';
        document.getElementById('password-error').innerText = '';
        document.getElementById('confirm-password-error').innerText = '';
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

        // Confirm password validation
        if (confirm_password === '') {
            document.getElementById('confirm-password-error').innerText = 'Please confirm your password.';
            isValid = false;
        } else if (password !== confirm_password) {
            document.getElementById('confirm-password-error').innerText = 'Passwords do not match.';
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