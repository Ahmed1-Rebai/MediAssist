<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$page_title = "Vérification d'email - MediAssist";

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $token_hash = hash('sha256', $token);
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE verification_token = ? AND token_expires_at > NOW()");
        $stmt->execute([$token_hash]);
        $user = $stmt->fetch();
        
        if ($user) {
            // Mark user as verified and clear token
            $stmt = $pdo->prepare("UPDATE users SET is_verified = TRUE, verification_token = NULL, token_expires_at = NULL WHERE id = ?");
            $stmt->execute([$user['id']]);
            
            $success = "Votre email a été vérifié avec succès! Vous pouvez maintenant vous connecter.";
        } else {
            $error = "Lien de vérification invalide ou expiré.";
        }
    } catch (PDOException $e) {
        $error = "Une erreur s'est produite lors de la vérification.";
        error_log("Verification error: " . $e->getMessage());
    }
} else {
    $error = "Aucun token de vérification fourni.";
}

include 'includes/header.php';
?>

<div class="page-wrapper auth-page">
    <div class="auth-container">
        <h1>Vérification d'email</h1>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($success); ?> <a href="login.php">Se connecter</a>
            </div>
        <?php elseif (isset($error)): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>