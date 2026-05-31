<?php
session_start();
include 'config.php';

$search = "";

if (isset($_GET["search"])) {
    $search = trim($_GET["search"]);
    $stmt = mysqli_prepare($conn, "
        SELECT m.*, c.category_name,
        IFNULL(AVG(r.rating_value), 0) AS avg_rating
        FROM dbProj_movies m
        JOIN dbProj_categories c ON m.category_id = c.category_id
        LEFT JOIN dbProj_ratings r ON m.movie_id = r.movie_id
        WHERE m.status = 'published'
        AND m.title LIKE ?
        GROUP BY m.movie_id
        ORDER BY m.created_at DESC
    ");
    $searchParam = "%" . $search . "%";
    mysqli_stmt_bind_param($stmt, "s", $searchParam);
    mysqli_stmt_execute($stmt);
    $movies = mysqli_stmt_get_result($stmt);
} else {
    $query = "
        SELECT m.*, c.category_name,
        IFNULL(AVG(r.rating_value), 0) AS avg_rating
        FROM dbProj_movies m
        JOIN dbProj_categories c ON m.category_id = c.category_id
        LEFT JOIN dbProj_ratings r ON m.movie_id = r.movie_id
        WHERE m.status = 'published'
        GROUP BY m.movie_id
        ORDER BY m.created_at DESC
    ";
    $movies = mysqli_query($conn, $query);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>RateFlix</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<header>
    <h1>RateFlix</h1>
    <nav>
    <a href="index.php">Home</a>

    <?php if (isset($_SESSION["user_id"])): ?>
        <a href="dashboard.php">Dashboard</a>
        <a href="logout.php">Logout</a>
    <?php else: ?>
        <a href="signup.php">Sign Up</a>
        <a href="login.php">Login</a>
    <?php endif; ?>
</nav>
</header>

<div class="container">
    <h2>Movie Rating & Review System</h2>

    <form method="GET" class="search-form">
        <input type="text" name="search" placeholder="Search movies by title..." value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit">Search</button>
    </form>

    <div class="movie-grid">
        <?php if (mysqli_num_rows($movies) > 0): ?>
            <?php while ($movie = mysqli_fetch_assoc($movies)): ?>
                <div class="movie-card">
                    <div class="poster">
                        <?php if (!empty($movie["poster_image"])): ?>
                            <img src="uploads/<?php echo htmlspecialchars($movie["poster_image"]); ?>" alt="Movie Poster">
                        <?php else: ?>
                            <div class="placeholder">No Image</div>
                        <?php endif; ?>
                    </div>

                    <h3><?php echo htmlspecialchars($movie["title"]); ?></h3>
                    <p class="category"><?php echo htmlspecialchars($movie["category_name"]); ?></p>
                    <p><?php echo htmlspecialchars($movie["short_description"]); ?></p>
                    <p class="rating">
    Rating: <?php echo rtrim(rtrim(number_format($movie["avg_rating"], 1), '0'), '.'); ?>/5
</p>

                    <a class="btn" href="movie.php?id=<?php echo $movie["movie_id"]; ?>">View Details</a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No movies found.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>