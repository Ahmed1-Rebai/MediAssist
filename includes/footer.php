</main>
        <footer class="main-footer">
            <div class="container">
                <div class="footer-content">
                    <div class="footer-brand">
                        <h3>MediAssist</h3>
                        <p>Votre compagnon de santé numérique</p>
                    </div>
                    <div class="footer-links">
                    <a href="/about.php">À propos</a>
                    <a href="/privacy.php">Confidentialité</a>
                    <a href="/contact.php">Contact</a>
                    </div>
                </div>
                <div class="footer-copyright">
                    <p>&copy; <?php echo date('Y'); ?> MediAssist. Tous droits réservés.</p>
                </div>
            </div>
        </footer>
        
        <!-- JavaScript -->
        <script src="assets/js/main.js"></script>
        <?php if (isset($additional_scripts)): ?>
            <?php foreach ($additional_scripts as $script): ?>
                <script src="assets/js/<?php echo e($script); ?>"></script>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>