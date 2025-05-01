<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/header.php';

if (!is_logged_in()) {
    redirect_with_message('login.php', 'Veuillez vous connecter', 'danger');
}
include 'navbar.php';

$user_id = $_SESSION['user_id'];
$success = $error = '';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $notifications = isset($_POST['notifications']) ? 1 : 0;
    $theme = $_POST['theme'] ?? 'light';
    
    try {
        $stmt = $pdo->prepare("UPDATE user_settings SET notifications = ?, theme = ? WHERE user_id = ?");
        $stmt->execute([$notifications, $theme, $user_id]);
        
        // Mettre à jour la session immédiatement
        $_SESSION['user_theme'] = $theme;
        
        // Rafraîchir la page
        header("Location: settings.php");
        exit();
    } catch (PDOException $e) {
        $error = "Erreur lors de la mise à jour: " . $e->getMessage();
    }
}

// Récupérer les paramètres
$stmt = $pdo->prepare("SELECT * FROM user_settings WHERE user_id = ?");
$stmt->execute([$user_id]);
$settings = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$settings) {
    // Créer des paramètres par défaut si inexistants
    $pdo->prepare("INSERT INTO user_settings (user_id, notifications, theme) VALUES (?, 1, 'light')")->execute([$user_id]);
    $settings = ['notifications' => 1, 'theme' => 'light'];
}

$page_title = "Paramètres";
?>

<div class="container">
    <div class="settings-section">
        <h1><i class="fas fa-cog"></i> Paramètres</h1>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="settings-card">
                <h2><i class="fas fa-bell"></i> Notifications</h2>
                
                <div class="form-group form-check">
                    <input type="checkbox" class="form-check-input" id="notifications" name="notifications" <?php echo $settings['notifications'] ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="notifications">Activer les notifications</label>
                </div>
            </div>
            
            <div class="settings-card">
                <h2><i class="fas fa-palette"></i> Apparence</h2>
                
                <div class="form-group">
                    <label>Thème :</label>
                    <select class="form-control" name="theme">
                        <option value="light" <?php echo $settings['theme'] === 'light' ? 'selected' : ''; ?>>Clair</option>
                        <option value="dark" <?php echo $settings['theme'] === 'dark' ? 'selected' : ''; ?>>Sombre</option>
                    </select>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i>&nbsp;Enregistrer les modifications
            </button>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>