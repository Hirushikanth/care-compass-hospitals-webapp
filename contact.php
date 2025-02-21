<?php
// Includes configuration and functions (if needed for form processing later)
include('includes/config.php');
include('includes/functions.php');

// Handle form submission (basic - just display a thank you message for now)
$submission_success = false;
if (isset($_POST['submit_contact_form'])) {
    // In a real application, you would:
    // 1. Validate form data (server-side)
    // 2. Sanitize form data
    // 3. Send email to hospital (using mail() or a library like PHPMailer)
    // 4. Optionally, store the message in the database (using your Database class)

    // For now, just set success to true for demonstration:
    $submission_success = true;
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
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            color: var(--text-color);
            background-color: var(--light-background);
        }
        :root {
            --primary-teal: #046A7A; /* Primary color from palette */
            --light-background: #f0f8ff; /* Light background from palette */
            --secondary-light-teal: #cce8ed; /* Secondary light teal from palette */
            --darker-teal: #034e5a; /* Darker teal for hover */
            --text-color: #343a40; /* Text color from palette */
            --border-color: #e9ecef; /* Border color from palette */
        }

        .contact-header {
            background: linear-gradient(rgba(4, 106, 122, 0.7), rgba(4, 106, 122, 0.2)), url('assets/images/ContactHero.jpg'); /* Replace with your contact hero image */
            background-size: cover;
            background-position: center;
            color: white;
            padding: 150px 0;
            text-align: center;
        }

        .contact-header h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .contact-section {
            padding: 4rem 0;
            background-color: white;
        }

        .contact-info {
            margin-bottom: 2rem;
        }

        .contact-info h3 {
            color: var(--primary-teal);
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .contact-info p {
            line-height: 1.8;
            margin-bottom: 0.75rem;
        }

        .contact-form-card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.05);
            padding: 2rem;
            background-color: var(--light-background); /* Light background for form card */
        }

        .contact-form-card h3 {
            color: var(--primary-teal);
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .form-control {
            border-radius: 0.3rem;
        }

        .btn-primary {
            background-color: var(--primary-teal);
            border-color: var(--primary-teal);
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: var(--darker-teal);
            border-color: var(--darker-teal);
        }
        .alert-success {
            margin-top: 1rem;
        }

    </style>
</head>
<body>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="assets/images/Logo.png" alt="CareCompass Logo" height="50">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php#services">Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="doctors.php">Doctors</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php#facilities">Facilities</a></li>
                    <li class="nav-item"><a class="nav-link active" href="contact.php">Contact</a></li>
                    <li class="nav-item"><a class="btn btn-primary btn-sm ms-2" href="login.php">Login</a></li>
                </ul>
            </div>
        </div>
    </nav>

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
                        <form method="post">
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