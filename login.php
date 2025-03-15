<?php
// Include necessary files (config.php, db.php, functions.php)
include('includes/config.php');
include('includes/db.php');
include('includes/functions.php');

// Start session (if not already started)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is already logged in, redirect if necessary
if (isset($_SESSION['user_id'])) {
    // Check if user_role is set before using it
    if (isset($_SESSION['user_role'])) {
        // Redirect to appropriate dashboard based on user role
        $redirectUrl = '';
        if ($_SESSION['user_role'] == 'admin') {
            $redirectUrl = "admin/dashboard.php";
        } elseif ($_SESSION['user_role'] == 'patient') {
            $redirectUrl = "patient/dashboard.php";
        } elseif ($_SESSION['user_role'] == 'staff') {
            $redirectUrl = "staff/dashboard.php";
        }
        

        if ($redirectUrl) {
            header("Location: " . $redirectUrl);
            exit;
        } else {
            // Handle invalid user role (maybe destroy session and redirect to login)
            session_destroy();
            header("Location: login.php");
            exit;
        }
    } else {
        // Handle the case where user_role is not set (maybe a corrupted session)
        // You might want to destroy the session and redirect to the login page
        session_destroy();
        header("Location: login.php");
        exit;
    }
}

// Generate CSRF token
$csrf_token = generate_csrf_token();

// Handle login form submission
$error_message = "";
if (isset($_POST['login'])) {
    // Verify CSRF token
    if (!verify_csrf_token()) {
        die("CSRF token validation failed."); // Or display a user-friendly error message and exit.
    }

    $email = sanitize_input($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error_message = "Email and password are required.";
    } else {
        $db = new Database();
        $user = $db->getUserByEmail($email);

        if ($user && password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['user_type'];
            error_log("Login: Session variables set - user_id: " . $_SESSION['user_id'] . ", user_role: " . $_SESSION['user_role']);

            // Redirect to appropriate dashboard based on user role using a variable
            $redirectUrl = '';
            if ($user['user_type'] == 'admin') {
                $redirectUrl = "admin/dashboard.php";
            } elseif ($user['user_type'] == 'patient') {
                $redirectUrl = "patient/dashboard.php";
            } elseif ($user['user_type'] == 'staff') {
                $redirectUrl = "staff/dashboard.php";
            }

            if ($redirectUrl) {
                header("Location: " . $redirectUrl);
                exit;
            } else {
                // Handle the case where the user has an invalid role (maybe display an error message)
                $error_message = "Invalid user role.";
            }
        } else {
            $error_message = "Invalid email or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Care Compass Connect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #f0f8ff; /* Light background from the palette */
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Nunito', sans-serif;
        }
        .login-card {
            background-color: #fff;
            border-radius: 0.75rem; /* Slightly less rounded */
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.05); /* Softer shadow */
            overflow: hidden;
            width: 100%;
            max-width: 450px; /* Adjust max width for better spacing */
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            margin: 20px auto;
        }
        .login-card:hover {
            transform: scale(1.01);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.07);
        }
        .login-header {
            background-color: #046A7A; /* Dark teal from the palette */
            color: white;
            padding: 1.5rem; /* Slightly reduced padding */
            text-align: center;
        }
        .login-header h2 {
            font-size: 1.75rem;
            margin-bottom: 0.25rem;
            font-weight: 700;
        }
        .login-header p {
            font-size: 0.9rem;
            opacity: 0.8;
            margin-bottom: 0;
        }
        .login-form {
            padding: 1.5rem;
        }
        .form-label {
            font-size: 0.95rem;
            color: #343a40;
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
            background-color: #046A7A; /* Dark teal */
            border-color: #046A7A;
            padding: 0.75rem 1.25rem;
            font-size: 1.05rem;
            border-radius: 0.3rem;
            transition: background-color 0.2s ease-in-out, border-color 0.2s ease-in-out;
        }
        .btn-primary:hover, .btn-primary:focus {
            background-color: #034e5a; /* Darker shade on hover/focus */
            border-color: #034e5a;
            box-shadow: 0 0 0 0.2rem rgba(4, 106, 122, 0.5); /* Stronger focus shadow */
        }
        .login-footer {
            text-align: center;
            padding: 1rem 1.5rem;
            border-top: 1px solid #e9ecef; /* Subtle divider */
        }
        .login-footer p {
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            color: #6c757d;
        }
        .login-footer a {
            color: #046A7A; /* Dark teal for links */
            text-decoration: none;
            transition: color 0.2s ease-in-out;
        }
        .login-footer a:hover {
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
                <div class="login-card">
                    <div class="login-header">
                        <h2 class="mb-0">Care Compass Connect</h2>
                        <p class="mb-0">Welcome back! Please log in to your account.</p>
                    </div>
                    <div class="login-form">
                        <?php if (!empty($error_message)): ?>
                            <div class="alert alert-danger" role="alert">
                                <?= $error_message ?>
                            </div>
                        <?php endif; ?>
                        <form method="post">
                            <!-- CSRF Token Hidden Input -->
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="d-grid mb-3">
                                <button type="submit" name="login" class="btn btn-primary btn-lg">Login</button>
                            </div>
                        </form>
                    </div>
                    <div class="login-footer">
                        <p>Don't have an account? <a href="register.php">Register</a></p>
                        <p><a href="forgot_password.php">Forgot Password?</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>