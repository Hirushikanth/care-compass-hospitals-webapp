<?php
// You might want to include session start or any common dashboard-related PHP logic here if needed
// For now, it's just the header HTML structure
?>
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container-fluid"> <!-- Use container-fluid to span full width -->
        <a class="navbar-brand" href="dashboard.php"> <!-- Link to the specific dashboard -->
            <img src="../assets/images/Logo-sm.png" alt="CareCompass Dashboard Logo" height="40"> <!-- Smaller logo version for dashboard -->
            Dashboard
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#dashboardNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="dashboardNav">
            <ul class="navbar-nav ms-auto">
                <!-- Dashboard Specific Navigation Items - Adjust these as needed for each dashboard -->
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">Home</a> <!-- Link to the main dashboard page -->
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="update_profile.php">Update Profile</a> <!-- Example: Profile link -->
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="view_all_appointments.php">Appointments</a> <!-- Example: Appointments link -->
                </li>
                 <li class="nav-item">
                    <a class="nav-link" href="view_queries.php">Queries</a> <!-- Example: Queries link - for Admin/Staff -->
                </li>
                 <li class="nav-item">
                    <a class="nav-link" href="manage_users.php">Users</a> <!-- Example: Users link - for Admin -->
                </li>
                 <li class="nav-item">
                    <a class="nav-link" href="manage_doctors.php">Doctors</a> <!-- Example: Doctors link - for Admin -->
                </li>
                <li class="nav-item">
                    <a class="btn btn-outline-danger btn-sm ms-2" href="?logout=true">Logout</a> <!-- Logout button -->
                </li>
            </ul>
        </div>
    </div>
</nav>