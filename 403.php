<?php
// 403.php - Custom Forbidden Page
session_start();
$current_lang = $_SESSION['lang'] ?? 'en';

// Reuse your translations (copy from index.php or include it)
$translations = [ /* Paste the entire $translations array from index.php here */ ];
function t($key, $params = []) { /* Paste the entire t() function from index.php here */ }

?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo t('title'); ?> - 403 Forbidden</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Ethiopic:wght@400;700&display=swap" rel="stylesheet">
    <?php if ($current_lang == 'am' || $current_lang == 'om'): ?>
    <style>body { font-family: 'Noto Sans Ethiopic', sans-serif; }</style>
    <?php endif; ?>
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .error-container { text-align: center; color: white; max-width: 600px; padding: 2rem; }
        .error-image { max-width: 100%; height: auto; margin-bottom: 1rem; border-radius: 12px; }
        .btn-back { @apply bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-semibold transition-colors mt-4 inline-block; }
    </style>
</head>
<body>
    <div class="error-container">
        <!-- Embed your GIF -->
       <img src="/mattupolice/assets/errors/403-forbidden.gif" alt="403 Forbidden" class="error-image" onerror="this.style.display='none';">
        
        <h1 class="text-3xl font-bold mb-4">403 - Access Forbidden</h1>
        <p class="mb-6">You don't have permission to access this resource. <a href="index.php" class="underline">Return to login</a> or contact the administrator.</p>
        
<a href="index.php" class="btn-back">Back to Secure Login</a>
    </div>
</body>
</html>