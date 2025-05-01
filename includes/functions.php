<?php
require_once 'config.php';

/**
 * Check if user is logged in
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}



/**
 * Redirect to login if not authenticated
 */
function require_login() {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit();
    }
}

/**
 * Authenticate user
 */
function authenticate_user($email, $password) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    } catch (PDOException $e) {
        error_log("Authentication error: " . $e->getMessage());
        return false;
    }
}

/**
 * Check if email exists
 */
function email_exists($email) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    return $stmt->fetch() !== false;
}


/**
 * Register new user
 */
function register_user($username, $email, $password) {
    global $pdo;
    
    // Generate verification token
    $token = bin2hex(random_bytes(32));
    $token_hash = hash('sha256', $token);
    $expiry = date("Y-m-d H:i:s", time() + 3600); // 1 hour expiration
    
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, verification_token, token_expires_at) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$username, $email, $hashed_password, $token_hash, $expiry]);
        
        // Send verification email
        send_verification_email($email, $token);
        
        return true;
    } catch (PDOException $e) {
        error_log("Registration error: " . $e->getMessage());
        return false;
    }
}

function send_verification_email($email, $token): bool {
    require __DIR__ . '/../PHPMailer/src/PHPMailer.php';
    require __DIR__ . '/../PHPMailer/src/SMTP.php';
    require __DIR__ . '/../PHPMailer/src/Exception.php';

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);

    try {
        // Serveur SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // à adapter si nécessaire
        $mail->SMTPAuth = true;
        $mail->Username = 'rebaiahmed244@gmail.com'; // TON EMAIL GMAIL
        $mail->Password = 'rgznwufglibaogtk';    // MOT DE PASSE D'APPLICATION
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Destinataire
        $mail->setFrom('rebaiahmed244@gmail.com', 'MediAssist');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Vérification de votre email - MediAssist';

        $verification_url = BASE_URL . "/verify.php?token=" . urlencode($token);
        $mail->Body = "
            <html>
            <body>
                <h2>Merci pour votre inscription!</h2>
                <p>Veuillez cliquer sur ce lien pour activer votre compte :</p>
                <p><a href='$verification_url'>$verification_url</a></p>
            </body>
            </html>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email error: " . $mail->ErrorInfo);
        return false;
    }
}


/**
 * Format date for display
 */
function format_date($date) {
    return date("d-m-Y", strtotime($date)); // Si vous avez besoin de formater la date
}

function format_time($time) {
    return date("H:i", strtotime($time)); // Pour formater l'heure
}


/**
 * Get medication count for user
 */
function get_medicament_count($user_id) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM medicaments WHERE user_id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetchColumn();
}

/**
 * Get appointment count for user
 */
function get_appointment_count($user_id) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM rendezvous WHERE user_id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetchColumn();
}

/**
 * Get prescription count for user
 */
function get_prescription_count($user_id) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM ordonnances WHERE user_id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetchColumn();
}

/**
 * Get upcoming appointments - FIXED LIMIT ISSUE
 */
function get_upcoming_appointments($user_id, $limit = 5) {
    global $pdo;
    
    // Cast to integer for safety
    $limit = (int)$limit;
    
    // Use direct parameter binding with explicit type
    $stmt = $pdo->prepare("SELECT * FROM rendezvous 
                          WHERE user_id = :user_id AND date >= CURDATE()
                          ORDER BY date, heure ASC
                          LIMIT :limit");
    
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get upcoming medications - FIXED LIMIT ISSUE
 */
function get_upcoming_medicaments($user_id, $limit = 5) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT id, nom, posologie, frequence, heures 
                          FROM medicaments 
                          WHERE user_id = ? 
                          ORDER BY nom ASC 
                          LIMIT ?");
    $stmt->execute([$user_id, $limit]);
    $medicaments = $stmt->fetchAll();
    
    foreach ($medicaments as &$med) {
        if (!empty($med['heures'])) {
            $med['heures'] = json_decode($med['heures'], true);
        } else {
            $med['heures'] = [];
        }
    }
    
    return $medicaments;
}

/**
 * Escape HTML output
 */
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Get contact count for user
 */
function get_contact_count($user_id) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM urgence_contacts WHERE user_id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetchColumn();
}

/**
 * Send notification email
 */
function send_notification_email($to, $subject, $message) {
    $headers = "From: MediAssist <no-reply@mediassist.com>\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    return mail($to, $subject, $message, $headers);
}

function generate_text_captcha() {
    $questions = [
        "3 + 5 = ?" => 8,
        "Quelle est la première lettre de 'Maison' ?" => "m",
        "Combien font 10 - 4 ?" => 6,
        "Écrivez 'trois' en chiffres" => 3,
        "Premier chiffre de 42" => 4,

    ];
    
    $random_question = array_rand($questions);
    $_SESSION['captcha_answer'] = $questions[$random_question];
    return $random_question;
}

function validate_text_captcha($user_input) {
    if (empty($_SESSION['captcha_answer'])) {
        return false;
    }
    return strtolower(trim($user_input)) == strtolower($_SESSION['captcha_answer']);
}



/**
 * Met à jour les informations du profil utilisateur
 */
function update_user_profile($user_id, $username, $email) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
        return $stmt->execute([$username, $email, $user_id]);
    } catch (PDOException $e) {
        error_log("Erreur mise à jour profil: " . $e->getMessage());
        return false;
    }
}
/**
 * Update user's last login time
 */
function update_last_login($user_id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
        return $stmt->execute([$user_id]);
    } catch (PDOException $e) {
        error_log("Error updating last login: " . $e->getMessage());
        return false;
    }
}

/**
 * Change le mot de passe utilisateur
 */
function change_user_password($user_id, $current_password, $new_password) {
    global $pdo;
    
    // Vérifier l'ancien mot de passe
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    
    if (!$user || !password_verify($current_password, $user['password'])) {
        return false;
    }
    
    // Mettre à jour le mot de passe
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
    return $stmt->execute([$hashed_password, $user_id]);
}

/**
 * Récupère les données du profil utilisateur
 */
function get_user_profile($user_id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT username, email, created_at, last_login FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching user profile: " . $e->getMessage());
        return false;
    }
}

/**
 * Valide les données du formulaire de profil
 */
function validate_profile_data($data) {
    $errors = [];
    
    if (empty($data['username'])) {
        $errors['username'] = "Le nom d'utilisateur est requis";
    }
    
    if (empty($data['email'])) {
        $errors['email'] = "L'email est requis";
    } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "L'email n'est pas valide";
    }
    
    return $errors;
}

/**
 * Valide les données de changement de mot de passe
 */
function validate_password_data($data) {
    $errors = [];
    
    if (empty($data['current_password'])) {
        $errors['current_password'] = "Le mot de passe actuel est requis";
    }
    
    if (empty($data['new_password'])) {
        $errors['new_password'] = "Le nouveau mot de passe est requis";
    } elseif (strlen($data['new_password']) < 8) {
        $errors['new_password'] = "Le mot de passe doit contenir au moins 8 caractères";
    }
    
    if ($data['new_password'] !== $data['confirm_password']) {
        $errors['confirm_password'] = "Les mots de passe ne correspondent pas";
    }
    
    return $errors;
}

function display_flash_message() {
    if (!empty($_SESSION['flash'])) {
        $message = $_SESSION['flash']['message'];
        $type = $_SESSION['flash']['type'];
        
        echo '<div class="alert alert-'.$type.' alert-dismissible fade show" role="alert">';
        echo $message;
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        echo '</div>';
        
        // Clear the message after displaying
        unset($_SESSION['flash']);
    }
}



