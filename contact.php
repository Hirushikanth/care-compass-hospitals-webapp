<?php
// Includes configuration and functions
include('includes/config.php');
include('includes/functions.php');
include('includes/db.php'); // Include Database class

$db = new Database(); // Instantiate Database class

// Initialize variables for feedback form
$feedback_submission_success = false;
$feedback_errors = [];
$feedback_fullname = $feedback_email = $feedback_subject = $feedback_message = '';
$feedback_text = '';  
$feedback_email = '';
$feedback_rating = '';

// Handle feedback form submission
if (isset($_POST['submit_feedback_form'])) {
    // Get form data
    $feedback_fullname = sanitize_input($_POST['name']); // Optional name
    $feedback_email = sanitize_input($_POST['email']);   // Optional email
    $feedback_text = sanitize_input($_POST['feedback_message']); // Feedback message (renamed from 'message' to 'feedback_message' to avoid confusion)
    $feedback_rating = isset($_POST['rating']) ? intval($_POST['rating']) : null; // Optional rating

    // Server-side validation for feedback form
    $feedback_errors = [];
    if (empty($feedback_text)) {
        $feedback_errors['feedback_message'] = "Feedback message is required.";
    }
    if (!empty($feedback_email) && !filter_var($feedback_email, FILTER_VALIDATE_EMAIL)) {
        $feedback_errors['feedback_email'] = "Invalid email format.";
    }

    // If no validation errors, submit feedback to database
    if (empty($feedback_errors)) {
        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null; // Get user ID if logged in, otherwise null
        $success = $db->createFeedback($userId, $feedback_fullname, $feedback_email, $feedback_text, $feedback_rating);

        if ($success) {
            $feedback_submission_success = true;
        } else {
            $feedback_errors[] = "Error submitting feedback. Please try again later."; // Database error
        }
    }
}


// Handle contact form submission (basic - just display a thank you message for now)
$contact_submission_success = false;
if (isset($_POST['submit_contact_form'])) {
    // ... (Your existing contact form submission handling - keep it as is for now if it's just displaying a message) ...
    $contact_submission_success = true; // Keep the success message logic
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
    <style>
        /* ... (Your existing CSS styles from contact.php) ... */
        .feedback-form-card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.05);
            padding: 2rem;
            background-color: var(--secondary-light-teal); /* Different background for feedback form */
            margin-top: 2rem; /* Add some margin above feedback form */
        }

        .feedback-form-card h3 {
            color: var(--primary-teal);
            font-weight: 600;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>

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
                        <?php if ($contact_submission_success): ?>
                            <div class="alert alert-success" role="alert">
                                Thank you for your message! We will get back to you as soon as possible.
                            </div>
                        <?php endif; ?>
                        <form method="post">
                            <div class="mb-3">
                                <label for="name" class="form-label">Your Name (Optional)</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($feedback_fullname) ?>">
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Your Email (Optional)</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($feedback_email) ?>">
                            </div>
                            <div class="mb-3">
                                <label for="subject" class="form-label">Subject</label>
                                <input type="text" class="form-control" id="subject" name="subject" required value="<?= htmlspecialchars($feedback_subject) ?>">
                            </div>
                            <div class="mb-3">
                                <label for="message" class="form-label">Message</label>
                                <textarea class="form-control" id="message" name="message" rows="5" required><?= htmlspecialchars($feedback_message) ?></textarea>
                            </div>
                            <button type="submit" name="submit_contact_form" class="btn btn-primary">Send Message</button>
                        </form>
                    </div>

                    <!-- Feedback Form Card -->
                    <div class="feedback-form-card">
                        <h3>Patient Feedback</h3>
                        <?php if ($feedback_submission_success): ?>
                            <div class="alert alert-success" role="alert">
                                Thank you for your feedback! We appreciate your input.
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($feedback_errors)): ?>
                            <div class="alert alert-danger" role="alert">
                                <ul>
                                    <?php foreach ($feedback_errors as $error): ?>
                                        <li><?= htmlspecialchars($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        <form method="post">
                            <div class="mb-3">
                                <label for="feedback_message" class="form-label">Your Feedback</label>
                                <textarea class="form-control <?php echo isset($feedback_errors['feedback_message']) ? 'is-invalid' : ''; ?>" id="feedback_message" name="feedback_message" rows="5" required><?= htmlspecialchars($feedback_text) ?></textarea>
                                <?php if (isset($feedback_errors['feedback_message'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($feedback_errors['feedback_message']) ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="mb-3">
                                <label for="rating" class="form-label">Rating (Optional, 1-5 stars)</label>
                                <select class="form-control" id="rating" name="rating">
                                    <option value="">-- Select Rating --</option>
                                    <option value="1">★☆☆☆☆ - Very Poor</option>
                                    <option value="2">★★☆☆☆ - Poor</option>
                                    <option value="3">★★★☆☆ - Average</option>
                                    <option value="4">★★★★☆ - Good</option>
                                    <option value="5">★★★★★ - Excellent</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="name" class="form-label">Your Name (Optional)</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($feedback_fullname) ?>">
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Your Email (Optional)</label>
                                <input type="email" class="form-control <?php echo isset($feedback_errors['feedback_email']) ? 'is-invalid' : ''; ?>" id="email" name="email" value="<?= htmlspecialchars($feedback_email) ?>">
                                <?php if (isset($feedback_errors['feedback_email'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($feedback_errors['feedback_email']) ?></div>
                                <?php endif; ?>
                            </div>
                            <button type="submit" name="submit_feedback_form" class="btn btn-primary">Submit Feedback</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer py-5" id="contact">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <h4>Contact Us</h4>
                    <p><i class="bi bi-geo-alt"></i> 123 Healthcare Ave, Medical City</p>
                    <p><i class="bi bi-telephone"></i> 1-800-CARE (2273)</p>
                    <p><i class="bi bi-envelope"></i> info@carecompass.com</p>
                </div>
                <div class="col-md-4">
                    <h4>Quick Links</h4>
                    <ul class="list-unstyled">
                        <li><a href="about_us.php" class="text-white">About Us</a></li>
                        <li><a href="index.php#services" class="text-white">Services</a></li>
                        <li><a href="doctors.php" class="text-white">Doctors</a></li>
                        <li><a href="contact.php" class="text-white">Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h4>Connect With Us</h4>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-white"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="text-white"><i class="bi bi-twitter"></i></a>
                        <a href="#" class="text-white"><i class="bi bi-linkedin"></i></a>
                        <a href="#" class="text-white"><i class="bi bi-instagram"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>