<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Services - CareCompass Hospital</title>
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
        }

        .services-header {
            background-color: var(--primary-teal);
            color: white;
            padding: 2rem 0;
            margin-bottom: 3rem;
            text-align: center;
        }

        .services-header h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0;
        }

        .service-card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.08);
            background-color: #fff;
            transition: transform 0.3s;
        }

        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.12);
        }

        .service-card img {
            border-top-left-radius: 0.75rem;
            border-top-right-radius: 0.75rem;
            height: 250px; /* Fixed height for images */
            object-fit: cover; /* Cover container, cropping if needed */
        }

        .service-card .card-body {
            padding: 1.5rem;
        }

        .service-card h3 {
            font-weight: 600;
            margin-bottom: 0.75rem;
            color: var(--primary-teal); /* Teal service titles */
        }

        .service-card p {
            color: var(--text-color);
            opacity: 0.8;
        }

        .service-card .btn-primary {
            background-color: var(--primary-teal);
            border-color: var(--primary-teal);
        }

        .service-card .btn-primary:hover {
            background-color: var(--darker-teal);
            border-color: var(--darker-teal);
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
                    <li class="nav-item"><a class="btn btn-primary ms-2" href="login.php">Login</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <header class="services-header">
        <div class="container">
            <h2>Our Medical Services</h2>
            <p class="lead">Comprehensive healthcare solutions tailored to your needs.</p>
        </div>
    </header>

    <main class="container py-5">
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <div class="col">
                <div class="card service-card h-100">
                    <img src="assets/images/Cardiology Department.jpg" class="card-img-top" alt="Cardiology Services">
                    <div class="card-body">
                        <h3 class="card-title">Cardiology</h3>
                        <p class="card-text">Expert cardiac care, from routine check-ups to advanced interventions. Our cardiology department is equipped to handle all aspects of heart health.</p>
                        <a href="#" class="btn btn-primary">Learn More</a>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card service-card h-100">
                    <img src="assets/images/Oncology Department.jpg" class="card-img-top" alt="Oncology Services">
                    <div class="card-body">
                        <h3 class="card-title">Oncology</h3>
                        <p class="card-text">Compassionate and cutting-edge cancer care. Our oncology team is dedicated to providing personalized treatment plans.</p>
                        <a href="#" class="btn btn-primary">Learn More</a>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card service-card h-100">
                    <img src="assets/images/Pediatric Department.png" class="card-img-top" alt="Pediatrics Services">
                    <div class="card-body">
                        <h3 class="card-title">Pediatrics</h3>
                        <p class="card-text">Comprehensive healthcare for infants, children, and adolescents. We provide a nurturing and child-friendly environment.</p>
                        <a href="#" class="btn btn-primary">Learn More</a>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card service-card h-100">
                    <img src="assets/images/Neurology Department.jpg" class="card-img-top" alt="Neurology Services">
                    <div class="card-body">
                        <h3 class="card-title">Neurology</h3>
                        <p class="card-text">Advanced diagnostics and treatment for neurological disorders. Our neurologists are leaders in their field.</p>
                        <a href="#" class="btn btn-primary">Learn More</a>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card service-card h-100">
                    <img src="assets/images/Orthopedics Department.jpg" class="card-img-top" alt="Orthopedics Services">
                    <div class="card-body">
                        <h3 class="card-title">Orthopedics</h3>
                        <p class="card-text">From sports injuries to joint replacements, our orthopedic services cover a wide range of musculoskeletal conditions.</p>
                        <a href="#" class="btn btn-primary">Learn More</a>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card service-card h-100">
                    <img src="assets/images/Emergency Room.jpg" class="card-img-top" alt="Emergency Services">
                    <div class="card-body">
                        <h3 class="card-title">Emergency Services</h3>
                        <p class="card-text">24/7 emergency care with rapid response and expert medical teams. We are always ready to provide immediate care.</p>
                        <a href="#" class="btn btn-primary">Learn More</a>
                    </div>
                </div>
            </div>
             <div class="col">
                <div class="card service-card h-100">
                    <img src="assets/images/Radiology Department.jpg" class="card-img-top" alt="Radiology Services">
                    <div class="card-body">
                        <h3 class="card-title">Radiology</h3>
                        <p class="card-text">Cutting-edge imaging technology for accurate and timely diagnoses. Our radiology department supports all clinical specialties.</p>
                        <a href="#" class="btn btn-primary">Learn More</a>
                    </div>
                </div>
            </div>
             <div class="col">
                <div class="card service-card h-100">
                    <img src="assets/images/Laboratory Services.jpg" class="card-img-top" alt="Laboratory Services">
                    <div class="card-body">
                        <h3 class="card-title">Laboratory Services</h3>
                        <p class="card-text">Comprehensive lab services ensuring quick and reliable diagnostic results. From routine tests to specialized analyses.</p>
                        <a href="#" class="btn btn-primary">Learn More</a>
                    </div>
                </div>
            </div>
             <div class="col">
                <div class="card service-card h-100">
                    <img src="assets/images/Pharmacy Services.jpg" class="card-img-top" alt="Pharmacy Services">
                    <div class="card-body">
                        <h3 class="card-title">Pharmacy Services</h3>
                        <p class="card-text">Our pharmacy provides a wide range of medications and pharmaceutical services, ensuring patient safety and convenience.</p>
                        <a href="#" class="btn btn-primary">See More</a> <!-- "See More" Button as requested -->
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="footer py-5">
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