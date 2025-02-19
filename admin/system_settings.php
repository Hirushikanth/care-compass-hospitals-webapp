<?php
// Include necessary files
include('../includes/config.php');
include('../includes/db.php');
include('../includes/functions.php');

// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

$db = new Database();

// Handle form submission
if (isset($_POST['update_settings'])) {
    $hospitalName = sanitize_input($_POST['hospital_name']);
    // ... (Update other settings as needed)

    $success = $db->updateSetting('hospital_name', $hospitalName);
    // ... (Update other settings as needed)

    if ($success) {
        echo '<div class="alert alert-success">Settings updated successfully!</div>';
    } else {
        echo '<div class="alert alert-danger">Error updating settings.</div>';
    }
}

// Fetch current settings
$hospitalName = $db->getSetting('hospital_name');
// ... (Fetch other settings as needed)
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Settings - Care Compass Connect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #f0f8ff; /* Light background from the palette */
            font-family: 'Nunito', sans-serif;
            color: #343a40;
        }
        .dashboard-header {
            background-color: #046A7A; /* Dark teal from the palette */
            color: white;
            padding: 2.5rem 0;
            margin-bottom: 3rem;
        }
        .dashboard-header h2 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0;
        }
        .dashboard-card {
            background-color: #fff;
            border-radius: 0.75rem;
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            overflow: hidden;
        }
        .dashboard-card:hover {
            transform: scale(1.01);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.07);
        }
        .dashboard-card-header {
            background-color: #046A7A;
            color: white;
            padding: 1.25rem;
            border-top-left-radius: 0.75rem;
            border-top-right-radius: 0.75rem;
        }
        .dashboard-card-header h3 {
            font-size: 1.5rem;
            margin-bottom: 0;
            font-weight: 600;
        }
        .card-body {
            padding: 1.5rem;
        }
        .form-label {
            font-size: 0.95rem;
            color: #343a40;
            margin-bottom: 0.3rem;
        }
        .form-control {
            border: 1px solid #ced4da;
            border-radius: 0.3rem;
            padding: 0.6rem 0.75rem;
            font-size: 1rem;
        }
        .form-control:focus {
            border-color: #046A7A; /* Focus color */
            box-shadow: 0 0 0 0.2rem rgba(4, 106, 122, 0.25); /* Focus shadow */
        }
        .btn-primary {
            background-color: #046A7A;
            border-color: #046A7A;
            transition: background-color 0.2s ease-in-out, border-color 0.2s ease-in-out;
        }
        .btn-primary:hover, .btn-primary:focus {
            background-color: #034e5a;
            border-color: #034e5a;
            box-shadow: 0 0 0 0.2rem rgba(4, 106, 122, 0.5);
        }
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
            transition: background-color 0.2s ease-in-out, border-color 0.2s ease-in-out;
        }
        .btn-secondary:hover, .btn-secondary:focus {
            background-color: #545b62;
            border-color: #4e555b;
            box-shadow: 0 0 0 0.2rem rgba(108, 117, 125, 0.5);
        }
    </style>
</head>
<body>
    <header class="dashboard-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <h2><i class="bi bi-gear me-2"></i>System Settings</h2>
                </div>
            </div>
        </div>
    </header>

    <main class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <h3 class="mb-0">General Settings</h3>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_POST['update_settings'])): ?>
                            <?php if ($success): ?>
                                <div class="alert alert-success" role="alert">
                                    Settings updated successfully!
                                </div>
                            <?php else: ?>
                                <div class="alert alert-danger" role="alert">
                                    Error updating settings.
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                        <form method="post">
                            <div class="mb-3">
                                <label for="hospital_name" class="form-label">Hospital Name:</label>
                                <input type="text" class="form-control" id="hospital_name" name="hospital_name" value="<?= htmlspecialchars($hospitalName) ?>" required>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" name="update_settings" class="btn btn-primary">Update Settings</button>
                                <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>