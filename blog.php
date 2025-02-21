<?php
// Include necessary files
include('includes/config.php');
include('includes/db.php');
include('includes/functions.php');

$db = new Database();

// Fetch all blog posts (you'll need to implement this in your Database class)
$blogPosts = $db->getAllBlogPosts(); // You need to add this method to db.php

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Blog - Care Compass Hospital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <?php include('includes/header.php'); ?>

    <div class="container py-5">
        <h1 class="mb-4">Health Tips & News</h1>

        <?php if ($blogPosts): ?>
            <div class="row row-cols-1 row-cols-md-2 g-4">
                <?php foreach ($blogPosts as $post): ?>
                    <div class="col">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title"><a href="blog_post.php?id=<?= $post['id'] ?>" class="text-decoration-none text-dark"><?= htmlspecialchars($post['title']) ?></a></h5>
                                <p class="card-text"><small class="text-muted">Published on: <?= date('F j, Y', strtotime($post['created_at'])) ?></small></p>
                                <p class="card-text"><?= htmlspecialchars(substr($post['content'], 0, 200)) ?>...</p> <!-- Display excerpt -->
                                <a href="blog_post.php?id=<?= $post['id'] ?>" class="btn btn-primary">Read More</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-muted">No blog posts found yet.</p>
        <?php endif; ?>
    </div>

    <?php include('includes/footer.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>