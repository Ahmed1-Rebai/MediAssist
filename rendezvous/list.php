<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_login();

$page_title = "Mes Rendez-vous";

// Get user's appointments
$stmt = $pdo->prepare("SELECT * FROM rendezvous WHERE user_id = ? ORDER BY date, heure ASC");
$stmt->execute([$_SESSION['user_id']]);
$rendezvous = $stmt->fetchAll();

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container">
    <div class="page-header">
        <h1>Mes Rendez-vous</h1>
        <a href="add.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Ajouter
        </a>
        <a href="calendar.php" class="btn btn-outline-primary">
            <i class="fas fa-calendar-alt"></i> Calendrier
        </a>
    </div>
    
    <?php if (empty($rendezvous)): ?>
        <div class="alert alert-info">
            Aucun rendez-vous enregistré. <a href="add.php">Ajoutez-en un</a>.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Heure</th>
                        <th>Type</th>
                        <th>Médecin</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rendezvous as $rdv): ?>
                        <tr class="<?php echo strtotime($rdv['date']) == strtotime('today') ? 'table-warning' : ''; ?>">
                            <td><?php echo format_date($rdv['date']); ?></td>
                            <td><?php echo format_time($rdv['heure']); ?></td>
                            <td><?php echo e($rdv['type_consultation']); ?></td>
                            <td><?php echo e($rdv['medecin']); ?></td>
                            <td>
                                <a href="#" class="btn btn-sm btn-outline-primary" data-toggle="modal" 
                                   data-target="#detailsModal<?php echo $rdv['id']; ?>">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="edit.php?id=<?php echo $rdv['id']; ?>" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="delete.php?id=<?php echo $rdv['id']; ?>" class="btn btn-sm btn-outline-danger" 
                                   onclick="return confirm('Supprimer ce rendez-vous?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        
                        <!-- Details Modal -->
                        <div class="modal fade" id="detailsModal<?php echo $rdv['id']; ?>" tabindex="-1" role="dialog">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Détails du Rendez-vous</h5>
                                        <button type="button" class="close" data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <p><strong>Type:</strong> <?php echo e($rdv['type_consultation']); ?></p>
                                        <p><strong>Date:</strong> <?php echo format_date($rdv['date']); ?></p>
                                        <p><strong>Heure:</strong> <?php echo format_time($rdv['heure']); ?></p>
                                        <p><strong>Médecin:</strong> <?php echo e($rdv['medecin']); ?></p>
                                        <p><strong>Lieu:</strong> <?php echo e($rdv['lieu']); ?></p>
                                        <p><strong>Notes:</strong></p>
                                        <p><?php echo nl2br(e($rdv['notes'])); ?></p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
