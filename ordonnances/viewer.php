<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_login();

if (!isset($_GET['id'])) {
    header("HTTP/1.0 400 Bad Request");
    exit("ID manquant");
}

// Récupération fichier
$stmt = $pdo->prepare("SELECT * FROM ordonnances WHERE id = ? AND user_id = ?");
$stmt->execute([$_GET['id'], $_SESSION['user_id']]);
$ordonnance = $stmt->fetch();

if (!$ordonnance) {
    header("HTTP/1.0 404 Not Found");
    exit("Ordonnance non trouvée");
}

$file_path = realpath(UPLOAD_DIR . $ordonnance['chemin']);
$file_ext = strtolower(pathinfo($ordonnance['chemin'], PATHINFO_EXTENSION));

// Vérifications finales
if (!$file_path || !file_exists($file_path)) {
    header("HTTP/1.0 404 Not Found");
    exit("Fichier non trouvé");
}

// Définir les types MIME
$mime_types = [
    'pdf' => 'application/pdf',
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png' => 'image/png',
    'gif' => 'image/gif'
];

// Vérifier le type de fichier
if (!array_key_exists($file_ext, $mime_types)) {
    header("HTTP/1.0 400 Bad Request");
    exit("Type de fichier non supporté pour la visualisation");
}

// En-têtes pour afficher le fichier dans le navigateur
header('Content-Type: ' . $mime_types[$file_ext]);
header('Content-Disposition: inline; filename="' . basename($ordonnance['nom_fichier']) . '"');
header('Content-Length: ' . filesize($file_path));
header('Cache-Control: public, must-revalidate, max-age=0');
header('Pragma: public');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');

readfile($file_path);
exit;
?>