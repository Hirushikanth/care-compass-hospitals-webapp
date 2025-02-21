<?php
// Include necessary files
include('includes/config.php');
include('includes/db.php');
include('includes/functions.php');

$db = new Database();

// Get blog post ID from URL parameter
$postId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($postId <= 0) {
    // Invalid post ID, redirect to blog listing or display error
    header("Location: blog.php"); // Redirect to blog listing
    exit;
}

// Fetch blog post by ID (you'll need to implement this in your Database class)
$blogPost = $db->getBlogPostById($postId); // You need to add this method to db.php

if (!$blogPost) {
    // Post not found, redirect to blog listing or display error
    header("Location: blog.php"); // Redirect to blog listing
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($blogPost['title']) ?> - Care Compass Blog</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css"> <!-- Link to your main stylesheet -->
</head>
<body>

    <?php include('includes/header.php'); // Assuming you have a header include file ?>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <article>
                    <h2><?= htmlspecialchars($blogPost['title']) ?></h2>
                    <p class="text-muted">Published on: <?= date('F j, Y', strtotime($blogPost['created_at'])) ?> by <?= htmlspecialchars($blogPost['author'] ?: 'Care Compass Team') ?></p> <!-- Display author if available -->
                    <hr>
                    <p><?= nl2br(htmlspecialchars($blogPost['content'])) ?></p> <!-- nl2br for line breaks in content -->
                </article>

                <div class="mt-4">
                    <a href="blog.php" class="btn btn-secondary"><i class="bi bi-arrow-left me-2"></i> Back to Blog</a>
                </div>
            </div>
        </div>
    </div>

    <?php include('includes/footer.php'); // Assuming you have a footer include file ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>