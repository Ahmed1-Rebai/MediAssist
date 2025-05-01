<?php
require_once 'config.php';
require_once 'functions.php';
?>

<nav class="main-navbar">
    <div class="container">
        <div class="logo">
            <a href="<?= BASE_URL ?>/dashboard.php">
                <i class="fas fa-stethoscope"></i> <span class="logo-text">MediAssist</span>
            </a>
        </div>
        
        
        <div class="navbar-links">
            <ul>
                <li><a href="<?= BASE_URL ?>/dashboard.php"><i class="fas fa-home"></i> <span class="link-text">Tableau de bord</span></a></li>
                <li><a href="<?= BASE_URL ?>/medicaments/list.php"><i class="fas fa-pills"></i> <span class="link-text">Médicaments</span></a></li>
                <li><a href="<?= BASE_URL ?>/rendezvous/list.php"><i class="fas fa-calendar-check"></i> <span class="link-text">Rendez-vous</span></a></li>
                <li><a href="<?= BASE_URL ?>/ordonnances/view.php"><i class="fas fa-file-prescription"></i> <span class="link-text">Ordonnances</span></a></li>
                <li><a href="<?= BASE_URL ?>/contacts/list.php"><i class="fas fa-address-book"></i> <span class="link-text">Contacts</span></a></li>
            </ul>
        </div>
        
        <div class="navbar-user">
            <div class="dropdown">
                <button class="dropdown-toggle">
                    <i class="fas fa-user-circle"></i>
                    <span class="user-text">Mon compte</span>
                    <i class="fas fa-caret-down"></i>
                </button>
                <div class="dropdown-menu">
                    <a href="<?= BASE_URL ?>/includes/profile.php"><i class="fas fa-user"></i> Profil</a>
                    <a href="<?= BASE_URL ?>/includes/settings.php"><i class="fas fa-cog"></i> Paramètres</a>
                    <div class="dropdown-divider"></div>
                    <a href="<?= BASE_URL ?>/logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
                </div>
            </div>
        </div>
    </div>
</nav>