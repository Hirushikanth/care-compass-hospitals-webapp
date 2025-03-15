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
    <link rel="stylesheet" href="assets/css/style.css"> <!-- Link to external stylesheet -->
</head>
<body>
    <?php include('includes/header.php'); ?> <!-- Include header -->

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
                            <a href="find_a_doctor.php" class="btn btn-outline-primary">Search Now</a> <!-- Linked to doctor search page -->
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
    <section class="py-5" id="departments"> <!-- Added id for section linking -->
        <div class="container">
            <h2 class="text-center mb-5">Our Specialized Departments</h2>
            <div class="row g-4 justify-content-center"> <!-- Center featured services cards -->
                <div class="col-md-4">
                    <div class="card service-card">
                        <img src="assets/images/Cardiology Department.jpg" class="card-img-top" alt="Cardiology Department">
                        <div class="card-body">
                            <h3>Cardiology</h3>
                            <p>State-of-the-art cardiac care with experienced specialists.</p>
                            <a href="Services/cardiology.php" class="btn btn-primary">Learn More</a> <!-- Section link within homepage or dedicated page -->
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card service-card">
                        <img src="assets/images/Oncology Department.jpg" class="card-img-top" alt="Oncology Department">
                        <div class="card-body">
                            <h3>Oncology</h3>
                            <p>Comprehensive cancer care and treatment programs.</p>
                            <a href="Services/oncology.php" class="btn btn-primary">Learn More</a> <!-- Section link within homepage or dedicated page -->
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card service-card">
                        <img src="assets/images/Pediatric Department.png" class="card-img-top" alt="Pediatrics Department">
                        <div class="card-body">
                            <h3>Pediatrics</h3>
                            <p>Specialized care for children of all ages.</p>
                            <a href="Services/pediatrics.php" class="btn btn-primary">Learn More</a> <!-- Section link within homepage or dedicated page -->
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

                <!-- Location Cards -->
                <div class="col-md-4">
                    <div class="card facility-card location-card h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-geo-alt-fill display-4 mb-3"></i>
                            <h3>Colombo Branch</h3>
                            <p>Find us in the heart of Colombo for accessible healthcare services.</p>
                            <a href="https://maps.app.goo.gl/HHgvkKU8Bi6LSYrm8" class="btn btn-outline-primary" target="_blank">View on Map</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card facility-card location-card h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-geo-alt-fill display-4 mb-3"></i>
                            <h3>Kandy Branch</h3>
                            <p>Serving the central province from our Kandy branch.</p>
                            <a href="https://maps.app.goo.gl/WjNdGzfQkwfuvNB6A" class="btn btn-outline-primary" target="_blank">View on Map</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card facility-card location-card h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-geo-alt-fill display-4 mb-3"></i>
                            <h3>Kurunegala Branch</h3>
                            <p>Extending our care to the North Western Province in Kurunegala.</p>
                            <a href="https://maps.app.goo.gl/b2HALSMSpPCfoixZA" class="btn btn-outline-primary" target="_blank">View on Map</a>
                        </div>
                    </div>
                </div>
                <!-- End Location Cards -->

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
                            <a href="blog.php" class="btn btn-outline-primary">Read More</a> <!-- Placeholder link - Blog page needed -->
                        </div>
                    </div>
                </div>
                <!-- Add more blog posts -->
            </div>
        </div>
    </section>

    <?php include('includes/footer.php'); ?> <!-- Include footer -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>