<?php
// Include necessary files
include('includes/config.php');
include('includes/db.php');
include('includes/functions.php');

// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in (you might allow non-logged-in users to submit queries too)
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$db = new Database();

// Generate CSRF token BEFORE displaying the form
$csrf_token = generate_csrf_token();

// Handle form submission
if (isset($_POST['submit_query'])) {
    // Verify CSRF token at the VERY BEGINNING of form processing
    if (!verify_csrf_token()) {
        // CSRF token verification failed!  Reject the request.
        die("CSRF token validation failed."); // Or display a user-friendly error message and exit.
    }

    $userId = $_SESSION['user_id']; // Or get user ID from form if allowing non-logged-in users
    $subject = sanitize_input($_POST['subject']);
    $message = sanitize_input($_POST['message']);

    // Server-side validation (in addition to client-side validation)
    $errors = [];
    if (empty($subject)) {
        $errors[] = "Subject is required.";
    }
    if (empty($message)) {
        $errors[] = "Message is required.";
    }
    // Add more server-side validation as needed

    // If no errors, proceed with submitting the query
    if (empty($errors)) {
        $success = $db->createQuery($userId, $subject, $message);

        if ($success) {
            echo '<div class="alert alert-success">Query submitted successfully!</div>';
        } else {
            echo '<div class="alert alert-danger">Error submitting query.</div>';
        }
    } else {
        // Display errors
        echo '<div class="alert alert-danger"><ul>';
        foreach ($errors as $error) {
            echo "<li>$error</li>";
        }
        echo '</ul></div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Query - Care Compass Connect</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <!-- Add your custom styles if needed -->
</head>
<body>
    <div class="container">
        <h2>Submit a Query</h2>

        <form method="post" onsubmit="return validateForm()">
            <!-- ADD THIS HIDDEN INPUT FIELD for CSRF token -->
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

            <div class="mb-3">
                <label for="subject" class="form-label">Subject:</label>
                <input type="text" class="form-control" id="subject" name="subject" required>
                <div id="subject-error" class="text-danger"></div>
            </div>
            <div class="mb-3">
                <label for="message" class="form-label">Message:</label>
                <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                <div id="message-error" class="text-danger"></div>
            </div>
            <button type="submit" name="submit_query" class="btn btn-primary">Submit Query</button>
        </form>

        <a href="<?= ($_SESSION['user_role'] == 'patient') ? 'patient/dashboard.php' : 'staff/dashboard.php' ?>" class="btn btn-secondary mt-3">Back to Dashboard</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>
    <script>
        function validateForm() {
            let isValid = true;
            const subject = document.getElementById('subject').value;
            const message = document.getElementById('message').value;

            // Reset error messages
            document.getElementById('subject-error').innerText = '';
            document.getElementById('message-error').innerText = '';

            // Subject validation
            if (subject.trim() === '') {
                document.getElementById('subject-error').innerText = 'Subject is required.';
                isValid = false;
            }

            // Message validation
            if (message.trim() === '') {
                document.getElementById('message-error').innerText = 'Message is required.';
                isValid = false;
            }

            return isValid;
        }
    </script>
</body>
</html>