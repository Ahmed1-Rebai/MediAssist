<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_login();

$page_title = "Mes Ordonnances";

// Get all prescriptions for current user
$stmt = $pdo->prepare("SELECT * FROM ordonnances WHERE user_id = ? ORDER BY date_upload DESC");
$stmt->execute([$_SESSION['user_id']]);
$ordonnances = $stmt->fetchAll();

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Mes Ordonnances</h1>
        <a href="upload.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Ajouter une ordonnance
        </a>
    </div>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success_message'] ?></div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (empty($ordonnances)): ?>
        <div class="alert alert-info">
            Vous n'avez aucune ordonnance enregistrée.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Fichier</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ordonnances as $ordonnance): 
                        $file_ext = pathinfo($ordonnance['chemin'], PATHINFO_EXTENSION);
                        $is_pdf = strtolower($file_ext) === 'pdf';
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($ordonnance['titre']) ?></td>
                            <td>
                            <?php if (in_array($file_ext, ['pdf', 'jpg','jpeg', 'png', 'gif'])): ?>
                                <a href="<?= BASE_URL ?>/ordonnances/viewer.php?id=<?= $ordonnance['id'] ?>" 
                                    class="btn btn-sm btn-info" target="_blank">
                                    <i class="fas fa-eye"></i> Visualiser
                                </a>
                            <?php endif; ?>
                                
                                <a href="<?= BASE_URL ?>/ordonnances/download.php?id=<?= $ordonnance['id'] ?>" 
                                   class="btn btn-sm btn-primary">
                                    <i class="fas fa-download"></i> Télécharger
                                </a>
                                
                                <small class="text-muted d-block mt-1">
                                    <?= htmlspecialchars($ordonnance['nom_fichier']) ?>
                                </small>
                            </td>
                            <td><?= date('d/m/Y H:i', strtotime($ordonnance['date_upload'])) ?></td>
                            <td>
                                <a href="<?= BASE_URL ?>/ordonnances/delete.php?id=<?= $ordonnance['id'] ?>" 
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette ordonnance?');">
                                    <i class="fas fa-trash"></i> Supprimer
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>