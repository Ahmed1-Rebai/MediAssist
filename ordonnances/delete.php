<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_login();

if (!isset($_GET['id'])) {
    header("Location: view.php");
    exit();
}

// Get prescription details
$stmt = $pdo->prepare("SELECT * FROM ordonnances WHERE id = ? AND user_id = ?");
$stmt->execute([$_GET['id'], $_SESSION['user_id']]);
$ordonnance = $stmt->fetch();

if (!$ordonnance) {
    header("Location: view.php");
    exit();
}

try {
    // Delete file from server
    $file_path = UPLOAD_DIR . $ordonnance['chemin'];
    if (file_exists($file_path)) {
        unlink($file_path);
    }
    
    // Delete record from database
    $stmt = $pdo->prepare("DELETE FROM ordonnances WHERE id = ? AND user_id = ?");
    $stmt->execute([$_GET['id'], $_SESSION['user_id']]);
    
    $_SESSION['success_message'] = "Ordonnance supprimée avec succès";
} catch (PDOException $e) {
    $_SESSION['error_message'] = "Erreur lors de la suppression: " . $e->getMessage();
}

header("Location: view.php");
exit();
?>