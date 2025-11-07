<?php
// admin/api/manage_users.php
require '../../includes/auth.php';
require '../../includes/database.php';
require '../../includes/admin_functions.php';

if (!isset($_SESSION['user_id'])) {
    header('HTTP/1.1 401 Unauthorized');
    exit();
}

header('Content-Type: text/html');

// Return the user management HTML content
ob_start();
echo "Manage Users API is working!<br>";
if (file_exists('../usermanagement.php')) {
    include '../usermanagement.php';
} else {
    echo "Error: usermanagement.php not found.";
}
$content = ob_get_clean();
echo $content;
?>