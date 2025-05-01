<?php
require_once 'config.php';
require_once 'functions.php';

// Protection contre les attaquesCSRF 
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validate_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// validation de robustesse du mot de passe
function is_password_strong($password) {
    return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password);
}

// nettoyer les données fournies par l'utilisateur pour éviter les injections XSS et autres attaques
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Vérifier si utlisateur a permission d'accéder à certaine fonctionnalité
function check_permission($required_role = 'user') {
    if (!isset($_SESSION['role'])) {
        return false;
    }
    return $_SESSION['role'] === $required_role;
}
