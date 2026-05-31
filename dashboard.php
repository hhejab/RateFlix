<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$roleName = "Viewer";

if ($_SESSION["role_id"] == 1) {
    $roleName = "Admin";
} elseif ($_SESSION["role_id"] == 2) {
    $roleName = "Creator";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - RateFlix</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<header>
    <h1>RateFlix Dashboard</h1>
    <nav>
        <a href="index.php">Home</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<div class="container">
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</h2>
    <p>Your role: <?php echo $roleName; ?></p>

    <?php if ($_SESSION["role_id"] == 1): ?>
        <a class="btn" href="admin/index.php">Admin Panel</a>
    <?php elseif ($_SESSION["role_id"] == 2): ?>
        <a class="btn" href="creator/my_movies.php">Creator Panel</a>
    <?php else: ?>
        <a class="btn" href="index.php">Browse Movies</a>
    <?php endif; ?>
</div>

</body>
</html>