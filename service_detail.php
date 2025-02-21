<?php
include('includes/config.php');
include('includes/functions.php');

// You will need to define these variables in each individual service page:
// $serviceName = "Service Name";
// $serviceHeroImage = "assets/images/ServiceHeroImage.jpg"; // Path to hero image
// $serviceDescription = "Detailed description of the service...";

if (!isset($serviceName) || !isset($serviceHeroImage) || !isset($serviceDescription)) {
    // Fallback in case variables are not defined (for direct access to template)
    $serviceName = "Service Detail Page";
    $serviceHeroImage = "assets/images/DefaultServiceHero.jpg"; // You can use a default image
    $serviceDescription = "This is a placeholder service detail page. Please define \$serviceName, \$serviceHeroImage, and \$serviceDescription in your individual service page.";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($serviceName) ?> - CareCompass Hospital</title>
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
            --primary-teal: #046A7A;
            --light-background: #f0f8ff;
            --secondary-light-teal: #cce8ed;
            --darker-teal: #034e5a;
            --text-color: #343a40;
            --border-color: #e9ecef;
        }

        .service-detail-header {
            background: linear-gradient(rgba(4, 106, 122, 0.7), rgba(4, 106, 122, 0.2)), url('<?= htmlspecialchars($serviceHeroImage) ?>');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 150px 0;
            text-align: center;
        }

        .service-detail-header h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .service-detail-section {
            padding: 4rem 0;
            background-color: white;
        }

        .service-detail-section h2 {
            color: var(--primary-teal);
            font-weight: 700;
            margin-bottom: 1.5rem;
        }

        .service-detail-section p {
            font-size: 1.1rem;
            color: var(--text-color);
            opacity: 0.9;
            line-height: 1.8;
        }

        /* Add more specific styles for service detail pages if needed */

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
                    <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                    <li class="nav-item"><a class="btn btn-primary btn-sm ms-2" href="login.php">Login</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Service Detail Header -->
    <header class="service-detail-header">
        <div class="container">
            <h1><?= htmlspecialchars($serviceName) ?></h1>
            <p class="lead"><!-- You can add a tagline or lead text here in individual pages --></p>
        </div>
    </header>

    <!-- Service Detail Section -->
    <section class="service-detail-section">
        <div class="container">
            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <h2>About <?= htmlspecialchars($serviceName) ?></h2>
                    <p><?= nl2br(htmlspecialchars($serviceDescription)) ?></p>
                    <!-- Add more sections here as needed (e.g., Facilities, Doctors, etc.) -->
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
                        <li><a href="index.php#about" class="text-white">About Us</a></li>
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