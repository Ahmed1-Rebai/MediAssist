<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Redirection si déjà connecté
if (is_logged_in()) {
    header('Location: dashboard.php');
    exit();
}

$page_title = "Inscription - MediAssist";
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validation
    if (empty($username)) {
        $errors['username'] = "Le nom d'utilisateur est requis";
    }

    if (empty($email)) {
        $errors['email'] = "L'email est requis";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Email invalide";
    } elseif (email_exists($email)) {
        $errors['email'] = "Cet email est déjà utilisé";
    }

    if (empty($password)) {
        $errors['password'] = "Le mot de passe est requis";
    } elseif (strlen($password) < 8) {
        $errors['password'] = "Le mot de passe doit contenir au moins 8 caractères";
    }

    if ($password !== $confirm_password) {
        $errors['confirm_password'] = "Les mots de passe ne correspondent pas";
    }

    // Si aucune erreur, procéder à l'inscription
    if (empty($errors)) {
        if (register_user($username, $email, $password)) {
            $success = true;
        } else {
            $errors['general'] = "Une erreur s'est produite lors de l'inscription";
        }
    }
}

include 'includes/header.php';
?>

<div class="page-wrapper auth-page">
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
            <h1>Inscription</h1>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    Inscription réussie! Un email de vérification a été envoyé à <?php echo htmlspecialchars($email); ?>.
                    Veuillez vérifier votre boîte mail pour activer votre compte.
                </div>
            <?php else: ?>
                <?php if (isset($errors['general'])): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($errors['general']); ?></div>
                <?php endif; ?>

                <form method="POST" action="register.php" class="auth-form">
                    <div class="form-group">
                        <label for="username">Nom d'utilisateur</label>
                        <input type="text" id="username" name="username"
                            value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>" required
                            class="form-control">
                        <?php if (isset($errors['username'])): ?>
                            <small class="text-danger"><?php echo htmlspecialchars($errors['username']); ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email"
                            value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required
                            class="form-control">
                        <?php if (isset($errors['email'])): ?>
                            <small class="text-danger"><?php echo htmlspecialchars($errors['email']); ?></small>
                        <?php endif; ?>
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

                        <?php if (isset($errors['password'])): ?>
                            <small class="text-danger"><?php echo htmlspecialchars($errors['password']); ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirmer le mot de passe</label>
                        <input type="password" id="confirm_password" name="confirm_password" required class="form-control"
                            placeholder="Confirmez votre mot de passe">
                        <?php if (isset($errors['confirm_password'])): ?>
                            <small class="text-danger"><?php echo htmlspecialchars($errors['confirm_password']); ?></small>
                        <?php endif; ?>
                    </div>



                    <button type="submit" class="btn btn-register btn-block">S'inscrire</button>
                </form>

                <div class="auth-links">
                    <p>Déjà inscrit? <a href="login.php">Se connecter</a></p>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php include 'includes/footer.php'; ?>
<script src="assets/js/bubbles.js"></script>
<script>
    // Script pour basculer la visibilité des mots de passe
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