    <?php
    require_once '../includes/config.php';
    require_once '../includes/auth.php';

    // Démarrer le buffer dès le début
    ob_start();
    require_login();

    if (!isset($_GET['id'])) {
        ob_end_clean();
        header("HTTP/1.0 400 Bad Request");
        exit("ID manquant");
    }

    // Récupération fichier
    $stmt = $pdo->prepare("SELECT * FROM ordonnances WHERE id = ? AND user_id = ?");
    $stmt->execute([$_GET['id'], $_SESSION['user_id']]);
    $ordonnance = $stmt->fetch();

    if (!$ordonnance) {
        ob_end_clean();
        header("HTTP/1.0 404 Not Found");
        exit("Ordonnance non trouvée");
    }

    $file_path = realpath(UPLOAD_DIR . $ordonnance['chemin']);

    // Vérifications finales
    if (!$file_path || !file_exists($file_path)) {
        ob_end_clean();
        header("HTTP/1.0 404 Not Found");
        exit("Fichier non trouvé: " . $file_path);
    }

    // Nettoyage des buffers
    while (ob_get_level()) {
        ob_end_clean();
    }

    // En-têtes de téléchargement
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($ordonnance['nom_fichier']) . '"');
    header('Content-Length: ' . filesize($file_path));
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');

    // Envoi du fichier
    readfile($file_path);
    exit;
    ?>