<nav class="navbar navbar-expand-lg navbar-dark navbar-primary-teal shadow-sm">  <!-- Using navbar-primary-teal class -->
    <div class="container">
        <a class="navbar-brand" href="<?= SITE_URL ?>/<?= ($_SESSION['user_role'] == 'patient') ? 'patient/dashboard.php' : (($_SESSION['user_role'] == 'staff') ? 'staff/dashboard.php' : 'admin/dashboard.php') ?>">
            <img src="<?= SITE_URL ?>/assets/images/Logo.png" alt="CareCompass Logo" height="50"> <!-- White Logo -->
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?= ($_SESSION['user_role'] == 'patient') ? 'dashboard.php' : (($_SESSION['user_role'] == 'staff') ? 'dashboard.php' : 'dashboard.php') ?>">Dashboard</a>
                </li>
                <?php if ($_SESSION['user_role'] == 'patient'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= SITE_URL ?>/patient/update_profile.php">Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= SITE_URL ?>/book_appointment.php">Book Appointment</a>
                    </li>
                <?php elseif ($_SESSION['user_role'] == 'staff'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= SITE_URL ?>/staff/appointment_search.php">Appointments</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= SITE_URL ?>/staff/patient_search.php">Patients</a>
                    </li>
                <?php elseif ($_SESSION['user_role'] == 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= SITE_URL ?>/admin/manage_users.php">Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= SITE_URL ?>/admin/manage_doctors.php">Doctors</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= SITE_URL ?>/admin/manage_branches.php">Branches</a>
                    </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?= SITE_URL ?>/logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<style>
    .navbar-primary-teal {
        background-color: var(--primary-teal); /* Using your primary teal color variable */
    }
    .navbar-dark .navbar-brand,
    .navbar-dark .navbar-nav .nav-link {
        color: white; /* White text for navbar links and brand */
    }
    .navbar-dark .navbar-nav .nav-link:hover,
    .navbar-dark .navbar-nav .nav-link:focus {
        color: rgba(255, 255, 255, 0.75); /* Slightly lighter white on hover/focus */
    }
    .navbar-brand img {
        filter: brightness(0) invert(1); /* Alternative way to ensure logo is white if Logo_White.png isn't available */
    }
</style>