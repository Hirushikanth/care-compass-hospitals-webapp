<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CareCompass - Leading Healthcare Provider</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-teal: #046A7A; /* Primary color from palette */
            --light-background: #f0f8ff; /* Light background from palette */
            --secondary-light-teal: #cce8ed; /* Secondary light teal from palette */
            --darker-teal: #034e5a; /* Darker teal for hover */
            --text-color: #343a40; /* Text color from palette */
            --border-color: #e9ecef; /* Border color from palette */
        }


body {
font-family: 'Nunito', sans-serif;
color: var(--text-color);
background-color: var(--light-background);
}

/* Hero Section */
.hero-section {
    background: linear-gradient(rgba(4, 106, 122, 0.6), rgba(4, 106, 122, 0.1)), url('assets/images/Hero.jpg'); /* Using teal overlay */
    background-size: cover;
    background-position: center;
    color: white;
    padding: 120px 0; /* Increased padding for hero */
    text-align: center; /* Center align hero content */
}

.hero-section h1 {
    font-size: 3rem; /* Larger hero title */
    font-weight: 700;
    margin-bottom: 1rem;
}

.hero-section p {
    font-size: 1.2rem;
    margin-bottom: 2rem;
    opacity: 0.9;
}

.hero-section .btn-light {
    background-color: white;
    color: var(--primary-teal);
    border: none;
    transition: background-color 0.3s ease, color 0.3s ease;
}

.hero-section .btn-light:hover {
    background-color: var(--secondary-light-teal); /* Light teal hover */
    color: var(--primary-teal);
}

.hero-section .btn-danger {
    background-color: #dc3545; /* Keep danger button as is or adjust to palette if needed */
    border: none;
}

.emergency-contact {
    background-color: #dc3545;
    color: white;
    padding: 10px 20px;
    border-radius: 25px;
    font-weight: bold;
    margin-top: 2rem; /* Added margin */
    display: inline-block; /* To allow margin-top to work */
}

/* Navigation */
.navbar {
    padding-top: 1rem;
    padding-bottom: 1rem;
}

.navbar-brand img {
    max-height: 60px; /* Adjusted logo height */
}

.navbar-nav .nav-link {
    color: var(--text-color);
    margin-left: 1rem;
    margin-right: 1rem;
    transition: color 0.3s ease;
}

.navbar-nav .nav-link:hover,
.navbar-nav .nav-link.active {
    color: var(--primary-teal);
}

.navbar-nav .btn-primary {
    background-color: var(--primary-teal);
    border-color: var(--primary-teal);
    color: white;
    transition: background-color 0.3s ease, border-color 0.3s ease;
}

.navbar-nav .btn-primary:hover {
    background-color: var(--darker-teal);
    border-color: var(--darker-teal);
}

/* Key Highlights Section */
.bg-light {
    background-color: var(--light-background) !important; /* Ensure light background from palette */
}

.service-card {
    transition: transform 0.3s;
    border: none;
    border-radius: 0.75rem; /* Rounded corners for cards */
    box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.08); /* Softer shadow */
    padding: 1.5rem; /* Padding inside cards */
    background-color: #fff; /* White card background */
}

.service-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.12); /* Enhanced shadow on hover */
}

.service-card i {
    color: var(--primary-teal); /* Teal icons */
}

.service-card h3 {
    font-weight: 600;
    margin-bottom: 0.75rem;
}

.service-card p {
    color: var(--text-color);
    opacity: 0.8;
}

.service-card .btn-outline-primary {
    color: var(--primary-teal);
    border-color: var(--primary-teal);
    transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
}

.service-card .btn-outline-primary:hover {
    background-color: var(--secondary-light-teal); /* Light teal hover */
    border-color: var(--primary-teal);
    color: var(--primary-teal);
}

/* Featured Services Section */
.py-5 {
    padding-top: 3rem !important; /* Increased padding for sections */
    padding-bottom: 3rem !important;
}

.service-card img {
    border-top-left-radius: 0.75rem;
    border-top-right-radius: 0.75rem;
}

.service-card .card-body {
    padding: 1.5rem;
}

.service-card .card-body h3 {
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.service-card .card-body p {
    color: var(--text-color);
    opacity: 0.8;
}

.service-card .btn-primary {
    background-color: var(--primary-teal);
    border-color: var(--primary-teal);
    transition: background-color 0.3s ease, border-color 0.3s ease;
}

.service-card .btn-primary:hover {
    background-color: var(--darker-teal);
    border-color: var(--darker-teal);
}

/* Testimonial Section */
.testimonial-section {
    background-color: var(--light-background);
    padding-top: 4rem; /* Increased padding for testimonial section */
    padding-bottom: 4rem;
}

.testimonial-section h2 {
    margin-bottom: 3rem;
}

.testimonial-section .card {
    border: none;
    border-radius: 0.75rem;
    box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.05);
    background-color: #fff;
}

.testimonial-section .card-body {
    padding: 1.5rem;
}

.testimonial-section .card-text {
    margin-bottom: 1rem;
    font-style: italic;
    color: var(--text-color);
    opacity: 0.9;
}

.testimonial-section .text-muted {
    font-style: normal;
    opacity: 1;
}

/* Facilities Section */
.facilities-section img {
    border-radius: 0.75rem;
    box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
}

.facilities-section h3 {
    margin-top: 1rem;
    margin-bottom: 0.5rem;
    font-weight: 600;
}

.facilities-section p {
    color: var(--text-color);
    opacity: 0.8;
}

/* Blog Section */
.blog-card {
    border: none;
    border-radius: 0.75rem;
    box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.08);
    background-color: #fff;
}

.blog-card img {
    border-top-left-radius: 0.75rem;
    border-top-right-radius: 0.75rem;
}

.blog-card .card-body {
    padding: 1.5rem;
}

.blog-card h3 {
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.blog-card p {
    color: var(--text-color);
    opacity: 0.8;
}

.blog-card .btn-outline-primary {
    color: var(--primary-teal);
    border-color: var(--primary-teal);
    transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
}

.blog-card .btn-outline-primary:hover {
    background-color: var(--secondary-light-teal); /* Light teal hover */
    border-color: var(--primary-teal);
    color: var(--primary-teal);
}


/* Footer */
.footer {
    background-color: var(--primary-teal); /* Teal footer */
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

.btn-primary.rounded-circle {
    background-color: var(--primary-teal);
    border-color: var(--primary-teal);
    transition: background-color 0.3s ease, border-color 0.3s ease;
}

.btn-primary.rounded-circle:hover {
    background-color: var(--darker-teal);
    border-color: var(--darker-teal);
}

.btn-primary.rounded-circle i {
    font-size: 1.5rem;
}

/* Modal */
.modal-header {
    border-bottom: 1px solid var(--border-color);
}
.modal-footer {
    border-top: 1px solid var(--border-color);
}
</style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="index.php"> <!-- Link homepage to index.php -->
                <img src="assets/images/Logo.png" alt="CareCompass Logo" height="50">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php#services">Services</a></li> <!-- Section link -->
                    <li class="nav-item"><a class="nav-link" href="doctors.php">Doctors</a></li>   <!-- Changed to doctors.php -->
                    <li class="nav-item"><a class="nav-link" href="#facilities">Facilities</a></li> <!-- Section link -->
                    <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>   <!-- Section link -->
                    <li class="nav-item"><a class="btn btn-primary btn-sm ms-2" href="login.php" style="width: 150px;">Login</a></li>  <!-- Added inline style here -->
                </ul>
            </div>
        </div>
    </nav>

<!-- Hero Section -->

<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto"> <!-- Center content in hero -->
                <h1 class="display-4 fw-bold mb-4">Your Health, Our Priority</h1>
                <p class="lead mb-4">Experience world-class healthcare with cutting-edge technology and compassionate care.</p>
                <div class="d-flex justify-content-center gap-3"> <!-- Center buttons -->
                    <a href="book_appointment.php" class="btn btn-light btn-lg">Book Appointment</a>
                </div>
                <div class="mt-4">
                    <span class="emergency-contact">
                        <i class="bi bi-telephone-fill"></i> Emergency: 1-800-CARE (2273)
                    </span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Key Highlights -->

<section class="py-5 bg-light" id="services"> <!-- Added id for section linking -->
    <div class="container">
        <div class="row g-4 justify-content-center"> <!-- Center cards in key highlights -->
            <div class="col-md-4">
                <div class="card h-100 service-card text-center">
                    <div class="card-body">
                        <i class="bi bi-person-plus display-4 mb-3"></i>
                        <h3>Find a Doctor</h3>
                        <p>Search our network of experienced healthcare professionals.</p>
                        <a href="doctor_search.php" class="btn btn-outline-primary">Search Now</a> <!-- Linked to doctor search page -->
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 service-card text-center">
                    <div class="card-body">
                        <i class="bi bi-camera-video display-4 mb-3"></i>
                        <h3>Teleconsultation</h3>
                        <p>Connect with doctors from the comfort of your home.</p>
                        <a href="book_appointment.php?service=teleconsultation" class="btn btn-outline-primary">Book Online</a> <!-- Example: Pass service type -->
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 service-card text-center">
                    <div class="card-body">
                        <i class="bi bi-file-medical display-4 mb-3"></i>
                        <h3>Patient Portal</h3>
                        <p>Access your medical records and test results online.</p>
                        <a href="login.php" class="btn btn-outline-primary">Login Now</a> <!-- Linked to login page -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Services -->

<section class="py-5" id="doctors"> <!-- Added id for section linking -->
    <div class="container">
        <h2 class="text-center mb-5">Our Specialized Departments</h2>
        <div class="row g-4 justify-content-center"> <!-- Center featured services cards -->
            <div class="col-md-4">
                <div class="card service-card">
                    <img src="assets/images/Cardiology Department.jpg" class="card-img-top" alt="Cardiology Department">
                    <div class="card-body">
                        <h3>Cardiology</h3>
                        <p>State-of-the-art cardiac care with experienced specialists.</p>
                        <a href="#cardiology-details" class="btn btn-primary">Learn More</a> <!-- Section link within homepage or dedicated page -->
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card service-card">
                    <img src="assets/images/Oncology Department.jpg" class="card-img-top" alt="Oncology Department">
                    <div class="card-body">
                        <h3>Oncology</h3>
                        <p>Comprehensive cancer care and treatment programs.</p>
                        <a href="#oncology-details" class="btn btn-primary">Learn More</a> <!-- Section link within homepage or dedicated page -->
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card service-card">
                    <img src="assets/images/Pediatric Department.png" class="card-img-top" alt="Pediatrics Department">
                    <div class="card-body">
                        <h3>Pediatrics</h3>
                        <p>Specialized care for children of all ages.</p>
                        <a href="#pediatrics-details" class="btn btn-primary">Learn More</a> <!-- Section link within homepage or dedicated page -->
                    </div>
                    <div class="mt-3 text-center"> <!-- Added button container and centering -->
                    </div>
                </div>
            </div>
            <a href="services.php" class="btn btn-outline-primary" style="width: 200px;">See More Services</a>
        </div>
    </div>
</section>

<!-- Patient Testimonials -->

<section class="testimonial-section py-5">
    <div class="container">
        <h2 class="text-center mb-5">Patient Testimonials</h2>
        <div class="row g-4 justify-content-center"> <!-- Center testimonial cards -->
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-fill text-warning"></i>
                        </div>
                        <p class="card-text">"Exceptional care and attention from the entire staff. The facilities are world-class."</p>
                        <p class="card-text"><small class="text-muted">- John D.</small></p>
                    </div>
                </div>
            </div>
            <!-- Add more testimonials as needed -->
        </div>
    </div>
</section>

<!-- Facilities Showcase -->

<section class="facilities-section py-5" id="facilities"> <!-- Added id for section linking -->
    <div class="container">
        <h2 class="text-center mb-5">Our Facilities & Doctors</h2>
        <div class="row g-4 justify-content-center"> <!-- Center facilities showcase -->
            <div class="col-md-6">
                <img src="assets/images/Advanced Medical Equipment.jpg" alt="Advanced Medical Equipment" class="img-fluid mb-3">
                <h3>State-of-the-art Equipment</h3>
                <p>Latest medical technology for accurate diagnosis and treatment.</p>
            </div>
            <div class="col-md-6">
                <img src="assets/images/Experienced Doctors.jpg" alt="Experienced Doctors" class="img-fluid mb-3">
                <h3>Meet Our Experienced Doctors</h3>
                <p>Our team of highly skilled and compassionate doctors are dedicated to providing the best care.</p>
            </div>
        </div>
    </div>
</section>

<!-- Blog Section -->

<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-5">Health Tips & News</h2>
        <div class="row g-4 justify-content-center"> <!-- Center blog cards -->
            <div class="col-md-4">
                <div class="card blog-card h-100">
                    <img src="assets/images/Health Tips.jpg" class="card-img-top" alt="Health Tips">
                    <div class="card-body">
                        <h3>Healthy Living Tips</h3>
                        <p>Simple ways to maintain a healthy lifestyle.</p>
                        <a href="#" class="btn btn-outline-primary">Read More</a> <!-- Placeholder link - Blog page needed -->
                    </div>
                </div>
            </div>
            <!-- Add more blog posts -->
        </div>
    </div>
</section>

<!-- Footer -->

<footer class="footer py-5" id="contact"> <!-- Added id for section linking -->
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
                    <li><a href="about_us.php" class="text-white">About Us</a></li> <!-- Section link - About Us section needed in homepage or separate page -->
                    <li><a href="#services" class="text-white">Services</a></li>
                    <li><a href="doctors.php" class="text-white">Doctors</a></li>
                    <li><a href="#contact" class="text-white">Contact</a></li>
                    <li><a class="btn btn-primary btn-sm ms-2" href="login.php" style="width: 150px;">Login</a></li>
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