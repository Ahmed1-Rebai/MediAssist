<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once __DIR__ . '/includes/config.php';
require_once 'includes/functions.php';

if (is_logged_in()) {
    header('Location: dashboard.php');
    exit();
}

$page_title = "MediAssist - Gestion Médicale";

include 'includes/header.php';
?>
<div class="page-wrapper">
    <div class="bubbles-container">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
    </div>

    <header class="main-header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <i class="fas fa-stethoscope"></i> MediAssist
                </div>
                <div class="header-buttons">
                    <a href="login.php" class="btn btn-login">
                        <i class="fas fa-sign-in-alt"></i> Connexion
                    </a>
                    <a href="register.php" class="btn btn-register">
                        <i class="fas fa-user-plus"></i> Inscription
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main>
        <section class="hero-section">
            <?php for ($i = 0; $i < 10; $i++): ?>
                <span></span>
            <?php endfor; ?>

            <div class="container">
                <div class="hero-grid">
                    <div class="hero-text" data-aos="fade-right">
                        <h1>Bienvenue sur <span>MediAssist</span></h1>
                        <p class="subtitle">Votre solution complète de gestion médicale</p>
                        <div class="cta-buttons">
                            <a href="#features" class="btn btn-primary">
                                <i class="fas fa-search"></i> Découvrir
                            </a>
                            <a href="#" class="btn btn-outline" id="videoDemoBtn">
                                <i class="fas fa-play-circle"></i> Démo vidéo
                            </a>
                        </div>
                    </div>
                    <div class="hero-image" data-aos="fade-left">
                        <img src="assets/images/medical-team.jpg" alt="Équipe médicale" loading="lazy">
                        <div class="image-overlay"></div>
                    </div>
                </div>
            </div>
        </section>

        <section id="features" class="features-section">
            <div class="container">
                <h2 class="section-title" data-aos="fade-up">Fonctionnalités Principales</h2>
                <div class="features-grid">
                    <div class="feature-card" data-aos="zoom-in" data-aos-delay="100">
                        <div class="feature-icon">
                            <i class="fas fa-pills"></i>
                        </div>
                        <h3>Gestion des Médicaments</h3>
                        <p>Suivi et rappels de prise de médicaments en temps réel</p>
                    </div>

                    <div class="feature-card" data-aos="zoom-in" data-aos-delay="200">
                        <div class="feature-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <h3>Rendez-vous Médicaux</h3>
                        <p>Calendrier intelligent</p>
                    </div>

                    <div class="feature-card" data-aos="zoom-in" data-aos-delay="300">
                        <div class="feature-icon">
                            <i class="fas fa-file-medical"></i>
                        </div>
                        <h3>Ordonnances Numériques</h3>
                        <p>Stockage sécurisé et consultation simplifiée</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Modal Video Demo -->
    <div id="videoModal" class="modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <div class="video-container">
                <video id="demoVideo" controls>
                    <source src="<?php echo BASE_URL; ?>/assets/videos/demo.mp4" type="video/mp4">
                </video>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <!-- Chargement des scripts JS -->
    <script src="assets/js/video-modal.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({ duration: 800, easing: 'ease-out-quad', once: true });
    </script>
    <script src="assets/js/bubbles.js"></script>