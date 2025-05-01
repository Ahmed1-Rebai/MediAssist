<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

require_login();

$page_title = "Mes Médicaments";

// Get user's medications
$stmt = $pdo->prepare("SELECT * FROM medicaments WHERE user_id = ? ORDER BY nom ASC");
$stmt->execute([$_SESSION['user_id']]);
$medicaments = $stmt->fetchAll();

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container">
    <div class="page-header">
        <h1>Mes Médicaments</h1>
        <a href="add.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Ajouter
        </a>
    </div>
    
    <?php if (empty($medicaments)): ?>
        <div class="alert alert-info">
            Aucun médicament enregistré. <a href="add.php">Ajoutez-en un</a> .
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Posologie</th>
                        <th>Fréquence</th>
                        <th>Heures</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($medicaments as $med): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($med['nom']); ?></td>
                            <td><?php echo htmlspecialchars($med['posologie']); ?></td>
                            <td><?php echo htmlspecialchars($med['frequence']); ?></td>
                            <td>
                                <?php 
                                // Décoder les heures stockées en JSON et les afficher
                                $heures = json_decode($med['heures']);
                                if (!empty($heures)) {
                                    echo implode(', ', $heures); // Afficher les heures séparées par une virgule
                                } else {
                                    echo 'Non spécifiée';
                                }
                                ?>
                            </td>
                            <td>
                                <a href="edit.php?id=<?php echo $med['id']; ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="delete.php?id=<?php echo $med['id']; ?>" class="btn btn-sm btn-outline-danger" 
                                   onclick="return confirm('Supprimer ce médicament?')">
                                    <i class="fas fa-trash"></i>
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
