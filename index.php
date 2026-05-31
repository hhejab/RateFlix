<?php
session_start();
include 'config.php';

$search = $_GET["search"] ?? "";
$creator = $_GET["creator"] ?? "";
$date_from = $_GET["date_from"] ?? "";
$date_to = $_GET["date_to"] ?? "";
$sort = $_GET["sort"] ?? "newest";

$sql = "
SELECT 
    m.movie_id,
    m.title,
    m.short_description,
    m.poster_image,
    m.created_at,
    c.category_name,
    u.username AS creator_name,
    IFNULL(AVG(r.rating_value), 0) AS avg_rating,
    COUNT(r.rating_id) AS rating_count
FROM dbProj_movies m
JOIN dbProj_categories c ON m.category_id = c.category_id
JOIN dbProj_users u ON m.creator_id = u.user_id
LEFT JOIN dbProj_ratings r ON m.movie_id = r.movie_id
WHERE m.status = 'published'
";

$params = [];
$types = "";

if (!empty($search)) {
    $sql .= " AND (m.title LIKE ? OR m.short_description LIKE ?)";
    $searchParam = "%$search%";
    $params[] = $searchParam;
    $params[] = $searchParam;
    $types .= "ss";
}

if (!empty($creator)) {
    $sql .= " AND u.username LIKE ?";
    $params[] = "%$creator%";
    $types .= "s";
}

if (!empty($date_from)) {
    $sql .= " AND DATE(m.created_at) >= ?";
    $params[] = $date_from;
    $types .= "s";
}

if (!empty($date_to)) {
    $sql .= " AND DATE(m.created_at) <= ?";
    $params[] = $date_to;
    $types .= "s";
}

$sql .= "
GROUP BY m.movie_id
";

if ($sort == "popular") {
    $sql .= " ORDER BY avg_rating DESC, rating_count DESC";
} else {
    $sql .= " ORDER BY m.created_at DESC";
}

$stmt = mysqli_prepare($conn, $sql);

if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
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

            <?php if ($_SESSION["role_id"] == 2): ?>
                <a href="creator/add_movie.php">Add Movie</a>
                <a href="creator/my_movies.php">My Movies</a>
            <?php endif; ?>

            <?php if ($_SESSION["role_id"] == 1): ?>
                <a href="admin/index.php">Admin Panel</a>
            <?php endif; ?>

            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="signup.php">Sign Up</a>
            <a href="login.php">Login</a>
        <?php endif; ?>
    </nav>
</header>

<div class="container">

    <h2>Movie Rating & Review System</h2>

    <form method="GET" class="filter-form">
        <input type="text" name="search" placeholder="Search by title..." value="<?php echo htmlspecialchars($search); ?>">
        <input type="text" name="creator" placeholder="Creator name..." value="<?php echo htmlspecialchars($creator); ?>">
        <input type="date" name="date_from" value="<?php echo htmlspecialchars($date_from); ?>">
        <input type="date" name="date_to" value="<?php echo htmlspecialchars($date_to); ?>">

        <select name="sort">
            <option value="newest" <?php if ($sort == "newest") echo "selected"; ?>>Newest First</option>
            <option value="popular" <?php if ($sort == "popular") echo "selected"; ?>>Most Popular</option>
        </select>

        <button type="submit">Search</button>
    </form>

    <div class="movie-grid">
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($movie = mysqli_fetch_assoc($result)): ?>
                <?php
                $ratingDisplay = ($movie["avg_rating"] == floor($movie["avg_rating"]))
                    ? floor($movie["avg_rating"])
                    : number_format($movie["avg_rating"], 1);
                ?>

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
                    <p><strong>Creator:</strong> <?php echo htmlspecialchars($movie["creator_name"]); ?></p>
                    <p><?php echo htmlspecialchars($movie["short_description"]); ?></p>
                    <p>
                            <strong>Rating:</strong>
                            <?php
                            if ($movie["avg_rating"] > 0) {
                                echo rtrim(rtrim(number_format($movie["avg_rating"], 1), '0'), '.') . "/5";
                            } else {
                                echo "No ratings yet";
                            }
                            ?>
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