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

// Set active page for navigation highlighting
$active_page = 'appointments';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Appointment - Care Compass Connect</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <?php include('../includes/header.php'); ?>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include('../includes/sidebar.php'); ?>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="appointments.php">Appointments</a></li>
                        <li class="breadcrumb-item"><a href="view_appointment.php?id=<?= $appointmentId ?>">View Appointment</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Confirm Appointment</li>
                    </ol>
                </nav>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Confirm Appointment</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($confirmation_success): ?>
                            <div class="alert alert-success"><?= $success_message ?></div>
                            <a href="dashboard.php" class="btn btn-primary mt-3"><i class="fas fa-tachometer-alt me-2"></i>Back to Dashboard</a>
                            <a href="appointments.php" class="btn btn-secondary mt-3 ms-2"><i class="fas fa-calendar-check me-2"></i>View All Appointments</a>
                        <?php else: ?>
                            <?php if ($error_message): ?>
                                <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
                            <?php endif; ?>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">Appointment Details</h6>
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-borderless">
                                                <tr>
                                                    <th width="40%">Appointment ID:</th>
                                                    <td><?= htmlspecialchars($appointment['id']) ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Patient Name:</th>
                                                    <td><?= htmlspecialchars($appointment['patient_name']) ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Doctor Name:</th>
                                                    <td><?= htmlspecialchars($appointment['doctor_name']) ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Date:</th>
                                                    <td><?= htmlspecialchars($appointment['appointment_date']) ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Time:</th>
                                                    <td><?= htmlspecialchars($appointment['appointment_time']) ?></td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">Confirmation</h6>
                                        </div>
                                        <div class="card-body">
                                            <p class="lead">Are you sure you want to confirm this appointment?</p>
                                            <p>Confirming this appointment will generate a bill and update the appointment status.</p>
                                            
                                            <form method="post">
                                                <!-- CSRF token -->
                                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

                                                <div class="mb-3">
                                                    <label for="appointment_price" class="form-label">Appointment Price ($):</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                                                        <input type="number" class="form-control" id="appointment_price" name="appointment_price" value="<?= htmlspecialchars($price) ?>" step="0.01" required>
                                                    </div>
                                                </div>

                                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                                    <button type="submit" name="confirm_appointment" class="btn btn-success">
                                                        <i class="fas fa-check me-2"></i>Yes, Confirm Appointment
                                                    </button>
                                                    <a href="view_appointment.php?id=<?= $appointment['id'] ?>" class="btn btn-secondary ms-2">
                                                        <i class="fas fa-times me-2"></i>No, Go Back
                                                    </a>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Footer -->
    <?php include('../includes/footer.php'); ?>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/scripts.js"></script>
</body>
</html>