<?php
include('includes/config.php');
include('includes/db.php');
include('includes/functions.php');

// Handle form submission
if (isset($_POST['reset_request'])) {
    $email = sanitize_input($_POST['email']);

    $errors = [];
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (empty($errors)) {
        $db = new Database();
        $user = $db->getUserByEmail($email);

        if ($user) {
            // Generate a unique token (you can use bin2hex(random_bytes()) for this)
            $token = bin2hex(random_bytes(32)); // Example: Generates a 64-character hex string

            // Set an expiration time for the token (e.g., 1 hour from now)
            $token_expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // Store the token and its expiration time in the database (you'll need to add these columns to your 'users' table)
            $success = $db->updateUserPasswordResetToken($user['id'], $token, $token_expires);

            if ($success) {
                // Send password reset email
                $resetLink = "http://yourdomain.com/reset_password.php?token=$token"; // Replace with your actual domain
                $subject = "Password Reset Request";
                $message = "Please click the following link to reset your password: <a href=\"$resetLink\">$resetLink</a>";
                $headers = "From: noreply@yourdomain.com\r\n"; // Update with your email
                $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

                // Use the mail() function or a library like PHPMailer to send the email
                if (mail($email, $subject, $message, $headers)) {
                    echo '<div class="alert alert-success">Password reset link sent to your email.</div>';
                } else {
                    echo '<div class="alert alert-danger">Error sending email. Please try again later.</div>';
                }
            } else {
                echo '<div class="alert alert-danger">Error updating password reset token.</div>';
            }
        } else {
            echo '<div class="alert alert-danger">Email not found in our records.</div>';
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
    <title>Forgot Password - Care Compass Connect</title>
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
        .forgot-password-card {
            background-color: #fff;
            border-radius: 0.75rem;
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.05);
            overflow: hidden;
            width: 100%;
            max-width: 400px;
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .forgot-password-card:hover {
            transform: scale(1.01);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.07);
        }
        .forgot-password-header {
            background-color: #046A7A; /* Dark teal from the palette */
            color: white;
            padding: 1.5rem;
            text-align: center;
        }
        .forgot-password-header h2 {
            font-size: 1.75rem;
            margin-bottom: 0;
            font-weight: 700;
        }
        .forgot-password-form {
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
        .forgot-password-footer {
            text-align: center;
            padding: 1rem 1.5rem;
            border-top: 1px solid #e9ecef;
        }
        .forgot-password-footer a {
            color: #046A7A;
            text-decoration: none;
            transition: color 0.2s ease-in-out;
        }
        .forgot-password-footer a:hover {
            text-decoration: underline;
            color: #034e5a;
        }
        .alert-danger, .alert-success {
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="forgot-password-card">
                    <div class="forgot-password-header">
                        <h2>Forgot Password</h2>
                    </div>
                    <div class="forgot-password-form">
                        <?php
                        if (isset($_POST['reset_request'])) {
                            if (!empty($errors)) {
                                echo '<div class="alert alert-danger"><ul>';
                                foreach ($errors as $error) {
                                    echo '<li>' . $error . '</li>';
                                }
                                echo '</ul></div>';
                            } else if (isset($success)) {
                                echo '<div class="alert alert-success">Password reset link sent to your email.</div>';
                            }
                        }
                        ?>
                        <p class="text-muted">Enter your email address and we'll send you a link to reset your password.</p>
                        <form method="post">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" name="reset_request" class="btn btn-primary">Send Reset Link</button>
                            </div>
                        </form>
                    </div>
                    <div class="forgot-password-footer">
                        <a href="login.php">Back to Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>