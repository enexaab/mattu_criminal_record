<?php
// 404.php - Custom Not Found Page
ini_set('display_errors', 1); // Temp: Show PHP errors for debugging
error_reporting(E_ALL);
session_start();
$current_lang = $_SESSION['lang'] ?? 'en';

// Full translations from index.php
$translations = [
    'en' => [
        'title' => 'Mattu Criminal Record System - Secure Login',
        'mattu_city_criminal_record_system' => 'Mattu City Criminal Record System',
        'secure_authentication_portal' => 'Secure Authentication Portal',
        'security_policy_enforced' => 'Security Policy Enforced',
        'username_or_user_id' => 'Username or User ID',
        'enter_your_username_or_id' => 'Enter your username or ID',
        'password' => 'Password',
        'enter_your_password' => 'Enter your password',
        'remember_me' => 'Remember me',
        'forgot_password' => 'Forgot password?',
        'sign_in_securely' => 'Sign In Securely',
        'minimum_character_password' => 'Minimum {min} character password',
        'account_lock_after_failed_attempts' => 'Account lock after {max} failed attempts',
        'auto_logout_after_minutes' => 'Auto-logout after {timeout} minutes of inactivity',
        'system_status' => 'System Status',
        'security_policy_active' => 'Security Policy Active',
        'failed_logins_today' => 'Failed Logins Today',
        'security_level' => 'Security Level',
        'high' => 'HIGH',
        'please_fill_in_all_required_fields' => 'Please fill in all required fields.',
        'invalid_credentials' => 'Invalid credentials. {remaining} attempts remaining.',
        'account_locked' => 'Account locked due to too many failed attempts.',
        'account_deactivated' => 'Account is deactivated. Please contact administrator.',
        'login_blocked' => 'Login blocked due to too many failed attempts.',
        'database_error' => 'Database error during login preparation: {error}',
        'contact_admin_for_reset' => 'Please contact your system administrator for password reset assistance.',
        'password_must_be_at_least' => 'Password must be at least {min} characters',
    ],
    'am' => [
        'title' => 'ማቱ የወደንጀላዊ መዝገብ ስርዓት - ደህንነቱ የተጠበቀ መግቢያ',
        'mattu_city_criminal_record_system' => 'ማቱ ከተማ የወደንጀላዊ መዝገብ ስርዓት',
        'secure_authentication_portal' => 'ደህንነቱ የተጠበቀ ማረጋገጫ ቦታ',
        'security_policy_enforced' => 'ደህንነት ፖሊሲ ተደፈረ',
        'username_or_user_id' => 'የተጠቃሚ ስም ወይም የተጠቃሚ ID',
        'enter_your_username_or_id' => 'የተጠቃሚ ስምዎ ወይም ID ያስገቡ',
        'password' => 'የይለፍ ቃል',
        'enter_your_password' => 'የይለፍ ቃልዎን ያስገቡ',
        'remember_me' => 'እኔን አስታውሱ',
        'forgot_password' => 'የይለፍ ቃል ረስተው?',
        'sign_in_securely' => 'በደህንነት ይግቡ',
        'minimum_character_password' => 'ቢያንስ {min} ቁምፊ የይለፍ ቃል',
        'account_lock_after_failed_attempts' => 'በ{ max} የላቀ ሙከራ ግብር አካውንት ይቆርጠዋል',
        'auto_logout_after_minutes' => 'በ{timeout} ደቂቃ ውስጥ ያለ እንቅስቃሴ በአውቶማቲክ ይወጣል',
        'system_status' => 'ስርዓት ሁኔታ',
        'security_policy_active' => 'ደህንነት ፖሊሲ አክቲቭ',
        'failed_logins_today' => 'ዛሬ የላቀ መግቢያዎች',
        'security_level' => 'ደህንነት ደረጃ',
        'high' => 'ከፍተኛ',
        'please_fill_in_all_required_fields' => 'የሚያስፈልጉ አካባቢዎችን ይሙሉ።',
        'invalid_credentials' => 'የላቀ የማረጋገጫ መረጃዎች። {remaining} ሙከራዎች ቀርተዋል።',
        'account_locked' => 'በብዙ የላቀ ሙከራዎች ምክንያት አካውንት ተቆለፈ።',
        'account_deactivated' => 'አካውንት ተቀልሎ ተገለጠው ነው። አስተዳዳሪን ያነጋግሩ።',
        'login_blocked' => 'በብዙ የላቀ ሙከራዎች ምክንያት መግቢያ ተቆለፈ።',
        'database_error' => 'በመግቢያ ወቅት የዳታቤዝ ስህተት: {error}',
        'contact_admin_for_reset' => 'የይለፍ ቃል ለመድል ስርዓት አስተዳዳሪዎን ያነጋግሩ።',
        'password_must_be_at_least' => 'የይለፍ ቃል ቢያንስ {min} ቁምፊ መሆን አለበት',
    ],
    'om' => [
        'title' => 'Sisteemi Diinagdee Mattu Kuta - Meegbiin Dhiinagdee',
        'mattu_city_criminal_record_system' => 'Sisteemi Diinagdee Mattu Kuta',
        'secure_authentication_portal' => 'Meegbiin Dhiinagdee',
        'security_policy_enforced' => 'Polisii Dhiinagdee Deebii',
        'username_or_user_id' => 'Naama Userii ykn ID',
        'enter_your_username_or_id' => 'Naama userii ykn ID argisi',
        'password' => 'Qoricha',
        'enter_your_password' => 'Qoricha argisi',
        'remember_me' => 'Na hin deebii',
        'forgot_password' => 'Qoricha Deebii?',
        'sign_in_securely' => 'Meegbiin Dhiinagdee',
        'minimum_character_password' => 'Qoricha {min} qoricha',
        'account_lock_after_failed_attempts' => '{max} qoricha deebii akka qoricha',
        'auto_logout_after_minutes' => '{timeout} deeqii akka qoricha',
        'system_status' => 'Hakkina Sisteemi',
        'security_policy_active' => 'Polisii Dhiinagdee',
        'failed_logins_today' => 'Guyyaa Deebii Meegbiin',
        'security_level' => 'Deerja Dhiinagdee',
        'high' => 'Keessaa',
        'please_fill_in_all_required_fields' => 'Qoricha qabuu argisi.',
        'invalid_credentials' => 'Qoricha hin taane. {remaining} qoricha.',
        'account_locked' => 'Qoricha qoricha deebii.',
        'account_deactivated' => 'Qoricha qoricha deebii.',
        'login_blocked' => 'Meegbiin qoricha deebii.',
        'database_error' => 'Sisteemi qoricha: {error}',
        'contact_admin_for_reset' => 'Qoricha deebii argisi.',
        'password_must_be_at_least' => 'Qoricha {min} qoricha.',
    ],
];

// Full t() function (simplified for errors)
function t($key, $params = []) {
    global $translations, $current_lang;
    $trans = $translations[$current_lang][$key] ?? $key;
    $defaults = [
        'min' => 6,
        'max' => 5,
        'timeout' => 60,
        'remaining' => '',
        'error' => ''
    ];
    $params = array_merge($defaults, $params);
    if (strpos($trans, '{') !== false) {
        foreach ($params as $placeholder => $value) {
            $trans = str_replace('{' . $placeholder . '}', $value, $trans);
        }
    }
    return $trans;
}
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo t('title'); ?> - 404 Error</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Ethiopic:wght@400;700&display=swap" rel="stylesheet">
    <?php if ($current_lang == 'am' || $current_lang == 'om'): ?>
    <style>body { font-family: 'Noto Sans Ethiopic', sans-serif; }</style>
    <?php endif; ?>
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .error-container { text-align: center; color: white; max-width: 600px; padding: 2rem; }
        .error-image { max-width: 100%; height: auto; margin-bottom: 1rem; border-radius: 12px; }
        .btn-back { @apply bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition-colors mt-4 inline-block; }
    </style>
</head>
<body>
    <div class="error-container">
        <!-- Embed your SVG -->
<img src="/mattupolice/assets/errors/404-error.svg" alt="404 Error" class="error-image" onerror="this.outerHTML='<svg width=400 height=300 xmlns=http://www.w3.org/2000/svg><text x=50% y=50% text-anchor=middle dy=.3em fill=white font-size=18>SVG Load Error - Contact Admin</text></svg>'">      
        <h1 class="text-3xl font-bold mb-4">404 - Page Not Found</h1>
        <p class="mb-6">The requested URL was not found. Check your spelling or <a href="index.php" class="underline">return to login</a>.</p>
        
<a href="/mattupolice/index.php" class="btn-back">Back to Secure Login</a>
    </div>
</body>
</html>
