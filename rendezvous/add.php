<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_login();

$page_title = "Ajouter un Rendez-vous";
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type_consultation = trim($_POST['type_consultation']);
    $date = $_POST['date'];
    $heure = $_POST['heure'];
    $notes = trim($_POST['notes']);
    $medecin = trim($_POST['medecin']);
    $lieu = trim($_POST['lieu']);
    
    // Validation
    if (empty($type_consultation)) {
        $errors['type_consultation'] = "Le type de consultation est requis";
    }
    
    if (empty($date)) {
        $errors['date'] = "La date est requise";
    } elseif (strtotime($date) < strtotime('today')) {
        $errors['date'] = "La date ne peut pas être dans le passé";
    }
    
    if (empty($heure)) {
        $errors['heure'] = "L'heure est requise";
    }
    
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO rendezvous 
                                  (user_id, type_consultation, date, heure, notes, medecin, lieu) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $_SESSION['user_id'],
                $type_consultation,
                $date,
                $heure,
                $notes,
                $medecin,
                $lieu
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
    <h1>Ajouter un Rendez-vous Médical</h1>
    
    <?php if ($success): ?>
        <div class="alert alert-success">
            Rendez-vous ajouté avec succès! <a href="list.php">Voir la liste</a>
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
                <label for="type_consultation">Type de Consultation</label>
                <input type="text" id="type_consultation" name="type_consultation" class="form-control" required>
            </div>
            
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="date">Date</label>
                    <input type="date" id="date" name="date" class="form-control" required
                           min="<?php echo date('Y-m-d'); ?>">
                </div>
                
                <div class="form-group col-md-6">
                    <label for="heure">Heure</label>
                    <input type="time" id="heure" name="heure" class="form-control" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="medecin">Médecin/Spécialiste</label>
                <input type="text" id="medecin" name="medecin" class="form-control">
            </div>
            
            <div class="form-group">
                <label for="lieu">Lieu/Adresse</label>
                <input type="text" id="lieu" name="lieu" class="form-control">
            </div>
            
            <div class="form-group">
                <label for="notes">Notes Complémentaires</label>
                <textarea id="notes" name="notes" class="form-control" rows="3"></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">Enregistrer</button>
            <a href="list.php" class="btn btn-secondary">Annuler</a>
        </form>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>