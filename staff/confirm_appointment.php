<?php
// Include necessary files
include('../includes/config.php');
include('../includes/db.php');
include('../includes/functions.php');
require_once('../includes/tcpdf/tcpdf.php'); // Include TCPDF library

// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in and is a staff member
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'staff') {
    header("Location: ../login.php");
    exit;
}

$db = new Database();

// Get appointment ID from the URL
$appointmentId = isset($_GET['id']) ? $_GET['id'] : null;

if (!$appointmentId) {
    echo "Appointment ID not provided.";
    exit;
}

// Fetch appointment details
$appointment = $db->getAppointmentById($appointmentId);

if (!$appointment) {
    echo "Appointment not found.";
    exit;
}

// Generate CSRF token BEFORE displaying the form
$csrf_token = generate_csrf_token();

$confirmation_success = false; // Flag to indicate successful confirmation
$error_message = '';
$price = ''; // Initialize price variable

// Handle appointment confirmation via POST
if (isset($_POST['confirm_appointment'])) {
    // Verify CSRF token at the VERY BEGINNING of form processing
    if (!verify_csrf_token()) {
        // CSRF token verification failed!  Reject the request.
        die("CSRF token validation failed."); // Or display a user-friendly error message and exit.
    }

    $price = $_POST['appointment_price']; // Get price from form input

    // Validate Price (Server-Side)
    if (!is_numeric($price) || $price < 0) {
        $error_message = "Please enter a valid non-negative price.";
    } else {
        $price = floatval($price); // Convert to float for database storage

        $success = $db->updateAppointmentStatusAndPrice($appointmentId, 'confirmed', $price); // Use the new method

        if ($success) {
            $confirmation_success = true;

            // Fetch appointment data again to include the price
            $appointmentWithPrice = $db->getAppointmentById($appointmentId);

            // Generate PDF Bill (Placeholder - Email sending and saving not yet implemented)
            $pdfContent = generateAppointmentBillPDF($appointmentWithPrice);

            // For now, just save the PDF to a temporary location (for testing)
            $billFileName = 'temp_bill_appointment_' . $appointmentId . '.pdf';
            $billFilePath = '../assets/Bills/' . $billFileName; // Create 'assets/temp_bills/' directory
            if (!file_exists('../assets/Bills')) { // Create directory if it doesn't exist
                mkdir('../assets/Bills', 0777, true);
            }
            file_put_contents($billFilePath, $pdfContent);

            // Success message with PDF generation info (for now)
            $success_message = "Appointment confirmed successfully! Bill generated and saved temporarily to: " . $billFilePath;


        } else {
            $error_message = "Error confirming appointment and updating price.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Appointment - Care Compass Connect</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <!-- Add your custom styles if needed -->
</head>
<body>
    <div class="container">
        <h2>Confirm Appointment</h2>

        <?php if ($confirmation_success): ?>
            <div class="alert alert-success"><?= $success_message ?></div>
            <a href="dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
        <?php else: ?>
            <?php if ($error_message): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>

            <p>Are you sure you want to confirm the following appointment?</p>
            <p><strong>Appointment ID:</strong> <?= htmlspecialchars($appointment['id']) ?></p>
            <p><strong>Patient Name:</strong> <?= htmlspecialchars($appointment['patient_name']) ?></p>
            <p><strong>Doctor Name:</strong> <?= htmlspecialchars($appointment['doctor_name']) ?></p>
            <p><strong>Date:</strong> <?= htmlspecialchars($appointment['appointment_date']) ?></p>
            <p><strong>Time:</strong> <?= htmlspecialchars($appointment['appointment_time']) ?></p>

            <div class="card mb-3">
                <div class="card-body">
                    <form method="post">
                        <!-- ADD THIS HIDDEN INPUT FIELD for CSRF token -->
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

                        <div class="mb-3">
                            <label for="appointment_price" class="form-label">Appointment Price ($):</label>
                            <input type="number" class="form-control" id="appointment_price" name="appointment_price" value="<?= htmlspecialchars($price) ?>" step="0.01" required>
                        </div>

                        <button type="submit" name="confirm_appointment" class="btn btn-success">Yes, Confirm Appointment</button>
                        <a href="view_appointment.php?id=<?= $appointment['id'] ?>" class="btn btn-secondary ms-2">No, Go Back</a>
                    </form>
                </div>
            </div>


        <?php endif; ?>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>