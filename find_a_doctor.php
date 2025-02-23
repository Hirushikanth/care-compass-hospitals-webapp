<?php
// Include necessary files
include('includes/config.php');
include('includes/db.php');
include('includes/functions.php');

$db = new Database();

// Handle search query
$searchQuery = isset($_GET['search_query']) ? sanitize_input($_GET['search_query']) : '';
$doctors = [];

if ($searchQuery) {
    // Search doctors by name or specialty
    $doctors = $db->searchDoctors($searchQuery); // Reusing your existing searchDoctors function
} else {
    // Optionally, fetch all doctors initially for the page to load with all doctors listed
    // $doctors = $db->getAllDoctors();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find a Doctor - Care Compass Connect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css"> <!-- Link to your main stylesheet -->
</head>
<body>
    <?php include('includes/header.php'); ?>

    <div class="container py-5">
        <h2 class="text-center mb-4">Find a Doctor</h2>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="get">
                    <div class="input-group">
                        <input type="text" class="form-control" name="search_query" placeholder="Search by doctor name or specialty" value="<?= htmlspecialchars($searchQuery) ?>">
                        <button class="btn btn-primary" type="submit">Search</button>
                    </div>
                </form>
            </div>
        </div>

        <?php if ($searchQuery): ?>
            <h3>Search Results:</h3>
        <?php endif; ?>

        <?php if ($doctors): ?>
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <?php foreach ($doctors as $doctor): ?>
                    <div class="col">
                        <div class="card h-100 doctor-card">
                            <img src="assets/images/DoctorPlaceholder.jpg" class="card-img-top" alt="Dr. <?= htmlspecialchars($doctor['fullname']) ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($doctor['fullname']) ?></h5>
                                <p class="card-text"><small class="text-muted"><?= htmlspecialchars($doctor['specialty']) ?></small></p>
                                <a href="doctor_profile.php?id=<?= $doctor['id'] ?>" class="btn btn-outline-primary">View Profile</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <?php if ($searchQuery): ?>
                <p class="text-muted">No doctors found matching your search criteria.</p>
            <?php else: ?>
                <p class="text-muted">Please use the search form above to find doctors.</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <?php include('includes/footer.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>