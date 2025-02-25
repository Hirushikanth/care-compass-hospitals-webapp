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

// Fetch all feedback from the database
$feedbackItems = $db->getAllFeedback();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Patient Feedback - Care Compass Connect Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include('../includes/header.php'); ?>

    <div class="container py-5">
        <h2 class="mb-4">Patient Feedback</h2>

        <div class="card shadow-sm">
            <div class="card-body">
                <?php if ($feedbackItems): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Submitted By</th>
                                    <th>Feedback Message</th>
                                    <th>Rating</th>
                                    <th>Submitted On</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($feedbackItems as $feedback): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($feedback['id']) ?></td>
                                        <td><?= htmlspecialchars($feedback['submitted_by_username'] ?: $feedback['fullname'] ?: 'Guest') ?></td> <!-- Display username if logged-in user, otherwise fullname, otherwise "Guest" -->
                                        <td><?= htmlspecialchars(substr($feedback['feedback_text'], 0, 100)) ?>...</td> <!-- Display excerpt -->
                                        <td><?= htmlspecialchars($feedback['rating'] ? $feedback['rating'] . ' stars' : 'No Rating') ?></td> <!-- Display rating if available -->
                                        <td><?= date('F j, Y, g:i a', strtotime($feedback['created_at'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No feedback submissions yet.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="mt-4">
            <a href="dashboard.php" class="btn btn-secondary"><i class="bi bi-arrow-left me-2"></i> Back to Dashboard</a>
        </div>
    </div>

    <?php include('../includes/footer.php'); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>