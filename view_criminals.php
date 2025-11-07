<?php
// view_criminals.php
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$result = $conn->query("SELECT * FROM criminals ORDER BY last_name, first_name");
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Criminal Records</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1>All Criminal Records</h1>
        <nav><a href="dashboard.php">Back to Dashboard</a></nav>
    </header>
    <main>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Date of Birth</th>
                    <th>Gender</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['criminal_id']; ?></td>
                    <td><?php echo htmlspecialchars($row['first_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['last_name']); ?></td>
                    <td><?php echo $row['dob']; ?></td>
                    <td><?php echo $row['gender']; ?></td>
                    <td><a href="#">View</a> | <a href="#">Edit</a></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </main>
</body>
</html>