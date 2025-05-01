<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$page_title = "Contact - MediAssist";
include __DIR__ . '/includes/header.php';

// Form processing
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Data sanitization
    $nom = htmlspecialchars($_POST['nom'] ?? '');
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $message = htmlspecialchars($_POST['message'] ?? '');
    $user_captcha = trim($_POST['captcha'] ?? '');

    // Validation
    if (empty($nom) || empty($email) || empty($message)) {
        $error = "Tous les champs sont obligatoires !";
    } elseif (!validate_text_captcha($user_captcha)) {
        $error = "Réponse incorrecte !";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO contacts (nom, email, message) VALUES (?, ?, ?)");
            $stmt->execute([$nom, $email, $message]);
            $success = true;
            unset($_SESSION['captcha_code']);
        } catch (PDOException $e) {
            $error = "Erreur : " . $e->getMessage();
        }
    }
}
?>

<div class="container py-5">
    <div class="logo">
        <a href="index.php">
            <i class="fas fa-stethoscope"></i> MediAssist
        </a>
    </div>
    <h1 class="mb-4">Contactez-nous</h1>

    <?php if (isset($success)): ?>
        <div class="alert alert-success">Merci ! Votre message a été envoyé.</div>
    <?php elseif (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" class="col-md-6 mx-auto">
        <div class="mb-3">
            <label for="nom" class="form-label">Nom complet</label>
            <input type="text" class="form-control" id="nom" name="nom" required
                value="<?= htmlspecialchars($nom ?? '') ?>">
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" required
                value="<?= htmlspecialchars($email ?? '') ?>">
        </div>

        <div class="mb-3">
            <label for="message" class="form-label">Message</label>
            <textarea class="form-control" id="message" name="message" rows="5"
                required><?= htmlspecialchars($message ?? '') ?></textarea>
        </div>

        <div class="mb-3">
            <label for="captcha" class="form-label">Vérification anti-robot</label>
            <div class="mb-2">
                <?php
                $captcha_question = generate_text_captcha();
                echo "<strong>$captcha_question</strong>";
                ?>
            </div>
            <input type="text" class="form-control" id="captcha" name="captcha" required placeholder="Votre réponse">
        </div>

        <button type="submit" class="btn btn-primary">Envoyer</button>
    </form>
</div>


<?php include __DIR__ . '/includes/footer.php'; ?>