<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

require_login();

// Check if ID is provided
if (!isset($_GET['id'])) {
    header("Location: list.php");
    exit();
}

$page_title = "Modifier Médicament";
$errors = [];
$success = false;

// Get medication details
$stmt = $pdo->prepare("SELECT * FROM medicaments WHERE id = ? AND user_id = ?");
$stmt->execute([$_GET['id'], $_SESSION['user_id']]);
$medicament = $stmt->fetch();

if (!$medicament) {
    header("Location: list.php");
    exit();
}

// Decode the 'heures' field (it is stored as a JSON array)
$heures = json_decode($medicament['heures'], true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $posologie = trim($_POST['posologie']);
    $frequence = trim($_POST['frequence']);
    
    // Sanitize and process the hours input
    $heures_post = $_POST['heures'];
    $heures = array_map('trim', explode(',', $heures_post)); // Convert comma-separated values into an array
    
    // Validation
    if (empty($nom)) {
        $errors['nom'] = "Le nom du médicament est requis";
    }
    
    if (empty($posologie)) {
        $errors['posologie'] = "La posologie est requise";
    }
    
    if (empty($frequence)) {
        $errors['frequence'] = "La fréquence est requise";
    }
    
    if (empty($heures)) {
        $errors['heures'] = "Les heures de prise sont requises";
    }
    
    if (empty($errors)) {
        try {
            // Encode hours back into JSON
            $heures_json = json_encode($heures);
            
            $stmt = $pdo->prepare("UPDATE medicaments SET nom = ?, posologie = ?, frequence = ?, heures = ? 
                                  WHERE id = ? AND user_id = ?");
            $stmt->execute([$nom, $posologie, $frequence, $heures_json, $_GET['id'], $_SESSION['user_id']]);
            $success = true;
            $medicament = array_merge($medicament, $_POST); // Update displayed values
        } catch (PDOException $e) {
            $errors['database'] = "Erreur lors de la mise à jour: " . $e->getMessage();
        }
    }
}

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container">
    <h1>Modifier Médicament</h1>
    
    <?php if ($success): ?>
        <div class="alert alert-success">
            Médicament mis à jour avec succès! <a href="list.php">Voir la liste</a>
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
                <label for="nom">Nom du Médicament</label>
                <input type="text" id="nom" name="nom" class="form-control" 
                       value="<?php echo e($medicament['nom']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="posologie">Posologie</label>
                <input type="text" id="posologie" name="posologie" class="form-control"
                       value="<?php echo e($medicament['posologie']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="frequence">Fréquence</label>
                <select id="frequence" name="frequence" class="form-control" required>
                    <option value="Quotidien" <?php echo $medicament['frequence'] === 'Quotidien' ? 'selected' : ''; ?>>Quotidien</option>
                    <option value="Hebdomadaire" <?php echo $medicament['frequence'] === 'Hebdomadaire' ? 'selected' : ''; ?>>Hebdomadaire</option>
                    <option value="Mensuel" <?php echo $medicament['frequence'] === 'Mensuel' ? 'selected' : ''; ?>>Mensuel</option>
                    <option value="Au besoin" <?php echo $medicament['frequence'] === 'Au besoin' ? 'selected' : ''; ?>>Au besoin</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="heures">Heures de Prise (séparées par des virgules)</label>
                <input type="text" id="heures" name="heures" class="form-control"
                       value="<?php echo implode(', ', $heures); ?>" required>
            </div>
            
            <button type="submit" class="btn btn-primary">Mettre à jour</button>
            <a href="list.php" class="btn btn-secondary">Annuler</a>
        </form>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
