<?php
date_default_timezone_set("Africa/Tunis");

require_once '../includes/config.php';
require __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require __DIR__ . '/../PHPMailer/src/SMTP.php';
require __DIR__ . '/../PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

$SMTP_DEBUG_MODE = false;
$EMAIL_SENDER = 'rebaiahmed244@gmail.com';
$EMAIL_PASSWORD = 'rgznwufglibaogtk';

// Affichage de l'heure actuelle et timezone
$heureActuelle = date('H:i');
echo "⏰ Heure actuelle serveur : $heureActuelle<br>";
echo "🕓 Fuseau horaire utilisé : " . date_default_timezone_get() . "<br><br>";

function normalizeTime($time) {
    return date("H:i", strtotime(preg_replace('/[^0-9:]/', '', $time)));
}

try {
    if (!$pdo) {
        throw new Exception("Erreur de connexion à la base de données");
    }

    $sql = "SELECT m.id, m.nom AS nomMedicament, m.heures, u.email, u.username AS nomUtilisateur 
            FROM medicaments m
            JOIN users u ON u.id = m.user_id
            WHERE m.heures IS NOT NULL 
              AND m.heures != 'null' 
              AND TRIM(m.heures) != ''
              AND u.is_verified = 1";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $totalMedicaments = 0;
    $emailsEnvoyes = 0;

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $totalMedicaments++;

        $heuresJson = trim($row['heures']);
        $heures = json_decode($heuresJson, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($heures)) {
            continue;
        }

        foreach ($heures as $heure) {
            $heureNormalisee = normalizeTime($heure);
            $timestampMedicament = strtotime($heureNormalisee);
            $timestampActuel = strtotime($heureActuelle);

            echo "🔎 Médicament : {$row['nomMedicament']} → Heure prévue : $heureNormalisee<br>";

            // TOLÉRANCE DE ±2 minutes (120 secondes)
            if (abs($timestampMedicament - $timestampActuel) <= 120) {
                echo "✅ Correspondance détectée pour {$row['email']} à $heureNormalisee<br>";

                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = $EMAIL_SENDER;
                    $mail->Password = $EMAIL_PASSWORD;
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;
                    $mail->CharSet = 'UTF-8';
                    $mail->Timeout = 20;
                    $mail->SMTPDebug = $SMTP_DEBUG_MODE ? SMTP::DEBUG_SERVER : 0;

                    $mail->setFrom($EMAIL_SENDER, 'MediAssist');
                    $mail->addAddress($row['email'], $row['nomUtilisateur']);
                    $mail->isHTML(true);
                    $mail->Subject = '💊 Rappel : Médicament à prendre';
                    $mail->Body = "
                        <html>
                        <body style='font-family: Arial, sans-serif;'>
                            <h3>Bonjour {$row['nomUtilisateur']},</h3>
                            <p>Ceci est un rappel automatique pour prendre votre médicament :</p>
                            <p><strong>{$row['nomMedicament']}</strong> à <strong>$heureNormalisee</strong>.</p>
                            <br>
                            <p>Bonne santé !</p>
                            <p><em>L'équipe MediAssist</em></p>
                        </body>
                        </html>
                    ";

                    $mail->send();
                    $emailsEnvoyes++;
                    break; // pas besoin de tester les autres heures pour ce médicament
                } catch (Exception $e) {
                    echo "❌ Erreur envoi mail à {$row['email']}<br>";
                }
            }
        }
    }

    echo "<br>=== RÉCAPITULATIF ===<br>";
    echo "🧪 Médicaments analysés : $totalMedicaments<br>";
    echo "📬 Emails envoyés : $emailsEnvoyes<br>";

} catch (Exception $e) {
    echo "ERREUR GÉNÉRALE : " . $e->getMessage();
}
?>
