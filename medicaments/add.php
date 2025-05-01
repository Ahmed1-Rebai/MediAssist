<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

require_login();

$page_title = "Ajouter un Médicament";
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $posologie = trim($_POST['posologie']);
    $frequence = trim($_POST['frequence']);
    $heures = $_POST['heures'] ?? [];

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
        $errors['heures'] = "Au moins une heure de prise est requise";
    } else {
        // Valider chaque heure
        foreach ($heures as $heure) {
            if (empty($heure)) {
                $errors['heures'] = "Toutes les heures doivent être renseignées";
                break;
            }
        }
    }
    
    if (empty($errors)) {
        try {
            // Convertir le tableau d'heures en JSON
            $heures_json = json_encode($heures);
            
            $stmt = $pdo->prepare("INSERT INTO medicaments 
                                  (user_id, nom, posologie, frequence, heures) 
                                  VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $_SESSION['user_id'], 
                $nom, 
                $posologie, 
                $frequence, 
                $heures_json
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
    <h1 class="my-4">Ajouter un Médicament</h1>
    
    <?php if ($success): ?>
        <div class="alert alert-success">
            Médicament ajouté avec succès! <a href="list.php" class="alert-link">Voir la liste</a>
        </div>
    <?php else: ?>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form method="POST" class="mt-4">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="nom" class="form-label">Nom du Médicament</label>
                        <input type="text" id="nom" name="nom" class="form-control" 
                               value="<?php echo htmlspecialchars($_POST['nom'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="posologie" class="form-label">Posologie</label>
                        <input type="text" id="posologie" name="posologie" class="form-control"
                               value="<?php echo htmlspecialchars($_POST['posologie'] ?? ''); ?>" required
                               placeholder="Ex: 1 comprimé, 2 gouttes, etc.">
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="frequence" class="form-label">Fréquence</label>
                        <select id="frequence" name="frequence" class="form-control" required
                                onchange="updateHeureFields()">
                            <option value="">Sélectionner...</option>
                            <option value="1" <?php echo ($_POST['frequence'] ?? '') === '1' ? 'selected' : ''; ?>>1 fois par jour</option>
                            <option value="2" <?php echo ($_POST['frequence'] ?? '') === '2' ? 'selected' : ''; ?>>2 fois par jour</option>
                            <option value="3" <?php echo ($_POST['frequence'] ?? '') === '3' ? 'selected' : ''; ?>>3 fois par jour</option>
                            <option value="autre" <?php echo ($_POST['frequence'] ?? '') === 'autre' ? 'selected' : ''; ?>>Autre fréquence</option>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div id="heures-container" class="mb-3">
                        <?php if (!empty($_POST['heures'])): ?>
                            <?php foreach ($_POST['heures'] as $index => $heure): ?>
                                <div class="form-group mb-2 heure-field">
                                    <label>Heure de prise <?php echo $index + 1; ?></label>
                                    <div class="input-group">
                                        <input type="time" name="heures[]" class="form-control" 
                                               value="<?php echo htmlspecialchars($heure); ?>" required>
                                        <?php if ($index > 0): ?>
                                            <button type="button" class="btn btn-outline-danger" 
                                                    onclick="this.closest('.heure-field').remove(); updateLabels()">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="form-group mb-2 heure-field">
                                <label>Heure de prise 1</label>
                                <input type="time" name="heures[]" class="form-control" required>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <button type="button" class="btn btn-outline-primary" onclick="addHeureField()">
                            <i class="fas fa-plus"></i> Ajouter une heure
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="mt-4">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-save"></i> Enregistrer
                </button>
                <a href="list.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Annuler
                </a>
            </div>
        </form>
    <?php endif; ?>
</div>

<script>
function updateHeureFields() {
    const frequence = document.getElementById('frequence').value;
    const container = document.getElementById('heures-container');
    container.innerHTML = '';
    
    if (frequence === 'autre') {
        // Permet d'ajouter des heures manuellement
        container.innerHTML = `
            <div class="form-group mb-2 heure-field">
                <label>Heure de prise 1</label>
                <input type="time" name="heures[]" class="form-control" required>
            </div>
        `;
        document.querySelector('.btn-outline-primary').style.display = 'block';
    } else if (frequence && frequence >= 1) {
        // Ajoute un champ pour chaque prise
        for (let i = 0; i < parseInt(frequence); i++) {
            container.innerHTML += `
                <div class="form-group mb-2 heure-field">
                    <label>Heure de prise ${i+1}</label>
                    <input type="time" name="heures[]" class="form-control" required>
                </div>
            `;
        }
        document.querySelector('.btn-outline-primary').style.display = 'none';
    }
}

function addHeureField() {
    const container = document.getElementById('heures-container');
    const heureFields = container.querySelectorAll('.heure-field');
    const newIndex = heureFields.length + 1;
    
    const newField = document.createElement('div');
    newField.className = 'form-group mb-2 heure-field';
    newField.innerHTML = `
        <label>Heure de prise ${newIndex}</label>
        <div class="input-group">
            <input type="time" name="heures[]" class="form-control" required>
            <button type="button" class="btn btn-outline-danger" 
                    onclick="this.closest('.heure-field').remove(); updateLabels()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    container.appendChild(newField);
}

function updateLabels() {
    const heureFields = document.querySelectorAll('.heure-field');
    heureFields.forEach((field, index) => {
        field.querySelector('label').textContent = `Heure de prise ${index + 1}`;
    });
}

// Initialiser les champs selon la fréquence sélectionnée
document.addEventListener('DOMContentLoaded', function() {
    const frequence = document.getElementById('frequence').value;
    if (frequence) {
        updateHeureFields();
    }
});
</script>

<?php include '../includes/footer.php'; ?>