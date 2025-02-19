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
if (isset($_POST['add_doctor'])) {
    $fullname = sanitize_input($_POST['fullname']);
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password']; // Hash this password!
    $specialty = sanitize_input($_POST['specialty']);
    $qualifications = sanitize_input($_POST['qualifications']);
    $availability = sanitize_input($_POST['availability']);
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
    if (empty($specialty)) {
        $errors[] = "Specialty is required.";
    }
    // Add more server-side validation as needed

    // Check if email already exists (only if it's not empty and valid)
    if (empty($errors) && !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        if ($db->getUserByEmail($email)) {
            $errors[] = "Email already exists.";
        }
    }

    // If no errors, proceed with adding the doctor
    if (empty($errors)) {
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert user (as 'staff')
        $user_id = $db->createUser($fullname, $email, $hashedPassword, 'staff', $phone, $address);

        if ($user_id) {
            // Insert doctor
            $success = $db->createDoctor($user_id, $specialty, $qualifications, $availability);

            if ($success) {
                echo '<div class="alert alert-success">Doctor added successfully!</div>';
            } else {
                echo '<div class="alert alert-danger">Error adding doctor.</div>';
            }
        } else {
            echo '<div class="alert alert-danger">Error creating user account for doctor.</div>';
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
    <title>Add Doctor - Care Compass Connect</title>
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
        .dashboard-header {
            background-color: #046A7A; /* Dark teal from the palette */
            color: white;
            padding: 2.5rem 0;
            margin-bottom: 3rem;
        }
        .dashboard-header h2 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0;
        }
        .dashboard-card {
            background-color: #fff;
            border-radius: 0.75rem;
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            overflow: hidden;
        }
        .dashboard-card:hover {
            transform: scale(1.01);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.07);
        }
        .dashboard-card-header {
            background-color: #046A7A;
            color: white;
            padding: 1.25rem;
            border-top-left-radius: 0.75rem;
            border-top-right-radius: 0.75rem;
        }
        .dashboard-card-header h3 {
            font-size: 1.5rem;
            margin-bottom: 0;
            font-weight: 600;
        }
        .card-body {
            padding: 1.5rem;
        }
        .form-label {
            font-size: 0.95rem;
            color: #343a40;
            margin-bottom: 0.3rem;
            font-weight: 600;
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
            border-color: #4e555b;
            box-shadow: 0 0 0 0.2rem rgba(108, 117, 125, 0.5);
        }
        .alert-danger, .alert-success {
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
    <header class="dashboard-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <h2>Add New Doctor</h2>
                </div>
            </div>
        </div>
    </header>

    <main class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <h3 class="mb-0">Doctor Information</h3>
                    </div>
                    <div class="card-body">
                        <?php
                        if (isset($_POST['add_doctor']) && !empty($errors)):
                            echo '<div class="alert alert-danger"><ul>';
                            foreach ($errors as $error) {
                                echo "<li>$error</li>";
                            }
                            echo '</ul></div>';
                        elseif (isset($_POST['add_doctor']) && empty($errors)):
                            echo '<div class="alert alert-success">Doctor added successfully!</div>';
                        endif;
                        ?>
                        <form method="post" onsubmit="return validateForm()">
                            <div class="mb-3">
                                <label for="fullname" class="form-label">Full Name:</label>
                                <input type="text" class="form-control" id="fullname" name="fullname" required>
                                <div id="fullname-error" class="text-danger"></div>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email:</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                                <div id="email-error" class="text-danger"></div>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password:</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <div id="password-error" class="text-danger"></div>
                            </div>
                            <div class="mb-3">
                                <label for="specialty" class="form-label">Specialty:</label>
                                <input type="text" class="form-control" id="specialty" name="specialty" required>
                                <div id="specialty-error" class="text-danger"></div>
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone:</label>
                                <input type="tel" class="form-control" id="phone" name="phone">
                                <div id="phone-error" class="text-danger"></div>
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">Address:</label>
                                <textarea class="form-control" id="address" name="address"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="qualifications" class="form-label">Qualifications:</label>
                                <textarea class="form-control" id="qualifications" name="qualifications"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="availability" class="form-label">Availability:</label>
                                <textarea class="form-control" id="availability" name="availability"></textarea>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" name="add_doctor" class="btn btn-primary">Add Doctor</button>
                                <a href="manage_doctors.php" class="btn btn-secondary">Back to Manage Doctors</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function validateForm() {
            let isValid = true;
            const fullname = document.getElementById('fullname').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const specialty = document.getElementById('specialty').value;
            const phone = document.getElementById('phone').value;

            // Reset error messages
            document.getElementById('fullname-error').innerText = '';
            document.getElementById('email-error').innerText = '';
            document.getElementById('password-error').innerText = '';
            document.getElementById('specialty-error').innerText = '';
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

            // Specialty validation
            if (specialty.trim() === '') {
                document.getElementById('specialty-error').innerText = 'Specialty is required.';
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