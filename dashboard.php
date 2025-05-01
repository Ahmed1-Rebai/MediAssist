<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

// Redirect non-logged-in users
if (!is_logged_in()) {
    header('Location: login.php');
    exit();
}

$page_title = "Tableau de Bord - MediAssist";
$user_id = $_SESSION['user_id'];

// Get counts for dashboard
$medicament_count = get_medicament_count($user_id);
$appointment_count = get_appointment_count($user_id);
$prescription_count = get_prescription_count($user_id);
$contacts_count = get_contact_count($user_id);

include 'includes/header.php';
include 'includes/navbar.php';

$stmt = $pdo->prepare("SELECT id, user_id, nom, posologie, frequence, heures 
                      FROM medicaments 
                      WHERE user_id = ? 
                      ORDER BY nom ASC
                      LIMIT 5");
$stmt->execute([$_SESSION['user_id']]);
$upcoming_meds = $stmt->fetchAll();

// Process the heures data (assuming it's JSON)
foreach ($upcoming_meds as &$med) {
    if (!empty($med['heures'])) {
        $med['heures'] = json_decode($med['heures'], true);
    } else {
        $med['heures'] = [];
    }
}
unset($med); // Break the reference
// Get today's appointments
$stmt = $pdo->prepare("SELECT * FROM rendezvous 
                      WHERE user_id = ? AND date = CURDATE()
                      ORDER BY heure ASC");
$stmt->execute([$_SESSION['usesr_id']]);
$today_appointments = $stmt->fetchAll();

// Get recent prescriptions
$stmt = $pdo->prepare("SELECT * FROM ordonnances 
                      WHERE user_id = ? 
                      ORDER BY date_upload DESC 
                      LIMIT 3");
$stmt->execute([$_SESSION['user_id']]);
$recent_prescriptions = $stmt->fetchAll();

// Get emergency contacts
$stmt = $pdo->prepare("SELECT * FROM urgence_contacts 
                      WHERE user_id = ? 
                      ORDER BY nom ASC");
$stmt->execute([$_SESSION['user_id']]);
$contacts = $stmt->fetchAll();

?>


<div class="dashboard-container">
    <div class="welcome-section">
        <h1>Bonjour, <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
        <p>Gérez votre santé en toute simplicité</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <h3>Médicaments</h3>
            <p><?php echo $medicament_count; ?></p>
            <a href="medicaments/list.php" class="btn btn-outline-primary">Voir</a>
        </div>

        <div class="stat-card">
            <h3>Rendez-vous</h3>
            <p><?php echo $appointment_count; ?></p>
            <a href="rendezvous/list.php" class="btn btn-outline-primary">Voir</a>
        </div>

        <div class="stat-card">
            <h3>Ordonnances</h3>
            <p><?php echo $prescription_count; ?></p>
            <a href="ordonnances/view.php" class="btn btn-outline-primary">Voir</a>
        </div>

        <div class="stat-card">
            <h3>Contacts d'Urgence</h3>
            <p><?php echo $contacts_count; ?></p>
            <a href="contacts/list.php" class="btn btn-outline-primary">Voir</a>
        </div>
    </div>

    <div class="dashboard-sections">
        <div class="upcoming-appointments">
            <h2>Prochains Rendez-vous</h2>
            <?php $appointments = get_upcoming_appointments($user_id, 3); ?>
            <?php if (!empty($appointments)): ?>
                <ul class="appointment-list">
                    <?php foreach ($appointments as $appointment): ?>
                        <li>
                            <strong><?php echo htmlspecialchars($appointment['type_consultation']); ?></strong>
                            <span><?php echo format_date($appointment['date']) . ' à ' . format_time($appointment['heure']); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <a href="rendezvous/list.php" class="btn btn-link">Voir tous</a>
            <?php else: ?>
                <p>Aucun rendez-vous à venir</p>
            <?php endif; ?>
        </div>

        <div class="medication-reminders">
            <h2>Prochains Médicaments</h2>
            <?php if (!empty($upcoming_meds)): ?>
                <ul class="medicament-list">
                    <?php foreach ($upcoming_meds as $medicament): ?>
                        <li>
                            <strong><?php echo htmlspecialchars($medicament['nom']); ?></strong>
                            <div><?php echo htmlspecialchars($medicament['posologie']); ?></div>
                            <?php if (!empty($medicament['frequence'])): ?>
                                <div>Fréquence: <?php echo htmlspecialchars($medicament['frequence']); ?></div>
                            <?php endif; ?>
                            <?php if (!empty($medicament['heures'])): ?>
                                <div>Heures: <?php echo implode(', ', $medicament['heures']); ?></div>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <a href="medicaments/list.php" class="btn btn-link">Voir tous</a>
            <?php else: ?>
                <p>Aucun médicament à prendre prochainement</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
<script src="assets/js/bubbles.js"></script>