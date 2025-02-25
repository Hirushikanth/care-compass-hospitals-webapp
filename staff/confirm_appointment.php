<?php
// Include necessary files
include('../includes/config.php');
include('../includes/db.php');
include('../includes/functions.php');
require_once('../includes/tcpdf/tcpdf.php'); // Include TCPDF library
require '../includes/PHPMailer/src/Exception.php';
require '../includes/PHPMailer/src/PHPMailer.php';
require '../includes/PHPMailer/src/SMTP.php';

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

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

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

    // Validate Price (Server-Side) - Enhanced Validation
    if (empty($price)) {
        $errors[] = "Appointment price is required.";
    } elseif (!is_numeric($price) || $price < 0) {
        $errors[] = "Please enter a valid non-negative price.";
    }

    if (empty($errors)) { // Proceed only if no validation errors
        $price = floatval($price); // Convert to float for database storage

        $success = $db->updateAppointmentStatusAndPrice($appointmentId, 'confirmed', $price); // Use the new method

        if ($success) {
            $confirmation_success = true;

            // Fetch appointment data again to include the price (for PDF generation)
            $appointmentWithPrice = $db->getAppointmentById($appointmentId);

            // Generate PDF Bill
            $pdfContent = generateAppointmentBillPDF($appointmentWithPrice);

            // Save the PDF to a temporary location (for testing - you'll enhance this later)
            $billFileName = 'temp_bill_appointment_' . $appointmentId . '.pdf';
            $billFilePath = '../assets/Bills/' . $billFileName; // Ensure 'assets/Bills/' directory exists
            if (!file_exists('../assets/Bills')) { // Create directory if it doesn't exist
                mkdir('../assets/Bills', 0777, true);
            }
            file_put_contents($billFilePath, $pdfContent);

            // Success message with PDF generation info (for now)
            $success_message = "Appointment confirmed successfully! Bill generated and saved temporarily to: " . $billFilePath;

        } else {
            $error_message = "Error confirming appointment and updating price in the database."; // More specific error
            error_log("Database error confirming appointment and updating price. Appointment ID: " . $appointmentId . " Error: " . $db->connection->error); // Log detailed error
        }
    }

    try {
        // Create a new PHPMailer instance
        $mail = new PHPMailer(true); // 'true' enables exceptions for better error handling

        // --- Server settings (Using Mailtrap SMTP credentials) ---
        $mail->isSMTP();                                        // Send using SMTP
        $mail->Host       = MAILTRAP_SMTP_HOST;                 // Mailtrap SMTP server hostname (from config.php)
        $mail->SMTPAuth   = true;                               // Enable SMTP authentication
        $mail->Username   = MAILTRAP_SMTP_USERNAME;             // Mailtrap SMTP username (from config.php)
        $mail->Password   = MAILTRAP_SMTP_PASSWORD;             // Mailtrap SMTP password (from config.php)
        $mail->SMTPSecure = MAILTRAP_SMTP_ENCRYPTION;             // Enable TLS encryption (from config.php)
        $mail->Port       = MAILTRAP_SMTP_PORT;                // TCP port to connect to (Mailtrap - from config.php)

        // --- Recipients ---
        $mail->setFrom('hi@demomailtrap.com', 'Care Compass Hospitals'); // Replace with your *sending* email address (can be your Mailtrap email for testing)
        $mail->addAddress($appointmentWithPrice['email'], $appointmentWithPrice['patient_name']);     // Add recipient (patient's email from appointment data)

        // --- Attachments (Attach the generated PDF bill) ---
        $mail->addStringAttachment($pdfContent, 'appointment_bill_' . $appointmentId . '.pdf', 'base64', 'application/pdf'); // Attach PDF content

        // --- Content ---
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = 'Your Appointment Bill from Care Compass Hospitals';
        $mail->Body    = 'Dear ' . htmlspecialchars($appointmentWithPrice['patient_name']) . ',<br><br>' .
                           'Please find attached your bill for your upcoming appointment on ' . htmlspecialchars($appointmentWithPrice['appointment_date']) . ' at ' . htmlspecialchars($appointmentWithPrice['appointment_time']) . '.<br><br>' .
                           'Thank you for choosing Care Compass Hospitals.<br><br>Sincerely,<br>Care Compass Hospitals';
        $mail->AltBody = 'Dear ' . htmlspecialchars($appointmentWithPrice['patient_name']) . ",\n\nPlease find attached your bill for your upcoming appointment on " . htmlspecialchars($appointmentWithPrice['appointment_date']) . " at " . htmlspecialchars($appointmentWithPrice['appointment_time']) . ".\n\nThank you for choosing Care Compass Hospitals.\n\nSincerely,\nCare Compass Hospitals"; // Plain text version for email clients that don't support HTML

        $mail->send();
        echo '<div class="alert alert-success">Appointment confirmed successfully! Bill generated and sent to patient email (using Mailtrap)!</div>';

    } catch (Exception $e) {
        echo '<div class="alert alert-warning">Appointment confirmed successfully! Bill generated, but there was an error sending the email via Mailtrap. Error: ' . $mail->ErrorInfo . '</div>';
        error_log("PHPMailer Error sending bill email: " . $mail->ErrorInfo); // Log email sending error
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Appointment - Care Compass Connect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include('../includes/header.php'); ?>

    <div class="container py-5">
        <h2>Confirm Appointment</h2>

        <?php if ($confirmation_success): ?>
            <div class="alert alert-success"><?= $success_message ?></div>
            <a href="dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
        <?php else: ?>
            <?php if ($error_message): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>

            <p>Are you sure you want to confirm the following appointment?</p>
            <ul class="list-group mb-3">
                <li class="list-group-item"><strong>Appointment ID:</strong> <?= htmlspecialchars($appointment['id']) ?></li>
                <li class="list-group-item"><strong>Patient Name:</strong> <?= htmlspecialchars($appointment['patient_name']) ?></li>
                <li class="list-group-item"><strong>Doctor Name:</strong> <?= htmlspecialchars($appointment['doctor_name']) ?></li>
                <li class="list-group-item"><strong>Date:</strong> <?= htmlspecialchars($appointment['appointment_date']) ?></li>
                <li class="list-group-item"><strong>Time:</strong> <?= htmlspecialchars($appointment['appointment_time']) ?></li>
            </ul>

            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="post">
                        <!-- CSRF token hidden input -->
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

                        <div class="mb-3">
                            <label for="appointment_price" class="form-label">Appointment Price ($):</label>
                            <input type="number" class="form-control <?php echo isset($errors['appointment_price']) ? 'is-invalid' : ''; ?>" id="appointment_price" name="appointment_price" value="<?= htmlspecialchars($price) ?>" step="0.01" required>
                            <?php if (isset($errors['appointment_price'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['appointment_price']) ?></div>
                            <?php endif; ?>
                        </div>

                        <button type="submit" name="confirm_appointment" class="btn btn-success">
                            <i class="bi bi-check-circle me-1"></i> Yes, Confirm Appointment
                        </button>
                        <a href="view_appointment.php?id=<?= $appointment['id'] ?>" class="btn btn-secondary ms-2">
                            <i class="bi bi-arrow-left me-1"></i> No, Go Back
                        </a>
                    </form>
                </div>
            </div>

        <?php endif; ?>
    </div>

    <?php include('../includes/footer.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>