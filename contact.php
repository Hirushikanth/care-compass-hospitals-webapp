<?php
// Includes configuration and functions
include('includes/config.php');
include('includes/functions.php');

// Start session (if not already started) - Make sure session_start() is at the VERY top of the file
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$db = new Database(); // Instantiate Database class - You might need this later if you decide to store contact messages in DB

// Generate CSRF token BEFORE displaying the form
$csrf_token = generate_csrf_token();

// Handle form submission
$submission_error = false; // Flag to track submission errors
$submission_success = false;
if (isset($_POST['submit_contact_form'])) {
    // Verify CSRF token at the VERY BEGINNING of form processing
    if (!verify_csrf_token()) {
        // CSRF token verification failed!  Reject the request.
        die("CSRF token validation failed."); // In production, redirect to an error page or log.
        // In this example, for simplicity, we'll just set an error flag:
        $submission_error = true;
    }

    if (!$submission_error) { // Only process form if CSRF token is valid
        // In a real application, you would:
        // 1. Validate form data (server-side) -  **You should add validation here!**
        // 2. Sanitize form data - **You should sanitize input here!**
        // 3. Send email to hospital (using mail() or a library like PHPMailer) - **Implement email sending!**
        // 4. Optionally, store the message in the database (using your Database class) - **Consider storing messages!**

        // For now, just set success to true for demonstration:
        $submission_success = true;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Care Compass Hospital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <!-- Navigation -->
    <?php include('includes/header.php'); ?>

    <!-- Contact Header -->
    <header class="contact-header">
        <div class="container">
            <h1>Get in Touch With Us</h1>
            <p class="lead">We're here to answer your questions and provide support.</p>
        </div>
    </header>

    <!-- Contact Section -->
    <section class="contact-section">
        <div class="container">
            <div class="row">
                <div class="col-md-4 contact-info">
                    <h3>Contact Information</h3>
                    <p><i class="bi bi-geo-alt me-2 text-primary"></i> <?= HOSPITAL_ADDRESS ?></p>
                    <p><i class="bi bi-telephone me-2 text-primary"></i> Phone: <?= HOSPITAL_PHONE ?></p>
                    <p><i class="bi bi-envelope me-2 text-primary"></i> Email: info@carecompass.com</p>
                    <!-- Add social media links here if needed -->
                </div>
                <div class="col-md-8">
                    <div class="contact-form-card">
                        <h3>Send us a Message</h3>
                        <?php if ($submission_success): ?>
                            <div class="alert alert-success" role="alert">
                                Thank you for your message! We will get back to you as soon as possible.
                            </div>
                        <?php endif; ?>
                        <?php if ($submission_error): ?>
                            <div class="alert alert-danger" role="alert">
                                There was an error processing your request. Please try again.
                            </div>
                        <?php endif; ?>
                        <form method="post">
                            <!-- CSRF Token Hidden Input -->
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

                            <div class="mb-3">
                                <label for="name" class="form-label">Your Name (Optional)</label>
                                <input type="text" class="form-control" id="name" name="name">
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Your Email (Optional)</label>
                                <input type="email" class="form-control" id="email" name="email">
                            </div>
                            <div class="mb-3">
                                <label for="subject" class="form-label">Subject</label>
                                <input type="text" class="form-control" id="subject" name="subject" required>
                            </div>
                            <div class="mb-3">
                                <label for="message" class="form-label">Message</label>
                                <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                            </div>
                            <button type="submit" name="submit_contact_form" class="btn btn-primary">Send Message</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include('includes/footer.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>