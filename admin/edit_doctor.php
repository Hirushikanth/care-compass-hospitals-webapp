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

// Get doctor ID from the URL
$doctorId = isset($_GET['id']) ? $_GET['id'] : null;

if (!$doctorId) {
    echo "Doctor ID not provided.";
    exit;
}

// Fetch doctor data
$doctor = $db->getDoctorById($doctorId);

if (!$doctor) {
    echo "Doctor not found.";
    exit;
}

// Fetch all branches for the dropdown
$branches = $db->getAllBranches(); // Fetch branches for branch selection dropdown

// Handle form submission
if (isset($_POST['edit_doctor'])) {
    $fullname = sanitize_input($_POST['fullname']);
    $email = sanitize_input($_POST['email']);
    $specialty = sanitize_input($_POST['specialty']);
    $qualifications = sanitize_input($_POST['qualifications']);
    $availability = sanitize_input($_POST['availability']);
    $phone = sanitize_input($_POST['phone']);
    $address = sanitize_input($_POST['address']);
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
    if (empty($specialty)) {
        $errors[] = "Specialty is required.";
    }
    if (empty($branchId)) { // Validate branch selection
        $errors[] = "Branch is required.";
    }
    // Add more server-side validation as needed

    // If no errors, proceed with updating the doctor
    if (empty($errors)) {
        $success = $db->updateDoctor($doctorId, $fullname, $email, $specialty, $qualifications, $availability, $phone, $address, $branchId); // Pass branchId to updateDoctor

        if ($success) {
            echo '<div class="alert alert-success">Doctor updated successfully!</div>';
            // Refetch doctor data to update the form
            $doctor = $db->getDoctorById($doctorId);
        } else {
            echo '<div class="alert alert-danger">Error updating doctor.</div>';
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
    <title>Edit Doctor - Care Compass Connect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <!-- Add your custom styles if needed -->
</head>
<body>
    <?php include('../includes/header.php'); ?>

    <div class="container">
        <div class="form-card">
            <h2 class="form-header">Edit Doctor</h2>

            <form method="post" onsubmit="return validateForm()">
                <?php if (isset($_POST['edit_doctor']) && empty($errors) && isset($success) && $success): ?>
                    <div class="alert alert-success"><?= $success_message ?></div>
                <?php endif; ?>
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?= $error ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                <div class="mb-3">
                    <label for="fullname" class="form-label">Full Name:</label>
                    <input type="text" class="form-control" id="fullname" name="fullname" value="<?= htmlspecialchars($doctor['fullname']) ?>" required>
                    <div id="fullname-error" class="text-danger"></div>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email:</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($doctor['email']) ?>" required>
                    <div id="email-error" class="text-danger"></div>
                </div>
                <div class="mb-3">
                    <label for="specialty" class="form-label">Specialty:</label>
                    <input type="text" class="form-control" id="specialty" name="specialty" value="<?= htmlspecialchars($doctor['specialty']) ?>" required>
                    <div id="specialty-error" class="text-danger"></div>
                </div>
                <div class="mb-3">
                    <label for="branch_id" class="form-label">Branch:</label>  <!-- Branch Dropdown -->
                    <select class="form-control" id="branch_id" name="branch_id" required>
                        <option value="">-- Select Branch --</option>
                        <?php foreach ($branches as $branchOption): ?>
                            <option value="<?= htmlspecialchars($branchOption['id']) ?>" <?= ($doctor['branch_id'] == $branchOption['id']) ? 'selected' : '' ?>><?= htmlspecialchars($branchOption['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div id="branch-id-error" class="text-danger"></div>
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone:</label>
                    <input type="tel" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($doctor['phone']) ?>">
                    <div id="phone-error" class="text-danger"></div>
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label">Address:</label>
                    <textarea class="form-control" id="address" name="address"><?= htmlspecialchars($doctor['address']) ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="qualifications" class="form-label">Qualifications:</label>
                    <textarea class="form-control" id="qualifications" name="qualifications"><?= htmlspecialchars($doctor['qualifications']) ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="availability" class="form-label">Availability:</label>
                    <textarea class="form-control" id="availability" name="availability"><?= htmlspecialchars($doctor['availability']) ?></textarea>
                </div>

                <button type="submit" name="edit_doctor" class="btn btn-primary">Update Doctor</button>
                <a href="manage_doctors.php" class="btn btn-secondary ms-2">Back to Manage Doctors</a>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function validateForm() {
            let isValid = true;
            const fullname = document.getElementById('fullname').value;
            const email = document.getElementById('email').value;
            const specialty = document.getElementById('specialty').value;
            const branchId = document.getElementById('branch_id').value; // Get branch ID
            const phone = document.getElementById('phone').value;

            // Reset error messages
            document.getElementById('fullname-error').innerText = '';
            document.getElementById('email-error').innerText = '';
            document.getElementById('specialty-error').innerText = '';
            document.getElementById('branch-id-error').innerText = ''; // Reset branch error
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

            // Specialty validation
            if (specialty.trim() === '') {
                document.getElementById('specialty-error').innerText = 'Specialty is required.';
                isValid = false;
            }
             // Branch validation
             if (branchId === '') {
                document.getElementById('branch-id-error').innerText = 'Branch is required.'; // Branch error message
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