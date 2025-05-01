<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_login();

$page_title = "Ajouter un Contact d'Urgence";
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $telephone = trim($_POST['telephone']);
    $relation = trim($_POST['relation']);
    
    // Validation
    if (empty($nom)) {
        $errors['nom'] = "Le nom est requis";
    }
    
    if (empty($telephone)) {
        $errors['telephone'] = "Le numéro de téléphone est requis";
    } else {
        // Nettoyage du numéro (supprime tout ce qui n'est pas chiffre)
        $cleaned_phone = preg_replace('/[^0-9]/', '', $telephone);
        
        // Garde seulement les 8 derniers chiffres
        $cleaned_phone = substr($cleaned_phone, -8);
        
        // Validation du format (exactement 8 chiffres commençant par 2,4,5 ou 9)
        if (!preg_match('/^[2459][0-9]{7}$/', $cleaned_phone)) {
            $errors['telephone'] = "Numéro tunisien invalide (8 chiffres commençant par 2,4,5 ou 9)";
        } else {
            $telephone = $cleaned_phone; // On conserve seulement les 8 chiffres
        }
    }
    
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO urgence_contacts 
                                  (user_id, nom, telephone, relation) 
                                  VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $_SESSION['user_id'],
                $nom,
                $telephone,
                $relation
            ]);
            $success = true;
        } catch (PDOException $e) {
            $errors['database'] = "Erreur lors de l'ajout: " . $e->getMessage();
        }
    }
}

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container">
    <h1>Ajouter un Contact d'Urgence</h1>
    
    <?php if ($success): ?>
        <div class="alert alert-success">
            Contact ajouté avec succès! <a href="list.php">Voir la liste</a>
        </div>
    <?php else: ?>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="nom">Nom Complet</label>
                <input type="text" id="nom" name="nom" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="telephone">Numéro de Téléphone Tunisien</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">+216</span>
                    </div>
                    <input type="tel" id="telephone" name="telephone" class="form-control" required
                           placeholder="12345678" maxlength="8" pattern="[2459][0-9]{7}">
                </div>
                <small class="form-text text-muted">8 chiffres commençant par 2, 4, 5 ou 9</small>
            </div>
            
            <div class="form-group">
                <label for="relation">Relation</label>
                <input type="text" id="relation" name="relation" class="form-control"
                       placeholder="Ex: Médecin traitant, Conjoint, Parent, etc.">
            </div>
            
            <button type="submit" class="btn btn-primary">Enregistrer</button>
            <a href="list.php" class="btn btn-secondary">Annuler</a>
        </form>
    <?php endif; ?>
</div>

<script src="/assets/js/phone-format.js" defer></script>


<?php include '../includes/footer.php'; ?>