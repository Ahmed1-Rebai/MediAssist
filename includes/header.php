<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set default theme
$theme = 'light';

// Fetch user's theme from DB if logged in
if (isset($_SESSION['user_id'])) {
    require_once 'config.php'; // Make sure $pdo is defined
    $stmt = $pdo->prepare("SELECT theme FROM user_settings WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $settings = $stmt->fetch();
    $theme = $settings['theme'] ?? 'light';
}
?>
<!DOCTYPE html>
<html lang="fr" data-theme="<?= htmlspecialchars($theme) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? e($page_title) : APP_NAME ?></title>
    
    
    <!-- CSS -->
    <link rel="stylesheet" href="<?= BASE_URL; ?>/assets/css/style.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- JS -->
    <script src="/assets/js/main.js" defer></script>

    <link rel="icon" href="<?= BASE_URL; ?>/assets/images/favicon.ico" type="image/jpg">


    <!-- Page-specific JS -->
    <?php 
    $page_scripts = [
        'login.php' => 'auth.js',
        'register.php' => 'auth.js',
        'dashboard.php' => 'dashboard.js',
        'settings.php' => 'settings.js'
    ];

    $current_page = basename($_SERVER['PHP_SELF']);
    if (isset($page_scripts[$current_page])): ?>
        <script src="/assets/js/<?= $page_scripts[$current_page] ?>" defer></script>
    <?php endif; ?>
</head>
