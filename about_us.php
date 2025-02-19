<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - CareCompass Hospital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-teal: #046A7A;
            --light-background: #f0f8ff;
            --secondary-light-teal: #cce8ed;
            --darker-teal: #034e5a;
            --text-color: #343a40;
            --border-color: #e9ecef;
        }

        body {
            font-family: 'Nunito', sans-serif;
            color: var(--text-color);
            background-color: var(--light-background);
            line-height: 1.7;
        }

        .about-header {
            background: linear-gradient(rgba(4, 106, 122, 0.7), rgba(4, 106, 122, 0.2)), url('assets/images/AboutUsHero.jpg'); /* Calming hero image */
            background-size: cover;
            background-position: center;
            color: white;
            padding: 150px 0;
            text-align: center;
        }

        .about-header h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .about-section {
            padding: 4rem 0;
            background-color: white;
        }

        .about-section h2 {
            color: var(--primary-teal);
            font-weight: 700;
            margin-bottom: 1.5rem;
        }

        .about-section p {
            font-size: 1.1rem;
            color: var(--text-color);
            opacity: 0.9;
        }

        .core-values {
            padding: 3rem 0;
            background-color: var(--light-background);
        }

        .core-values h2 {
            color: var(--primary-teal);
            font-weight: 700;
            margin-bottom: 2rem;
            text-align: center;
        }

        .core-value-item {
            text-align: center;
            padding: 1.5rem;
            border-radius: 0.75rem;
            background-color: white;
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }

        .core-value-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.07);
        }

        .core-value-item i {
            font-size: 2.5rem;
            color: var(--primary-teal);
            margin-bottom: 1rem;
        }

        .core-value-item h3 {
            font-weight: 600;
            margin-bottom: 0.75rem;
        }

        .core-value-item p {
            opacity: 0.8;
        }

        .footer {
            background-color: var(--primary-teal);
            color: white;
            padding-top: 3rem;
            padding-bottom: 3rem;
        }

        .footer h4 {
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        .footer p, .footer a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer a:hover {
            color: white;
        }

        .footer .bi {
            font-size: 1.2rem;
            margin-right: 0.5rem;
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
                    <li class="nav-item"><a class="nav-link" href="index.php#doctors">Doctors</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php#facilities">Facilities</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php#contact">Contact</a></li>
                    <li class="nav-item"><a class="btn btn-primary btn-sm ms-2" href="login.php">Login</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- About Us Header -->
    <header class="about-header">
        <div class="container">
            <h1>About CareCompass Hospital</h1>
            <p class="lead">Where Your Well-being is Our Heart's Work</p>
        </div>
    </header>

    <!-- Our Mission Section -->
    <section class="about-section">
        <div class="container">
            <div class="row">
                <div class="col-md-8 offset-md-2 text-center">
                    <h2>Our Mission</h2>
                    <p>At CareCompass Hospital, our mission is deeply rooted in a commitment to providing compassionate, world-class healthcare. We strive to be a beacon of hope and healing for every individual who walks through our doors.  Our purpose is to guide you on your journey to wellness, offering advanced medical expertise with a personal touch that makes you feel understood, cared for, and truly valued.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Vision Section -->
    <section class="about-section bg-light">
        <div class="container">
            <div class="row">
                <div class="col-md-8 offset-md-2 text-center">
                    <h2>Our Vision</h2>
                    <p>We envision a future where healthcare is not just a service, but a partnership. CareCompass Hospital aspires to lead the way in innovative medical practices, setting new standards for patient care and experience. We are dedicated to creating a healthier community, one where every person has access to exceptional medical services delivered with empathy and respect.  We aim to be the trusted compass guiding individuals and families towards a healthier, brighter future.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Core Values Section -->
    <section class="core-values py-5">
        <div class="container">
            <h2 class="mb-5">Our Core Values</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="core-value-item h-100">
                        <i class="bi bi-heart-fill"></i>
                        <h3>Compassion</h3>
                        <p>We lead with kindness and empathy, ensuring every patient feels heard and understood.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="core-value-item h-100">
                        <i class="bi bi-stars"></i>
                        <h3>Excellence</h3>
                        <p>We are committed to the highest standards of medical practice and continuous improvement.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="core-value-item h-100">
                        <i class="bi bi-shield-check"></i>
                        <h3>Trust</h3>
                        <p>We build lasting relationships with our patients through honesty, integrity, and reliability.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- History Section -->
    <section class="about-section">
        <div class="container">
            <div class="row">
                <div class="col-md-8 offset-md-2 text-center">
                    <h2>Our Story</h2>
                    <p>CareCompass Hospital began as a small clinic with a big heart, founded by a group of dedicated healthcare professionals who shared a vision of a more caring and accessible healthcare system. Over the years, we have grown into a leading hospital network, expanding our services and facilities while staying true to our founding principles.  From our humble beginnings to our current state-of-the-art hospital, our journey has always been guided by a deep-seated commitment to our patients and the community we serve. We are proud of our history and excited about the future as we continue to evolve and innovate in healthcare.</p>
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
                        <li><a href="index.php#doctors" class="text-white">Doctors</a></li>
                        <li><a href="index.php#contact" class="text-white">Contact</a></li>
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