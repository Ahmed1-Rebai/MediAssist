<?php

// Database configuration
define('DB_HOST', 'sql307.infinityfree.com');
define('DB_USER', 'if0_38793913');
define('DB_PASS', 'QuSnE5IMdcxYZFU');
define('DB_NAME', 'if0_38793913_mediassist');

// Establish database connection
try {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Application settings
define('APP_NAME', 'MediAssist');
define('BASE_URL', 'https://mediassist.kesug.com');
define('UPLOAD_DIR', __DIR__.'/ordonnances/uploads/');
// Create uploads directory if it doesn't exist
if (!file_exists(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
}
	
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}




