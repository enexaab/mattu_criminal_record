<?php
// dashboard.php
require 'db_connect.php';

// Check if user is logged in, otherwise redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
        <nav>
            <a href="view_criminals.php">View Criminals</a>
            <a href="add_criminal.php">Add Criminal</a>
            <a href="#">Manage Cases</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>
    <main>
        <h2>System Dashboard</h2>
        <p>Select an option from the navigation to get started.</p>
    </main>
</body>
</html>