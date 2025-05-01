<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/header.php';


if (!is_logged_in()) {
    redirect_with_message('login.php', 'Veuillez vous connecter', 'danger');
}

include 'navbar.php';

$user_id = $_SESSION['user_id'];
update_last_login($user_id);
$user = get_user_profile($user_id);

if (!$user) {
    die("Utilisateur non trouvé");
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_info'])) {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        
        // Validate inputs
        if (empty($username)) {
            $errors['username'] = "Le nom d'utilisateur est requis";
        }
        
        if (empty($email)) {
            $errors['email'] = "L'email est requis";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "L'email n'est pas valide";
        }
        
        if (empty($errors)) {
            if (update_user_profile($user_id, $username, $email)) {
                $success = "Profil mis à jour avec succès";
                // Refresh user data
                $user = get_user_profile($user_id);
            } else {
                $errors['general'] = "Erreur lors de la mise à jour du profil";
            }
        }
    } elseif (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        if (empty($current_password)) {
            $errors['current_password'] = "Le mot de passe actuel est requis";
        }
        
        if (empty($new_password)) {
            $errors['new_password'] = "Le nouveau mot de passe est requis";
        } elseif (strlen($new_password) < 8) {
            $errors['new_password'] = "Le mot de passe doit contenir au moins 8 caractères";
        }
        
        if ($new_password !== $confirm_password) {
            $errors['confirm_password'] = "Les mots de passe ne correspondent pas";
        }
        
        if (empty($errors)) {
            if (change_user_password($user_id, $current_password, $new_password)) {
                $success = "Mot de passe changé avec succès";
            } else {
                $errors['general'] = "Mot de passe actuel incorrect";
            }
        }
    }
}

$page_title = "Mon Profil";
?>

<div class="container">
    <div class="profile-section">
        <h1><i class="fas fa-user-circle"></i> Mon Profil</h1>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($errors['general'])): ?>
            <div class="alert alert-danger"><?php echo $errors['general']; ?></div>
        <?php endif; ?>
        
        <div class="profile-grid">
            <!-- Profile Information Section -->
            <div class="profile-card">
                <div class="card-header">
                    <h2><i class="fas fa-info-circle"></i> Informations Personnelles</h2>
                    <button class="btn btn-outline-primary btn-sm" id="edit-info-btn">
                        <i class="fas fa-edit"></i> Modifier
                    </button>
                </div>
                
                <div class="card-body">
                    <div id="info-display">
                        <div class="info-item">
                            <label>Nom d'utilisateur :</label>
                            <span><?php echo htmlspecialchars($user['username']); ?></span>
                        </div>
                        
                        <div class="info-item">
                            <label>Email :</label>
                            <span><?php echo htmlspecialchars($user['email']); ?></span>
                        </div>
                        
                        <div class="info-item">
                            <label>Date d'inscription :</label>
                            <span><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></span>
                        </div>
                        
                        <div class="info-item">
                            <label>Dernière connexion :</label>
                            <span><?php echo $user['last_login'] ? date('d/m/Y H:i', strtotime($user['last_login'])) : 'Jamais'; ?></span>
                        </div>
                    </div>
                    
                    <form id="info-form" method="POST" style="display: none;">
                        <input type="hidden" name="update_info" value="1">
                        
                        <div class="form-group">
                            <label for="username">Nom d'utilisateur</label>
                            <input type="text" class="form-control" id="username" name="username" 
                                   value="<?php echo htmlspecialchars($user['username']); ?>">
                            <?php if (isset($errors['username'])): ?>
                                <div class="error"><?php echo $errors['username']; ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($user['email']); ?>">
                            <?php if (isset($errors['email'])): ?>
                                <div class="error"><?php echo $errors['email']; ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                        <button type="button" class="btn btn-secondary cancel-btn">Annuler</button>
                    </form>
                </div>
            </div>
            
            <!-- Password Section -->
            <div class="profile-card">
                <div class="card-header">
                    <h2><i class="fas fa-shield-alt"></i> Sécurité</h2>
                    <button class="btn btn-primary" id="edit-password-btn">
                        <i class="fas fa-key"></i> Changer le mot de passe
                    </button>
                </div>
                
                <div class="card-body">
                    <div id="password-display">
                        <div class="info-item">
                            <p>Cliquez sur "Changer le mot de passe" pour modifier votre mot de passe</p>
                        </div>
                    </div>
                    <form id="password-form" method="POST" style="display: none;">
                        <input type="hidden" name="change_password" value="1">
                        
                        <div class="form-group">
                            <label for="current_password">Mot de passe actuel</label>
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                            <?php if (isset($errors['current_password'])): ?>
                                <div class="error"><?php echo $errors['current_password']; ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="new_password">Nouveau mot de passe</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                            <small class="form-text text-muted">Minimum 8 caractères</small>
                            <?php if (isset($errors['new_password'])): ?>
                                <div class="error"><?php echo $errors['new_password']; ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">Confirmer le mot de passe</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            <?php if (isset($errors['confirm_password'])): ?>
                                <div class="error"><?php echo $errors['confirm_password']; ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Changer le mot de passe</button>
                        <button type="button" class="btn btn-secondary" id="cancel-edit-password">Annuler</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo BASE_URL; ?>/assets/js/profile.js"></script>

<?php include 'footer.php'; ?>