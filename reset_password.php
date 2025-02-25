<?php
include('includes/config.php');
include('includes/db.php');
include('includes/functions.php');

$db = new Database();
$errors = [];
$success = false;

// Handle form submission
if (isset($_POST['reset_password'])) {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $token = $_POST['token'];

    // Basic validation
    if (empty($password) || empty($confirm_password)) {
        $errors[] = "Both password fields are required.";
    } elseif ($password != $confirm_password) {
        $errors[] = "Passwords do not match.";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    }

    // If no errors, proceed
    if (empty($errors)) {
        // Find user by token and check if token is still valid
        $user = $db->getUserByPasswordResetToken($token);

        if ($user && strtotime($user['password_reset_expires']) > time()) {
            // Hash the new password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Update user's password and clear the token
            $updateSuccess = $db->updateUserPassword($user['id'], $hashed_password);

            if ($updateSuccess) {
                // Clear the token (optional, for security)
                $db->clearUserPasswordResetToken($user['id']);
                $success = true;
            } else {
                $errors[] = "Error updating password.";
            }
        } else {
            $errors[] = "Invalid or expired password reset token.";
        }
    }
} else {
    // Check if token is provided in the URL
    $token = isset($_GET['token']) ? $_GET['token'] : null;

    if (!$token) {
        $errors[] = "Password reset token not provided.";
    } else {
        // Check if token exists and is still valid
        $user = $db->getUserByPasswordResetToken($token);
        if (!$user || strtotime($user['password_reset_expires']) < time()) {
            $errors[] = "Invalid or expired password reset token.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Care Compass Connect</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
    <!-- Add your custom styles if needed -->
</head>
<body>
    <div class="container">
        <h2>Reset Password</h2>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">
                Your password has been successfully reset. You can now <a href="login.php">login</a> with your new password.
            </div>
        <?php else: ?>
            <form method="post">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                <div class="mb-3">
                    <label for="password" class="form-label">New Password:</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm New Password:</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                </div>
                <button type="submit" name="reset_password" class="btn btn-primary">Reset Password</button>
            </form>
        <?php endif; ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>
</body>
</html>