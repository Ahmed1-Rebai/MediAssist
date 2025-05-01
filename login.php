<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Redirection si déjà connecté
if (is_logged_in()) {
    header('Location: dashboard.php');
    exit();
}

$page_title = "Connexion - MediAssist";
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Veuillez remplir tous les champs";
    } else {
        $user = authenticate_user($email, $password);
        if ($user) {
            if (!$user['is_verified']) {
                $error = "Votre compte n'est pas encore vérifié. Veuillez vérifier vos emails.";
            } else {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                header('Location: dashboard.php');
                exit();
            }
        } else {
            $error = "Email ou mot de passe incorrect";
        }
    }
}

include 'includes/header.php';
?>

<div class="page-wrapper auth-page">
    <!-- Éléments d'arrière-plan dynamiques -->
    <div class="bubbles-container">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
    </div>

    <header class="auth-header">
        <div class="container">
            <div class="logo">
                <a href="<?= BASE_URL ?>/index.php">
                    <i class="fas fa-stethoscope"></i> MediAssist
                </a>
            </div>
        </div>
    </header>

    <main class="auth-main">
        <div class="auth-container">
            <h1>Connexion</h1>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" action="login.php" class="auth-form">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required class="form-control">
                </div>

                <div class="form-group password-container">
                    <label for="password">Mot de passe</label>
                    <div class="input-group">
                        <input type="password" id="password" name="password" required class="form-control"
                            placeholder="Entrez votre mot de passe">
                        <div class="input-group-append">
                            <span class="input-group-text toggle-password">
                                <i class="fas fa-eye" id="togglePassword"></i>
                            </span>
                        </div>
                    </div>
                    <small class="form-text text-muted">
                        <a href="forgot-password.php">Mot de passe oublié?</a>
                    </small>
                </div>

                <button type="submit" class="btn btn-login btn-block">Se connecter</button>
            </form>

            <div class="auth-links">
                <p>Pas encore de compte? <a href="register.php">S'inscrire</a></p>
            </div>
        </div>
    </main>
</div>

<?php include 'includes/footer.php'; ?>
<script src="assets/js/bubbles.js"></script>
<script>
    // Script pour basculer la visibilité du mot de passe
    document.getElementById('togglePassword').addEventListener('click', function () {
        const passwordInput = document.getElementById('password');
        const icon = this;

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });
</script>