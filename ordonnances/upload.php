<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_login();

$page_title = "Ajouter une Ordonnance";
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre']);
    
    // File upload handling
    if (isset($_FILES['ordonnance']) && $_FILES['ordonnance']['error'] === UPLOAD_ERR_OK) {
        $file_name = $_FILES['ordonnance']['name'];
        $file_tmp = $_FILES['ordonnance']['tmp_name'];
        $file_size = $_FILES['ordonnance']['size'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        $allowed_ext = ['jpg', 'jpeg', 'png', 'pdf'];
        
        if (!in_array($file_ext, $allowed_ext)) {
            $errors['ordonnance'] = "Format de fichier non supporté. Formats acceptés: JPG, PNG, PDF";
        } elseif ($file_size > 5000000) { // 5MB max
            $errors['ordonnance'] = "Le fichier est trop volumineux (max 5MB)";
        } else {
            $new_file_name = uniqid('ordonnance_', true) . '.' . $file_ext;
            $destination = UPLOAD_DIR . $new_file_name;
            
            if (move_uploaded_file($file_tmp, $destination)) {
                try {
                    $stmt = $pdo->prepare("INSERT INTO ordonnances 
                                          (user_id, nom_fichier, chemin, titre) 
                                          VALUES (?, ?, ?, ?)");
                    $stmt->execute([
                        $_SESSION['user_id'],
                        $file_name,
                        $new_file_name,
                        $titre ?: 'Ordonnance ' . date('d/m/Y')
                    ]);
                    $success = true;
                } catch (PDOException $e) {
                    $errors['database'] = "Erreur lors de l'enregistrement: " . $e->getMessage();
                    // Delete the uploaded file if DB insert failed
                    if (file_exists($destination)) {
                        unlink($destination);
                    }
                }
            } else {
                $errors['ordonnance'] = "Erreur lors du téléversement du fichier";
            }
        }
    } else {
        $errors['ordonnance'] = "Veuillez sélectionner un fichier";
    }
}

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container">
    <h1>Ajouter une Ordonnance</h1>
    
    <?php if ($success): ?>
        <div class="alert alert-success">
            Ordonnance ajoutée avec succès! <a href="view.php">Voir mes ordonnances</a>
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
        
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="titre">Titre (optionnel)</label>
                <input type="text" id="titre" name="titre" class="form-control"
                       placeholder="Ex: Ordonnance Dr. Dupont - 15/01/2023">
            </div>
            
            <div class="form-group">
                <label for="ordonnance">Fichier d'Ordonnance</label>
                <div class="custom-file">
                    <input type="file" class="custom-file-input" id="ordonnance" name="ordonnance" required>
                    <label class="custom-file-label" for="ordonnance">Choisir un fichier (JPG, PNG ou PDF)</label>
                </div>
                <small class="form-text text-muted">Taille maximale: 5MB</small>
            </div>
            
            <button type="submit" class="btn btn-primary">Enregistrer</button>
            <a href="view.php" class="btn btn-secondary">Annuler</a>
        </form>
    <?php endif; ?>
</div>

<script>
// Update file input label with selected file name
document.querySelector('.custom-file-input').addEventListener('change', function(e) {
    const fileName = e.target.files[0].name;
    const nextSibling = e.target.nextElementSibling;
    nextSibling.innerText = fileName;
});
</script>

<?php include '../includes/footer.php'; ?>
