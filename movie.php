<?php
include 'config.php';

if (!isset($_GET["id"])) {
    header("Location: index.php");
    exit();
}

$movie_id = intval($_GET["id"]);

$stmt = mysqli_prepare($conn, "
    SELECT m.*, c.category_name, u.username,
    IFNULL(AVG(r.rating_value), 0) AS avg_rating
    FROM dbProj_movies m
    JOIN dbProj_categories c ON m.category_id = c.category_id
    JOIN dbProj_users u ON m.creator_id = u.user_id
    LEFT JOIN dbProj_ratings r ON m.movie_id = r.movie_id
    WHERE m.movie_id = ? AND m.status = 'published'
    GROUP BY m.movie_id
");

mysqli_stmt_bind_param($stmt, "i", $movie_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$movie = mysqli_fetch_assoc($result);

if (!$movie) {
    echo "Movie not found.";
    exit();
}

$comments_stmt = mysqli_prepare($conn, "
    SELECT c.comment_text, c.created_at, u.username
    FROM dbProj_comments c
    JOIN dbProj_users u ON c.user_id = u.user_id
    WHERE c.movie_id = ?
    ORDER BY c.created_at DESC
");
mysqli_stmt_bind_param($comments_stmt, "i", $movie_id);
mysqli_stmt_execute($comments_stmt);
$comments = mysqli_stmt_get_result($comments_stmt);
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($movie["title"]); ?> - RateFlix</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<header>
    <h1>RateFlix</h1>
    <nav>
        <a href="index.php">Home</a>
        <a href="signup.php">Sign Up</a>
        <a href="login.php">Login</a>
    </nav>
</header>

<div class="container">
    <div class="movie-detail">
        <div class="poster large">
            <?php if (!empty($movie["poster_image"])): ?>
                <img src="uploads/<?php echo htmlspecialchars($movie["poster_image"]); ?>" alt="Movie Poster">
            <?php else: ?>
                <div class="placeholder">No Image</div>
            <?php endif; ?>
        </div>

        <div>
            <h2><?php echo htmlspecialchars($movie["title"]); ?></h2>
            <p><strong>Category:</strong> <?php echo htmlspecialchars($movie["category_name"]); ?></p>
            <p><strong>Creator:</strong> <?php echo htmlspecialchars($movie["username"]); ?></p>
            <p><strong>Average Rating:</strong> <?php echo number_format($movie["avg_rating"], 1); ?>/5</p>
            <p><?php echo nl2br(htmlspecialchars($movie["description"])); ?></p>
        </div>
    </div>

    <h3>Comments</h3>

    <?php if (mysqli_num_rows($comments) > 0): ?>
        <?php while ($comment = mysqli_fetch_assoc($comments)): ?>
            <div class="comment">
                <strong><?php echo htmlspecialchars($comment["username"]); ?></strong>
                <p><?php echo htmlspecialchars($comment["comment_text"]); ?></p>
                <small><?php echo $comment["created_at"]; ?></small>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No comments yet.</p>
    <?php endif; ?>

    <p><a class="btn" href="index.php">Back to Movies</a></p>
</div>

</body>
</html>