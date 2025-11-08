<?php
// usermanagement.php

// Your existing requires
require '../includes/auth.php';
require '../includes/database.php';
require '../includes/admin_functions.php';

// Language support
$languages = ['en', 'am', 'om'];
$current_lang = $_SESSION['lang'] ?? 'en';
if (isset($_POST['lang']) && in_array($_POST['lang'], $languages)) {
    $_SESSION['lang'] = $_POST['lang'];
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
} elseif (isset($_GET['lang']) && in_array($_GET['lang'], $languages)) {
    $_SESSION['lang'] = $_GET['lang'];
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

$translations = [
    'en' => [
        'title' => 'User Management - Mattu Criminal Record System',
        'header_title' => 'User Management',
        'header_subtitle' => 'Manage system users and their permissions',
        'msg_user_created' => 'User created successfully!',
        'msg_create_failed' => 'Failed to create user. Username might already exist.',
        'msg_user_updated' => 'User updated successfully!',
        'msg_update_failed' => 'Failed to update user. Username might already exist.',
        'msg_password_reset' => 'Password reset successfully for user!',
        'msg_reset_failed' => 'Failed to reset password.',
        'msg_status_toggled' => 'User status updated successfully!',
        'msg_toggle_failed' => 'Failed to update user status.',
        'table_id' => 'ID',
        'table_name' => 'Name',
        'table_username' => 'Username',
        'table_email' => 'Email',
        'table_role' => 'Role',
        'table_status' => 'Status',
        'table_last_login' => 'Last Login',
        'table_created' => 'Created',
        'table_actions' => 'Actions',
        'status_active' => 'Active',
        'status_inactive' => 'Inactive',
        'never_logged_in' => 'Never',
        'no_users_found' => 'No users found in the system',
        'btn_create_user' => 'Create New User',
        'btn_edit' => 'Edit',
        'btn_reset_password' => 'Reset Password',
        'btn_deactivate' => 'Deactivate',
        'btn_activate' => 'Activate',
        'modal_create_title' => 'Create New User',
        'modal_edit_title' => 'Edit User',
        'modal_reset_title' => 'Reset Password',
        'label_first_name' => 'First Name',
        'label_last_name' => 'Last Name',
        'label_username' => 'Username',
        'label_email' => 'Email',
        'label_password' => 'Password',
        'label_role' => 'Role',
        'label_status' => 'Status',
        'placeholder_search' => 'Search by name, username, or email...',
        'filter_all_roles' => 'All Roles',
        'filter_all_status' => 'All Status',
        'btn_apply_filter' => 'Apply',
        'password_strength_very_weak' => 'Very Weak',
        'password_strength_weak' => 'Weak',
        'password_strength_medium' => 'Medium',
        'password_strength_strong' => 'Strong',
        'password_enter' => 'Enter password',
        'confirm_activate' => 'Are you sure you want to activate the account for',
        'confirm_deactivate' => 'Are you sure you want to deactivate the account for',
        'debug_title' => 'Debug Information',
        'debug_db_info' => 'Database Info:',
        'debug_total_users' => 'Total Users:',
        'debug_total_roles' => 'Total Roles:',
        'debug_current_user' => 'Current User ID:',
        'debug_sample_data' => 'Sample Data Check:',
        'debug_first_user' => 'First user:',
        'debug_first_user_id' => 'First user ID:',
        'debug_no_users' => 'No users found in database!',
        'debug_first_role' => 'First role:',
        'debug_no_roles' => 'No roles found in database!',
        'btn_cancel' => 'Cancel',
        'btn_update_user' => 'Update User',
        'btn_reset_password_confirm' => 'Reset Password',
        'alert_password_warning' => 'This will reset the password for',
        'password_min_length_error' => 'Password must be at least 8 characters long',
        'js_user_updated' => 'User updated successfully!',
        'js_password_reset' => 'Password reset successfully!',
        'js_status_activated' => 'User activated successfully!',
        'js_status_deactivated' => 'User deactivated successfully!',
        'js_error_generic' => 'An error occurred. Please try again.',
        'js_load_failed' => 'Failed to load user data. Please try again.',
        'js_status_update_failed' => 'Failed to update user status. Please try again.',
        'lang_english' => 'English',
        'lang_amharic' => 'አማርኛ',
        'lang_oromo' => 'Afaan Oromoo',
    ],
    'am' => [
        'title' => 'ተጠቃሚ አስተዳደር - ማቱ የወንጀል መዝገብ ስርዓት',
        'header_title' => 'ተጠቃሚ አስተዳደር',
        'header_subtitle' => 'ስርዓት ተጠቃሚዎችን እና ፍቃዶችን ይቆጣጠሩ',
        'msg_user_created' => 'ተጠቃሚ በተሳካ ሁኔታ ተፈጠረ!',
        'msg_create_failed' => 'ተጠቃሚ መፍጠር አልተሳካም። የተጠቃሚ ስም ቀደም ሲል ሊኖር ይችላል።',
        'msg_user_updated' => 'ተጠቃሚ በተሳካ ሁኔታ ተዘመነ!',
        'msg_update_failed' => 'ተጠቃሚ ማዘመን አልተሳካም። የተጠቃሚ ስም ቀደም ሲል ሊኖር ይችላል።',
        'msg_password_reset' => 'የተጠቃሚ የይለፍ ቃል በተሳካ ሁኔታ ተዘመነ!',
        'msg_reset_failed' => 'የይለፍ ቃል ማዘመን አልተሳካም።',
        'msg_status_toggled' => 'የተጠቃሚ ሁኔታ በተሳካ ሁኔታ ተዘመነ!',
        'msg_toggle_failed' => 'የተጠቃሚ ሁኔታ ማዘመን አልተሳካም።',
        'table_id' => 'ID',
        'table_name' => 'ስም',
        'table_username' => 'የተጠቃሚ ስም',
        'table_email' => 'ኢሜይል',
        'table_role' => 'ሚና',
        'table_status' => 'ሁኔታ',
        'table_last_login' => 'የመጨረሻ ግባ',
        'table_created' => 'ተፈጠረ',
        'table_actions' => 'እርምጃዎች',
        'status_active' => 'ንቁ',
        'status_inactive' => 'ያልተክተለ',
        'never_logged_in' => 'በተለይ ተገባ',
        'no_users_found' => 'በስርዓቱ ውስጥ ተጠቃሚዎች አልተገኙም',
        'btn_create_user' => 'አዲስ ተጠቃሚ ፍጠር',
        'btn_edit' => 'አስተካክል',
        'btn_reset_password' => 'የይለፍ ቃል ዳግም ማስጀመር',
        'btn_deactivate' => 'አባይ',
        'btn_activate' => 'ንቁ አድርግ',
        'modal_create_title' => 'አዲስ ተጠቃሚ ፍጠር',
        'modal_edit_title' => 'ተጠቃሚ አስተካክል',
        'modal_reset_title' => 'የይለፍ ቃል ዳግም ማስጀመር',
        'label_first_name' => 'የመጀመሪያ ስም',
        'label_last_name' => 'የጄና ስም',
        'label_username' => 'የተጠቃሚ ስም',
        'label_email' => 'ኢሜይል',
        'label_password' => 'የይለፍ ቃል',
        'label_role' => 'ሚና',
        'label_status' => 'ሁኔታ',
        'placeholder_search' => 'በስም፣ የተጠቃሚ ስም ወይም ኢሜይል ፍለጋ...',
        'filter_all_roles' => 'ሁሉም ሚናዎች',
        'filter_all_status' => 'ሁሉም ሁኔታ',
        'btn_apply_filter' => 'ተግባራዊ አድርግ',
        'password_strength_very_weak' => 'በጣም ደካማ',
        'password_strength_weak' => 'ደካማ',
        'password_strength_medium' => 'መካከለኛ',
        'password_strength_strong' => 'ጠንካራ',
        'password_enter' => 'የይለፍ ቃል ያስገቡ',
        'confirm_activate' => 'የአካውንት ለንቁ ማድረግ ትፈልጋለህ?',
        'confirm_deactivate' => 'የአካውንት ለአባይ ማድረግ ትፈልጋለህ?',
        'debug_title' => 'የመሞከር መረጃ',
        'debug_db_info' => 'የውሂብ ቤዝ መረጃ:',
        'debug_total_users' => 'ጠቅላላ ተጠቃሚዎች:',
        'debug_total_roles' => 'ጠቅላላ ሚናዎች:',
        'debug_current_user' => 'አሁኑ ተጠቃሚ ID:',
        'debug_sample_data' => 'የተረጋገጠ ውሂብ ምርምር:',
        'debug_first_user' => 'የመጀመሪያ ተጠቃሚ:',
        'debug_first_user_id' => 'የመጀመሪያ ተጠቃሚ ID:',
        'debug_no_users' => 'በውሂብ ቤዝ ውስጥ ተጠቃሚዎች አልተገኙም!',
        'debug_first_role' => 'የመጀመሪያ ሚና:',
        'debug_no_roles' => 'በውሂብ ቤዝ ውስጥ ሚናዎች አልተገኙም!',
        'btn_cancel' => 'ይቅር',
        'btn_update_user' => 'ተጠቃሚ ይዘምኑ',
        'btn_reset_password_confirm' => 'የይለፍ ቃል ዳግም ማስጀመር',
        'alert_password_warning' => 'ይህ ለተጠቃሚ የይለፍ ቃሉን ዳግም ያስጀምራል',
        'password_min_length_error' => 'የይለፍ ቃሉ ቢያንስ 8 ቁምፊዎች ርዝመት መኖር አለበት',
        'js_user_updated' => 'ተጠቃሚ በተሳካ ሁኔታ ተዘመነ!',
        'js_password_reset' => 'የይለፍ ቃል በተሳካ ሁኔታ ተዘመነ!',
        'js_status_activated' => 'ተጠቃሚ በተሳካ ሁኔታ ተክተለ!',
        'js_status_deactivated' => 'ተጠቃሚ በተሳካ ሁኔታ ተቆለፈ!',
        'js_error_generic' => 'ስህተት ተከስቷል። እባክዎ ዳግም ይሞክሩ።',
        'js_load_failed' => 'የተጠቃሚ ውሂብ ማግኘት አልተሳካም። እባክዎ ዳግም ይሞክሩ።',
        'js_status_update_failed' => 'የተጠቃሚ ሁኔታ ማዘመን አልተሳካም። እባክዎ ዳግም ይሞክሩ።',
        'lang_english' => 'English',
        'lang_amharic' => 'አማርኛ',
        'lang_oromo' => 'Afaan Oromoo',
    ],
    'om' => [
        'title' => 'Imaammataan User - Sisteemi Ummata Mattu Diinagdee',
        'header_title' => 'Imaammataan User',
        'header_subtitle' => 'Imaammataan useroota fi bilisummaa',
        'msg_user_created' => 'User yoo taane argame!',
        'msg_create_failed' => 'User argachuu miti. Username yoo taane argame.',
        'msg_user_updated' => 'User yoo taane aadaa!',
        'msg_update_failed' => 'User aadaa miti. Username yoo taane argame.',
        'msg_password_reset' => 'Passwordii yoo taane aadaa user irratti!',
        'msg_reset_failed' => 'Passwordii aadaa miti.',
        'msg_status_toggled' => 'Balaa user yoo taane aadaa!',
        'msg_toggle_failed' => 'Balaa user aadaa miti.',
        'table_id' => 'ID',
        'table_name' => 'Maati',
        'table_username' => 'Username',
        'table_email' => 'Imeeli',
        'table_role' => 'Qabsoo',
        'table_status' => 'Balaa',
        'table_last_login' => 'Loginiin Utuu',
        'table_created' => 'Argame',
        'table_actions' => 'Irmaa',
        'status_active' => 'Ykn',
        'status_inactive' => 'Moo ykn',
        'never_logged_in' => 'Moo argamuu',
        'no_users_found' => 'Sisteemiin useroota argamuu miti',
        'btn_create_user' => 'User Yoo Taane Argachuu',
        'btn_edit' => 'Aadaa',
        'btn_reset_password' => 'Passwordii Reset',
        'btn_deactivate' => 'Deactivate',
        'btn_activate' => 'Activate',
        'modal_create_title' => 'User Yoo Taane Argachuu',
        'modal_edit_title' => 'User Aadaa',
        'modal_reset_title' => 'Passwordii Reset',
        'label_first_name' => 'Maati Garaa',
        'label_last_name' => 'Maati Garaa',
        'label_username' => 'Username',
        'label_email' => 'Imeeli',
        'label_password' => 'Password',
        'label_role' => 'Qabsoo',
        'label_status' => 'Balaa',
        'placeholder_search' => 'Maati, username, ykn imeeliin argachuu...',
        'filter_all_roles' => 'Qabsoota Hundee',
        'filter_all_status' => 'Balaa Hundee',
        'btn_apply_filter' => 'Tegbaa',
        'password_strength_very_weak' => 'Balaa Moo',
        'password_strength_weak' => 'Balaa',
        'password_strength_medium' => 'Meeka',
        'password_strength_strong' => 'Balaa',
        'password_enter' => 'Password argachuu',
        'confirm_activate' => 'User activate barbaachisa?',
        'confirm_deactivate' => 'User deactivate barbaachisa?',
        'debug_title' => 'Maatii Debug',
        'debug_db_info' => 'Database Maatii:',
        'debug_total_users' => 'Useroota Jijjiirama:',
        'debug_total_roles' => 'Qabsoota Jijjiirama:',
        'debug_current_user' => 'User Yoo Taane ID:',
        'debug_sample_data' => 'Sample Data Check:',
        'debug_first_user' => 'User Garaa:',
        'debug_first_user_id' => 'User Garaa ID:',
        'debug_no_users' => 'Databasein useroota argamuu miti!',
        'debug_first_role' => 'Qabsoo Garaa:',
        'debug_no_roles' => 'Databasein qabsoota argamuu miti!',
        'btn_cancel' => 'Fufiisi',
        'btn_update_user' => 'User Aadaa',
        'btn_reset_password_confirm' => 'Passwordii Reset',
        'alert_password_warning' => 'Passwordii user irratti reset yoo taane',
        'password_min_length_error' => 'Passwordii 8 chars min',
        'js_user_updated' => 'User yoo taane aadaa!',
        'js_password_reset' => 'Passwordii yoo taane aadaa!',
        'js_status_activated' => 'User activated!',
        'js_status_deactivated' => 'User deactivated!',
        'js_error_generic' => 'Maatii argame. Dagaa yoo taane.',
        'js_load_failed' => 'User data argachuu miti. Dagaa yoo taane.',
        'js_status_update_failed' => 'Balaa user aadaa miti. Dagaa yoo taane.',
        'lang_english' => 'English',
        'lang_amharic' => 'አማርኛ',
        'lang_oromo' => 'Afaan Oromoo',
    ],
];

function t($key) {
    global $translations, $current_lang;
    return $translations[$current_lang][$key] ?? $key;
}

// Check if user is logged in, otherwise redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Get current user ID for session updates
$current_user_id = $_SESSION['user_id'];

// Initialize database connection
//dsfkdsnfdklnfskl
// Initialize database connection
$database = new Database();
$db = $database->getConnection();
$adminFunctions = new AdminFunctions($db);



// Handle form submissions
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create_user') {
        $userData = [
            'username' => trim($_POST['username']),
            'password' => $_POST['password'],
            'first_name' => trim($_POST['first_name']),
            'last_name' => trim($_POST['last_name']),
            'email' => trim($_POST['email']),
            'role_id' => $_POST['role_id']
        ];
        
        if (createUser($userData)) {
            $message = t('msg_user_created');
            logActivity($_SESSION['user_id'], 'user_create', "Created user: " . $userData['username']);
        } else {
            $error = t('msg_create_failed');
        }
    }
    elseif ($action === 'update_user') {
        $userData = [
            'user_id' => $_POST['user_id'],
            'username' => trim($_POST['username']),
            'first_name' => trim($_POST['first_name']),
            'last_name' => trim($_POST['last_name']),
            'email' => trim($_POST['email']),
            'role_id' => $_POST['role_id'],
            'status' => $_POST['status']
        ];
        
        if (updateUser($userData)) {
            $message = t('msg_user_updated');
            logActivity($_SESSION['user_id'], 'user_update', "Updated user: " . $userData['username']);
            
            // If current user updated their own profile, update session
            if ($userData['user_id'] == $current_user_id) {
                $_SESSION['first_name'] = $userData['first_name'];
                $_SESSION['last_name'] = $userData['last_name'];
            }
        } else {
            $error = t('msg_update_failed');
        }
    }
    elseif ($action === 'reset_password') {
        $user_id = $_POST['user_id'];
        $new_password = $_POST['new_password'];
        
        if (resetUserPassword($user_id, $new_password)) {
            $message = t('msg_password_reset');
            logActivity($_SESSION['user_id'], 'password_reset', "Reset password for user ID: " . $user_id);
        } else {
            $error = t('msg_reset_failed');
        }
    }
    elseif ($action === 'toggle_status') {
        $user_id = $_POST['user_id'];
        $current_status = $_POST['current_status'];
        $new_status = $current_status === 'active' ? 'inactive' : 'active';
        
        $user = getUserById($user_id);
        $username = $user ? $user['username'] : 'Unknown User';
        
        if (toggleUserStatus($user_id, $new_status)) {
            $action_text = $new_status === 'active' ? t('btn_activate') : t('btn_deactivate');
            $message = t('msg_status_toggled');
            logActivity($_SESSION['user_id'], 'user_status', "Changed status for user ID: " . $user_id . " to " . $new_status);
        } else {
            $error = t('msg_toggle_failed');
        }
    }
}

// Debug: Check database connection
try {
    $test_stmt = $db->query("SELECT 1");
    echo "<!-- Debug: Database connection OK -->\n";
} catch (PDOException $e) {
    echo "<!-- Debug: Database connection FAILED: " . $e->getMessage() . " -->\n";
}

// Get all users and roles
$users = getAllUsers();
$roles = getRoles();

// Debug: Check what data we're getting
echo "<!-- Debug: Users count: " . count($users) . " -->\n";
echo "<!-- Debug: Roles count: " . count($roles) . " -->\n";

// If no users found, create a sample admin user for testing
if (empty($users)) {
    echo "<!-- Debug: No users found, creating sample data -->\n";
    try {
        $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $db->prepare("
            INSERT INTO users (username, password, role, first_name, last_name, email, is_active, created_at)
            VALUES ('admin', ?, 'administrator', 'System', 'Administrator', 'admin@system.com', 1, NOW())
        ");
        $stmt->execute([$hashedPassword]);
        
        // Refresh users list
        $users = getAllUsers();
    } catch (Exception $e) {
        echo "<!-- Debug: Failed to create sample user: " . $e->getMessage() . " -->\n";
    }
}

// Process users data for display - FIXED: Use user_id instead of id
foreach ($users as &$user) {
    // Add role_name for display (using the role field)
    $user['role_name'] = ucfirst($user['role']);
    // Ensure status field exists
    $user['status'] = $user['is_active'] ? t('status_active') : t('status_inactive');
    // Add role_id for form handling
    $user['role_id'] = $user['role'];
    // Map user_id to id for JavaScript compatibility
    $user['id'] = $user['user_id'];
}
?>

<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo t('title'); ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    
    <!-- Google Fonts for Amharic support -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Ethiopic:wght@400;700&display=swap" rel="stylesheet">
    
    <?php if ($current_lang == 'am'): ?>
    <style>
        body {
            font-family: 'Noto Sans Ethiopic', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
    </style>
    <?php endif; ?>
    
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
            color: #333;
            line-height: 1.6;
            padding: 0;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 10px 10px 0 0;
            margin-bottom: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .header h1 {
            margin: 0;
            font-size: 1.8rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .header p {
            margin: 5px 0 0 0;
            opacity: 0.9;
        }
        
        /* Language Selector in Header */
        .lang-selector {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .lang-selector form {
            display: inline;
        }
        
        .lang-selector select {
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            background: rgba(255,255,255,0.2);
            color: white;
            font-size: 14px;
        }
        
        .lang-selector select option {
            background: #667eea;
            color: white;
        }
        
        .content {
            background: white;
            padding: 30px;
            border-radius: 0 0 10px 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        
        .card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            margin-bottom: 25px;
            overflow: hidden;
        }
        
        .card-header {
            background: #f8f9fa;
            padding: 15px 20px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .card-header h3 {
            margin: 0;
            color: #495057;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .card-body {
            padding: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #495057;
        }
        
        .form-control {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }
        
        .btn-success {
            background: #28a745;
            color: white;
        }
        
        .btn-success:hover {
            background: #218838;
        }
        
        .btn-warning {
            background: #ffc107;
            color: #212529;
        }
        
        .btn-warning:hover {
            background: #e0a800;
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c82333;
        }
        
        .btn-sm {
            padding: 6px 12px;
            font-size: 0.8rem;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        
        .table th,
        .table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }
        
        .table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #495057;
        }
        
        .table tbody tr:hover {
            background: #f8f9fa;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .badge-success { background: #28a745; color: white; }
        .badge-warning { background: #ffc107; color: #212529; }
        .badge-danger { background: #dc3545; color: white; }
        .badge-info { background: #17a2b8; color: white; }
        .badge-primary { background: #667eea; color: white; }
        
        .action-buttons {
            display: flex;
            gap: 5px;
        }
        
        .search-filter-container {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
      .alert {
    padding: 15px;
    border-radius: 5px;
    margin-bottom: 20px;
    animation: slideDown 0.3s ease;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.alert-success {
    background: #d4edda;
    border: 1px solid #c3e6cb;
    color: #155724;
}

.alert-error {
    background: #f8d7da;
    border: 1px solid #f5c6cb;
    color: #721c24;
}
        
        .password-strength {
            margin-top: 10px;
        }
        
        .password-strength-bar {
            height: 5px;
            border-radius: 3px;
            transition: all 0.3s ease;
        }
        
        .password-strength.weak .password-strength-bar {
            background: #dc3545;
            width: 33%;
        }
        
        .password-strength.medium .password-strength-bar {
            background: #ffc107;
            width: 66%;
        }
        
        .password-strength.strong .password-strength-bar {
            background: #28a745;
            width: 100%;
        }
        
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.9);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }
        
        .loading-spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
    </div>

    <div class="container">
        <div class="header">
            <div>
                <h1><i class="fas fa-users-cog"></i> <?php echo t('header_title'); ?></h1>
                <p><?php echo t('header_subtitle'); ?></p>
            </div>
          
        </div>
        
        <div class="content">
            <?php if ($message): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <!-- Debug Information Card -->
            <div class="card mt-4">
                <div class="card-header">
                    <h3><i class="fas fa-bug"></i> <?php echo t('debug_title'); ?></h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5><?php echo t('debug_db_info'); ?></h5>
                            <ul>
                                <li><?php echo t('debug_total_users'); ?> <?php echo count($users); ?></li>
                                <li><?php echo t('debug_total_roles'); ?> <?php echo count($roles); ?></li>
                                <li><?php echo t('debug_current_user'); ?> <?php echo $current_user_id; ?></li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5><?php echo t('debug_sample_data'); ?></h5>
                            <?php if (!empty($users)): ?>
                                <p><?php echo t('debug_first_user'); ?> <?php echo htmlspecialchars($users[0]['first_name'] . ' ' . $users[0]['last_name']); ?></p>
                                <p><?php echo t('debug_first_user_id'); ?> <?php echo $users[0]['id']; ?> (user_id: <?php echo $users[0]['user_id']; ?>)</p>
                            <?php else: ?>
                                <p class="text-danger"><?php echo t('debug_no_users'); ?></p>
                            <?php endif; ?>
                            
                            <?php if (!empty($roles)): ?>
                                <p><?php echo t('debug_first_role'); ?> <?php echo htmlspecialchars($roles[0]['role_name']); ?></p>
                            <?php else: ?>
                                <p class="text-danger"><?php echo t('debug_no_roles'); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3>User Accounts</h3>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
                    <i class="fas fa-user-plus"></i> <?php echo t('btn_create_user'); ?>
                </button>
            </div>
            
            <!-- Search and Filter -->
            <div class="search-filter-container">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><?php echo t('placeholder_search'); ?></label>
                            <input type="text" class="form-control" id="searchInput" placeholder="<?php echo t('placeholder_search'); ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label><?php echo t('filter_all_roles'); ?></label>
                            <select class="form-control" id="roleFilter">
                                <option value=""><?php echo t('filter_all_roles'); ?></option>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?php echo $role['id']; ?>"><?php echo htmlspecialchars($role['role_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label><?php echo t('filter_all_status'); ?></label>
                            <select class="form-control" id="statusFilter">
                                <option value=""><?php echo t('filter_all_status'); ?></option>
                                <option value="active"><?php echo t('status_active'); ?></option>
                                <option value="inactive"><?php echo t('status_inactive'); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button class="btn btn-primary w-100" onclick="applyFilters()">
                            <i class="fas fa-filter"></i> <?php echo t('btn_apply_filter'); ?>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Users Table -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-table"></i> User Accounts</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="usersTable">
                            <thead>
                                <tr>
                                    <th><?php echo t('table_id'); ?></th>
                                    <th><?php echo t('table_name'); ?></th>
                                    <th><?php echo t('table_username'); ?></th>
                                    <th><?php echo t('table_email'); ?></th>
                                    <th><?php echo t('table_role'); ?></th>
                                    <th><?php echo t('table_status'); ?></th>
                                    <th><?php echo t('table_last_login'); ?></th>
                                    <th><?php echo t('table_created'); ?></th>
                                    <th><?php echo t('table_actions'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($users)): ?>
                                    <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td><?php echo $user['id']; ?></td>
                                            <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                                            <td>
                                                <span class="badge badge-primary"><?php echo htmlspecialchars($user['role_name']); ?></span>
                                            </td>
                                            <td>
                                                <span class="badge badge-<?php echo $user['is_active'] ? 'success' : 'danger'; ?>">
                                                    <?php echo $user['is_active'] ? t('status_active') : t('status_inactive'); ?>
                                                </span>
                                            </td>
                                            <td><?php echo $user['last_login'] ? date('M j, Y g:i A', strtotime($user['last_login'])) : t('never_logged_in'); ?></td>
                                            <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                                            <td class="action-buttons">
                                                <button class="btn btn-warning btn-sm" onclick="editUser(<?php echo $user['id']; ?>)"
                                                        <?php echo $user['id'] == $current_user_id ? 'disabled title="Cannot edit your own account"' : ''; ?>>
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-info btn-sm" onclick="resetPassword(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>')">
                                                    <i class="fas fa-key"></i>
                                                </button>
                                                <?php if ($user['is_active']): ?>
                                                    <button class="btn btn-danger btn-sm" onclick="toggleStatus(<?php echo $user['id']; ?>, 'inactive', '<?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>')"
                                                            <?php echo $user['id'] == $current_user_id ? 'disabled title="Cannot deactivate your own account"' : ''; ?>>
                                                        <i class="fas fa-user-slash"></i>
                                                    </button>
                                                <?php else: ?>
                                                    <button class="btn btn-success btn-sm" onclick="toggleStatus(<?php echo $user['id']; ?>, 'active', '<?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>')">
                                                        <i class="fas fa-user-check"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            <i class="fas fa-users fa-2x mb-2"></i><br>
                                            <?php echo t('no_users_found'); ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        <!-- Add this after the table closing tag -->
<div id="paginationContainer" class="pagination-wrapper"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Create User Modal -->
    <div class="modal fade" id="createUserModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-user-plus"></i> <?php echo t('modal_create_title'); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="createUserForm" method="POST">
                    <input type="hidden" name="action" value="create_user">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo t('label_first_name'); ?> *</label>
                                    <input type="text" class="form-control" name="first_name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo t('label_last_name'); ?> *</label>
                                    <input type="text" class="form-control" name="last_name" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo t('label_username'); ?> *</label>
                                    <input type="text" class="form-control" name="username" required>
                                    <small class="text-muted"><?php echo t('label_username'); ?> <?php echo t('label_username'); ?></small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo t('label_email'); ?></label>
                                    <input type="email" class="form-control" name="email">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo t('label_password'); ?> *</label>
                                    <input type="password" class="form-control" name="password" id="createPassword" required>
                                    <div class="password-strength" id="passwordStrength">
                                        <div class="password-strength-bar"></div>
                                        <small><?php echo t('password_enter'); ?>: <span id="strengthText"><?php echo t('password_enter'); ?></span></small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo t('label_role'); ?> *</label>
                                    <select class="form-control" name="role_id" required>
                                        <option value=""><?php echo t('label_role'); ?></option>
                                        <?php foreach ($roles as $role): ?>
                                            <option value="<?php echo $role['id']; ?>"><?php echo htmlspecialchars($role['role_name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo t('btn_cancel'); ?></button>
                        <button type="submit" class="btn btn-primary"><?php echo t('btn_create_user'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-user-edit"></i> <?php echo t('modal_edit_title'); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editUserForm" method="POST">
                    <input type="hidden" name="action" value="update_user">
                    <input type="hidden" name="user_id" id="editUserId">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo t('label_first_name'); ?> *</label>
                                    <input type="text" class="form-control" name="first_name" id="editFirstName" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo t('label_last_name'); ?> *</label>
                                    <input type="text" class="form-control" name="last_name" id="editLastName" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo t('label_username'); ?> *</label>
                                    <input type="text" class="form-control" name="username" id="editUsername" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo t('label_email'); ?></label>
                                    <input type="email" class="form-control" name="email" id="editEmail">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo t('label_role'); ?> *</label>
                                    <select class="form-control" name="role_id" id="editRoleId" required>
                                        <?php foreach ($roles as $role): ?>
                                            <option value="<?php echo $role['id']; ?>"><?php echo htmlspecialchars($role['role_name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo t('label_status'); ?></label>
                                    <select class="form-control" name="status" id="editStatus">
                                        <option value="active"><?php echo t('status_active'); ?></option>
                                        <option value="inactive"><?php echo t('status_inactive'); ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo t('btn_cancel'); ?></button>
                        <button type="submit" class="btn btn-primary"><?php echo t('btn_update_user'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Reset Password Modal -->
    <div class="modal fade" id="resetPasswordModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-key"></i> <?php echo t('modal_reset_title'); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="resetPasswordForm" method="POST">
                    <input type="hidden" name="action" value="reset_password">
                    <input type="hidden" name="user_id" id="resetUserId">
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <?php echo t('alert_password_warning'); ?> <strong id="resetUserName"></strong>
                        </div>
                        <div class="form-group">
                            <label><?php echo t('label_password'); ?> *</label>
                            <input type="password" class="form-control" name="new_password" id="resetNewPassword" required>
                            <div class="password-strength" id="resetPasswordStrength">
                                <div class="password-strength-bar"></div>
                                <small><?php echo t('password_enter'); ?>: <span id="resetStrengthText"><?php echo t('password_enter'); ?></span></small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo t('btn_cancel'); ?></button>
                        <button type="submit" class="btn btn-danger"><?php echo t('btn_reset_password_confirm'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    
    <script>
      let usersTable;

// Initialize DataTable
document.addEventListener('DOMContentLoaded', function() {
    usersTable = $('#usersTable').DataTable({
        responsive: true,
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        order: [[0, 'desc']]
    });
    
    // Password strength checking
    document.getElementById('createPassword').addEventListener('input', function() {
        checkPasswordStrength(this.value, 'passwordStrength', 'strengthText');
    });
    
    document.getElementById('resetNewPassword').addEventListener('input', function() {
        checkPasswordStrength(this.value, 'resetPasswordStrength', 'resetStrengthText');
    });
    
    // Initialize form submissions
    initializeFormSubmissions();
});

// Initialize form submissions with AJAX
function initializeFormSubmissions() {
    // Edit User Form
    document.getElementById('editUserForm').addEventListener('submit', function(e) {
        e.preventDefault();
        submitForm(this, '<?php echo addslashes(t("js_user_updated")); ?>');
    });
    
    // Reset Password Form
    document.getElementById('resetPasswordForm').addEventListener('submit', function(e) {
        e.preventDefault();
        submitForm(this, '<?php echo addslashes(t("js_password_reset")); ?>');
    });
    
    // Create User Form (keep as regular submit since it needs to refresh user list)
    document.getElementById('createUserForm').addEventListener('submit', function(e) {
        const password = document.getElementById('createPassword').value;
        if (password.length < 8) {
            e.preventDefault();
            showAlert('<?php echo addslashes(t("password_min_length_error")); ?>', 'error');
            return false;
        }
        showLoading(true);
    });
}

// AJAX form submission
function submitForm(form, successMessage) {
    showLoading(true);
    
    const formData = new FormData(form);
    
    fetch('usermanagement.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(html => {
        // Create a temporary element to parse the response
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = html;
        
        // Check for success message
        const successAlert = tempDiv.querySelector('.alert-success');
        const errorAlert = tempDiv.querySelector('.alert-error');
        
        if (successAlert) {
            showAlert(successAlert.textContent, 'success');
            // Close the modal
            const modal = bootstrap.Modal.getInstance(form.closest('.modal'));
            if (modal) {
                modal.hide();
            }
            // Reload the page after a short delay to see changes
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else if (errorAlert) {
            showAlert(errorAlert.textContent, 'error');
        } else {
            showAlert(successMessage, 'success');
            // Close the modal
            const modal = bootstrap.Modal.getInstance(form.closest('.modal'));
            if (modal) {
                modal.hide();
            }
            // Reload the page after a short delay to see changes
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('<?php echo addslashes(t("js_error_generic")); ?>', 'error');
    })
    .finally(() => {
        showLoading(false);
    });
}

// EDIT USER
function editUser(userId) {
    console.log('Editing user ID:', userId);
    showLoading(true);
    
    fetch(`api/user_actions.php?action=get_user&id=${userId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const user = data.data;
                
                // Fill the edit form
                document.getElementById('editUserId').value = user.id;
                document.getElementById('editFirstName').value = user.first_name;
                document.getElementById('editLastName').value = user.last_name;
                document.getElementById('editUsername').value = user.username;
                document.getElementById('editEmail').value = user.email || '';
                
                // Set role
                const roleSelect = document.getElementById('editRoleId');
                for (let i = 0; i < roleSelect.options.length; i++) {
                    if (roleSelect.options[i].text.toLowerCase() === user.role_name.toLowerCase()) {
                        roleSelect.selectedIndex = i;
                        break;
                    }
                }
                
                // Set status
                document.getElementById('editStatus').value = user.status;
                
                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('editUserModal'));
                modal.show();
            } else {
                showAlert('Error: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('<?php echo addslashes(t("js_load_failed")); ?>', 'error');
        })
        .finally(() => {
            showLoading(false);
        });
}

// RESET PASSWORD
function resetPassword(userId, userName) {
    document.getElementById('resetUserId').value = userId;
    document.getElementById('resetUserName').textContent = userName;
    document.getElementById('resetNewPassword').value = '';
    
    // Reset password strength indicator
    const container = document.getElementById('resetPasswordStrength');
    const text = document.getElementById('resetStrengthText');
    container.className = 'password-strength';
    text.textContent = '<?php echo t("password_enter"); ?>';
    
    const modal = new bootstrap.Modal(document.getElementById('resetPasswordModal'));
    modal.show();
}

// TOGGLE STATUS - Improved with better confirmation
function toggleStatus(userId, newStatus, userName) {
    const action = newStatus === 'active' ? '<?php echo addslashes(t("confirm_activate")); ?>' : '<?php echo addslashes(t("confirm_deactivate")); ?>';
    const confirmMessage = `${action} ${userName}?`;
    
    if (confirm(confirmMessage)) {
        showLoading(true);
        
        const formData = new FormData();
        formData.append('action', 'toggle_status');
        formData.append('user_id', userId);
        formData.append('current_status', newStatus === 'active' ? 'inactive' : 'active');
        
        fetch('usermanagement.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(html => {
            // Create a temporary element to parse the response
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;
            
            // Check for success message
            const successAlert = tempDiv.querySelector('.alert-success');
            const errorAlert = tempDiv.querySelector('.alert-error');
            
            if (successAlert) {
                showAlert(successAlert.textContent, 'success');
                // Reload the page after a short delay to see changes
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else if (errorAlert) {
                showAlert(errorAlert.textContent, 'error');
            } else {
                const actionText = newStatus === 'active' ? '<?php echo addslashes(t("js_status_activated")); ?>' : '<?php echo addslashes(t("js_status_deactivated")); ?>';
                showAlert(actionText, 'success');
                // Reload the page after a short delay to see changes
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('<?php echo addslashes(t("js_status_update_failed")); ?>', 'error');
        })
        .finally(() => {
            showLoading(false);
        });
    }
}

// Show alert message
function showAlert(message, type) {
    // Remove any existing custom alerts
    const existingAlert = document.getElementById('customAlert');
    if (existingAlert) {
        existingAlert.remove();
    }
    
    const alertClass = type === 'success' ? 'alert-success' : 'alert-error';
    const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
    
    const alertDiv = document.createElement('div');
    alertDiv.id = 'customAlert';
    alertDiv.className = `alert ${alertClass}`;
    alertDiv.innerHTML = `<i class="fas ${icon}"></i> ${message}`;
    
    // Insert after the header
    const contentDiv = document.querySelector('.content');
    contentDiv.insertBefore(alertDiv, contentDiv.firstChild);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

// Apply filters to DataTable
function applyFilters() {
    const searchValue = document.getElementById('searchInput').value;
    const roleValue = document.getElementById('roleFilter').value;
    const statusValue = document.getElementById('statusFilter').value;
    
    usersTable.search(searchValue).draw();
    
    // Custom filtering for role and status
    $.fn.dataTable.ext.search.push(
        function(settings, data, dataIndex) {
            const role = data[4]; // Role column
            const status = data[5]; // Status column
            
            let roleMatch = true;
            let statusMatch = true;
            
            if (roleValue) {
                const roleSelect = document.getElementById('roleFilter');
                const roleText = roleSelect.options[roleSelect.selectedIndex].text;
                roleMatch = role.includes(roleText);
            }
            
            if (statusValue) {
                statusMatch = status.toLowerCase().includes(statusValue);
            }
            
            return roleMatch && statusMatch;
        }
    );
    
    usersTable.draw();
    $.fn.dataTable.ext.search.pop();
}

// Password strength checker
function checkPasswordStrength(password, containerId, textId) {
    const container = document.getElementById(containerId);
    const text = document.getElementById(textId);
    
    let strength = 0;
    let strengthText = '<?php echo t("password_strength_very_weak"); ?>';
    
    if (password.length >= 8) strength++;
    if (/[a-z]/.test(password)) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;
    
    container.className = 'password-strength';
    
    if (password.length === 0) {
        strengthText = '<?php echo t("password_enter"); ?>';
    } else if (strength < 2) {
        container.classList.add('weak');
        strengthText = '<?php echo t("password_strength_weak"); ?>';
    } else if (strength < 4) {
        container.classList.add('medium');
        strengthText = '<?php echo t("password_strength_medium"); ?>';
    } else {
        container.classList.add('strong');
        strengthText = '<?php echo t("password_strength_strong"); ?>';
    }
    
    text.textContent = strengthText;
}

// Loading overlay
function showLoading(show) {
    document.getElementById('loadingOverlay').style.display = show ? 'flex' : 'none';
}

// Add form submission handlers to hide loading on modal close
document.addEventListener('hidden.bs.modal', function() {
    showLoading(false);
});
    </script>
</body>
</html>