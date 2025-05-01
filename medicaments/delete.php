<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

require_login();

// Check if ID is provided
if (!isset($_GET['id'])) {
    header("Location: list.php");
    exit();
}

// Delete medication
try {
    $stmt = $pdo->prepare("DELETE FROM medicaments WHERE id = ? AND user_id = ?");
    $stmt->execute([$_GET['id'], $_SESSION['user_id']]);
    
    $_SESSION['success_message'] = "Médicament supprimé avec succès";
} catch (PDOException $e) {
    $_SESSION['error_message'] = "Erreur lors de la suppression: " . $e->getMessage();
}

header("Location: list.php");
exit();
?>
