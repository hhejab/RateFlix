<?php
session_start();
include '../config.php';

if (!isset($_SESSION["user_id"]) || $_SESSION["role_id"] != 1) {
    die("Access denied. Admin only.");
}

$users = mysqli_query($conn, "
    SELECT u.user_id, u.username, u.email, u.created_at, r.role_name
    FROM dbProj_users u
    JOIN dbProj_roles r ON u.role_id = r.role_id
    ORDER BY u.created_at DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Users - RateFlix</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<header>
    <h1>Admin Panel</h1>
    <nav>
        <a href="index.php">Dashboard</a>
        <a href="reports.php">Reports</a>
        <a href="../logout.php">Logout</a>
    </nav>
</header>

<div class="container">
    <h2>Manage Users</h2>

    <table border="1" cellpadding="10" cellspacing="0" width="100%">
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Role</th>
            <th>Created At</th>
        </tr>

        <?php while ($user = mysqli_fetch_assoc($users)): ?>
            <tr>
                <td><?php echo $user["user_id"]; ?></td>
                <td><?php echo htmlspecialchars($user["username"]); ?></td>
                <td><?php echo htmlspecialchars($user["email"]); ?></td>
                <td><?php echo htmlspecialchars($user["role_name"]); ?></td>
                <td><?php echo $user["created_at"]; ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>