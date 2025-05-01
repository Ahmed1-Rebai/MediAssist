<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$page_title = "Confidentialité - MediAssist";
include __DIR__ . '/includes/header.php';
?>

<div class="container py-5">
    <div class="logo">
        <a href="index.php">
            <i class="fas fa-stethoscope"></i> MediAssist
        </a>
    </div>
    <h1 class="mb-4">Politique de Confidentialité</h1>

    <section class="mb-5">
        <h2>1. Collecte des données</h2>
        <p>MediAssist stocke uniquement les données nécessaires à votre suivi médical. Nous ne partageons aucune
            information avec des tiers sans votre consentement.</p>
    </section>

    <section class="mb-5">
        <h2>2. Sécurité</h2>
        <p>Toutes les données sont chiffrées (AES-256) et protégées conformément au RGPD.</p>
    </section>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>