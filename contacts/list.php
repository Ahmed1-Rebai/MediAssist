<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_login();

$page_title = "Mes Contacts d'Urgence";

// Get user's emergency contacts
$stmt = $pdo->prepare("SELECT * FROM urgence_contacts WHERE user_id = ? ORDER BY nom ASC");
$stmt->execute([$_SESSION['user_id']]);
$contacts = $stmt->fetchAll();

include '../includes/header.php';
include '../includes/navbar.php';

?>

<div class="container">
    <div class="page-header">
        <h1>Mes Contacts d'Urgence</h1>
        <a href="add.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Ajouter
        </a>
    </div>
    
    <?php if (empty($contacts)): ?>
        <div class="alert alert-info">
            Aucun contact enregistré. <a href="add.php">Ajoutez-en un</a>.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Téléphone</th>
                        <th>Relation</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($contacts as $contact): ?>
                        <tr>
                            <td><?php echo e($contact['nom']); ?></td>
                            <td>
                                <a href="tel:+216<?php echo e($contact['telephone']); ?>">
                                    +216<?php echo e($contact['telephone']); ?>
                                </a>
                            </td>
                            <td><?php echo e($contact['relation']); ?></td>
                            <td>
                                <a href="tel:+216<?php echo e($contact['telephone']); ?>" 
                                   class="btn btn-sm btn-success">
                                    <i class="fas fa-phone"></i> Appeler
                                </a>
                                <a href="edit.php?id=<?php echo $contact['id']; ?>" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="delete.php?id=<?php echo $contact['id']; ?>" 
                                   class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('Supprimer ce contact?')">
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