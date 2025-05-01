<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$page_title = "À propos - MediAssist";
include __DIR__ . '/includes/header.php';
?>

<div class="container">
    <div class="logo">
        <a href="index.php">
            <i class="fas fa-stethoscope"></i> MediAssist
        </a>
    </div>
    <h1>À propos de MediAssist</h1>
    <p>
        MediAssist est une application web de santé numérique développée pour faciliter la gestion quotidienne des
        médicaments et des rendez-vous médicaux.
    </p>

    <p>
        Notre objectif est d’offrir aux utilisateurs une solution simple, rapide et sécurisée pour :
    </p>

    <ul>
        <li>Suivre leurs traitements</li>
        <li>Organiser leurs consultations médicales</li>
        <li>Recevoir des rappels automatiques</li>
        <li>Centraliser les informations utiles à leur suivi médical</li>
    </ul>

    <p>
        Développée en HTML, CSS, JavaScript et PHP, MediAssist combine accessibilité et efficacité tout en respectant la
        confidentialité des données personnelles.
    </p>

</div>

<?php include __DIR__ . '/includes/footer.php'; ?>