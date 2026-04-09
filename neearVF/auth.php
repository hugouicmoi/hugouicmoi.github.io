<?php
/**
 * auth.php
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = '';
$success = '';
$authTab = 'login';
$scoreMessage = '';
$scoreError = '';
$profileMessage = '';
$profileError = '';
$commentMessage = '';
$commentError = '';
$contactMessage = '';
$contactError = '';
$newsletterMessage = '';
$newsletterError = '';

$pointActions = [
    'referral' => ['label' => 'Parrainer un nouvel utilisateur', 'points' => 50],
    'premium' => ['label' => 'Abonnement premium Pionnier', 'points' => 40],
    'review' => ['label' => 'Laisser un avis', 'points' => 30],
    'linkedin' => ['label' => 'Partager un post LinkedIn', 'points' => 20],
    'feedback' => ['label' => 'Donner un feedback produit', 'points' => 15],
    'signup' => ['label' => 'Créer un compte Neear', 'points' => 10],
];

function getUserPoints(PDO $db, int $userId): int
{
    $stmt = $db->prepare('SELECT points FROM users WHERE id = ?');
    $stmt->execute([$userId]);
    return (int) $stmt->fetchColumn();
}

function claimPoints(PDO $db, int $userId, string $actionKey, array $pointActions): array
{
    if (!isset($pointActions[$actionKey])) {
        return ['error' => 'Action invalide.', 'success' => ''];
    }

    $pointsToAdd = (int) $pointActions[$actionKey]['points'];

    if ($actionKey === 'signup') {
        $checkStmt = $db->prepare('SELECT id FROM user_point_actions WHERE user_id = ? AND action_key = ?');
        $checkStmt->execute([$userId, $actionKey]);

        if ($checkStmt->fetch()) {
            return ['error' => 'Cette action a déjà été validée sur ton compte.', 'success' => ''];
        }

        try {
            $db->beginTransaction();

            $insertActionStmt = $db->prepare(
                'INSERT INTO user_point_actions (user_id, action_key, points_earned) VALUES (?, ?, ?)'
            );
            $insertActionStmt->execute([$userId, $actionKey, $pointsToAdd]);

            $newPoints = getUserPoints($db, $userId) + $pointsToAdd;

            $updateUserStmt = $db->prepare('UPDATE users SET points = ?, badge = ? WHERE id = ?');
            $updateUserStmt->execute([$newPoints, badgeFromPoints($newPoints), $userId]);

            $db->commit();

            return ['error' => '', 'success' => '+' . $pointsToAdd . ' points ajoutés avec succès.'];
        } catch (Exception $e) {
            $db->rollBack();
            return ['error' => 'Impossible d’ajouter les points pour le moment.', 'success' => ''];
        }
    }

    try {
        $db->beginTransaction();

        $insertActionStmt = $db->prepare(
            'INSERT INTO user_point_actions (user_id, action_key, points_earned) VALUES (?, ?, ?)'
        );
        $insertActionStmt->execute([$userId, $actionKey, $pointsToAdd]);

        if ($actionKey === 'referral') {
            $newPoints = getUserPoints($db, $userId) + $pointsToAdd;

            $stmt = $db->prepare(
                'UPDATE users
                 SET points = ?, badge = ?, referrals_count = referrals_count + 1
                 WHERE id = ?'
            );
            $stmt->execute([$newPoints, badgeFromPoints($newPoints), $userId]);
        } else {
            $newPoints = getUserPoints($db, $userId) + $pointsToAdd;

            $updateUserStmt = $db->prepare('UPDATE users SET points = ?, badge = ? WHERE id = ?');
            $updateUserStmt->execute([$newPoints, badgeFromPoints($newPoints), $userId]);
        }

        $db->commit();

        return ['error' => '', 'success' => '+' . $pointsToAdd . ' points ajoutés avec succès.'];
    } catch (Exception $e) {
        $db->rollBack();
        return ['error' => 'Impossible d’ajouter les points pour le moment.', 'success' => ''];
    }
}

function registerUser(PDO $db, array $pointActions): array
{
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $job = trim($_POST['job'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($fullname === '' || $email === '' || $job === '' || $password === '') {
        return ['error' => 'Tous les champs d’inscription sont obligatoires.', 'success' => '', 'tab' => 'register'];
    }

    $checkStmt = $db->prepare('SELECT id FROM users WHERE email = ?');
    $checkStmt->execute([$email]);

    if ($checkStmt->fetch()) {
        return ['error' => 'Cet email est déjà utilisé.', 'success' => '', 'tab' => 'register'];
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $signupPoints = (int) $pointActions['signup']['points'];
    $badge = badgeFromPoints($signupPoints);

    $insertUserStmt = $db->prepare(
        'INSERT INTO users (
            fullname, email, password, job, points, badge,
            profile_image, profile_image_file, contact_email, contact_phone, referrals_count
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );
    $insertUserStmt->execute([
        $fullname,
        $email,
        $passwordHash,
        $job,
        $signupPoints,
        $badge,
        '',
        '',
        $email,
        '',
        0
    ]);

    $newUserId = (int) $db->lastInsertId();

    $insertScoreStmt = $db->prepare(
        "INSERT INTO user_point_actions (user_id, action_key, points_earned) VALUES (?, 'signup', ?)"
    );
    $insertScoreStmt->execute([$newUserId, $signupPoints]);

    return ['error' => '', 'success' => 'Compte créé avec succès. Tu peux maintenant te connecter.', 'tab' => 'login'];
}

function loginUser(PDO $db): array
{
    $email = trim($_POST['login_email'] ?? '');
    $password = $_POST['login_password'] ?? '';

    if ($email === '' || $password === '') {
        return ['error' => 'Merci de remplir l’email et le mot de passe.', 'tab' => 'login'];
    }

    $stmt = $db->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header('Location: index.php');
        exit;
    }

    return ['error' => 'Email ou mot de passe incorrect.', 'tab' => 'login'];
}

function updateProfile(PDO $db, int $userId): array
{
    $job = trim($_POST['job'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    $profileImage = trim($_POST['profile_image'] ?? '');
    $contactEmail = trim($_POST['contact_email'] ?? '');
    $contactPhone = trim($_POST['contact_phone'] ?? '');

    if ($job === '' || $bio === '') {
        return ['error' => 'Le métier et la section à propos sont obligatoires.', 'success' => ''];
    }

    $currentStmt = $db->prepare('SELECT profile_image_file FROM users WHERE id = ?');
    $currentStmt->execute([$userId]);
    $currentUser = $currentStmt->fetch(PDO::FETCH_ASSOC);

    $profileImageFile = $currentUser['profile_image_file'] ?? '';

    if (!empty($_FILES['profile_image_upload']['name'])) {
        if (!isset($_FILES['profile_image_upload']) || $_FILES['profile_image_upload']['error'] !== UPLOAD_ERR_OK) {
            return ['error' => 'Erreur lors de l’upload de l’image.', 'success' => ''];
        }

        $allowedMimeTypes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png'
        ];

        $tmpPath = $_FILES['profile_image_upload']['tmp_name'];

        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($fileInfo, $tmpPath);
        finfo_close($fileInfo);

        if (!isset($allowedMimeTypes[$mimeType])) {
            return ['error' => 'La photo doit être au format PNG ou JPEG.', 'success' => ''];
        }

        $uploadDir = __DIR__ . '/uploads/profiles';

        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0777, true) && !is_dir($uploadDir)) {
                return ['error' => 'Impossible de créer le dossier d’upload.', 'success' => ''];
            }
        }

        $extension = $allowedMimeTypes[$mimeType];
        $fileName = 'profile_' . $userId . '_' . time() . '.' . $extension;
        $destination = $uploadDir . '/' . $fileName;

        if (!move_uploaded_file($tmpPath, $destination)) {
            return ['error' => 'Impossible d’enregistrer la photo sur le serveur.', 'success' => ''];
        }

        $profileImageFile = 'uploads/profiles/' . $fileName;
        $profileImage = '';
    }

    $stmt = $db->prepare(
        'UPDATE users
         SET job = ?, bio = ?, profile_image = ?, profile_image_file = ?, contact_email = ?, contact_phone = ?
         WHERE id = ?'
    );
    $stmt->execute([
        $job,
        $bio,
        $profileImage,
        $profileImageFile,
        $contactEmail,
        $contactPhone,
        $userId
    ]);

    return ['error' => '', 'success' => 'Profil mis à jour avec succès.'];
}

function addProfileComment(PDO $db, int $targetUserId): array
{
    $authorName = trim($_POST['author_name'] ?? '');
    $comment = trim($_POST['comment'] ?? '');

    if ($authorName === '' || $comment === '') {
        return ['error' => 'Le nom et le commentaire sont obligatoires.', 'success' => ''];
    }

    if ($targetUserId <= 0) {
        return ['error' => 'Profil invalide.', 'success' => ''];
    }

    $stmt = $db->prepare(
        'INSERT INTO profile_comments (target_user_id, author_name, comment)
         VALUES (?, ?, ?)'
    );
    $stmt->execute([$targetUserId, $authorName, $comment]);

    return ['error' => '', 'success' => 'Commentaire ajouté avec succès.'];
}

function saveProfileContact(PDO $db, int $ownerUserId): array
{
    $targetUserId = (int) ($_POST['target_user_id'] ?? 0);
    $savedName = trim($_POST['saved_name'] ?? '');
    $savedJob = trim($_POST['saved_job'] ?? '');
    $savedEmail = trim($_POST['saved_email'] ?? '');
    $savedPhone = trim($_POST['saved_phone'] ?? '');

    if ($targetUserId <= 0 || $savedName === '') {
        return ['error' => 'Contact invalide.', 'success' => ''];
    }

    $stmt = $db->prepare(
        'INSERT INTO saved_contacts (owner_user_id, target_user_id, saved_name, saved_job, saved_email, saved_phone)
         VALUES (?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([$ownerUserId, $targetUserId, $savedName, $savedJob, $savedEmail, $savedPhone]);

    return ['error' => '', 'success' => 'Contact enregistré dans le site.'];
}

function subscribeNewsletter(PDO $db, string $email): array
{
    $email = trim($email);

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['error' => 'Merci de renseigner une adresse email valide.', 'success' => ''];
    }

    $checkStmt = $db->prepare('SELECT id FROM newsletter_subscribers WHERE email = ?');
    $checkStmt->execute([$email]);

    if ($checkStmt->fetch()) {
        return ['error' => '', 'success' => 'Cette adresse est déjà inscrite à la newsletter.'];
    }

    $insertStmt = $db->prepare('INSERT INTO newsletter_subscribers (email) VALUES (?)');
    $insertStmt->execute([$email]);

    $subject = 'Confirmation inscription newsletter';
    $message = "Vous vous êtes bien inscrit à la newsletter";
    $headers = "From: no-reply@neear.fr\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    $mailSent = @mail($email, $subject, $message, $headers);

    if ($mailSent) {
        return ['error' => '', 'success' => 'Inscription confirmée. Votre email a bien été enregistré et un mail de confirmation a été envoyé.'];
    }

    return ['error' => '', 'success' => 'Inscription confirmée. Votre email a bien été enregistré. Le mail de confirmation dépend de la configuration du serveur.'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['claim_points'])) {
    if (!isset($_SESSION['user_id'])) {
        $authTab = 'login';
        $scoreError = 'Tu dois être connecté pour gagner des points.';
    } else {
        $result = claimPoints($db, (int) $_SESSION['user_id'], $_POST['action_key'] ?? '', $pointActions);
        $scoreError = $result['error'];
        $scoreMessage = $result['success'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $result = registerUser($db, $pointActions);
    $error = $result['error'];
    $success = $result['success'];
    $authTab = $result['tab'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $result = loginUser($db);
    $error = $result['error'];
    $authTab = $result['tab'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    if (!isset($_SESSION['user_id'])) {
        $profileError = 'Tu dois être connecté pour modifier ton profil.';
    } else {
        $result = updateProfile($db, (int) $_SESSION['user_id']);
        $profileError = $result['error'];
        $profileMessage = $result['success'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_comment'])) {
    $targetUserId = (int) ($_POST['target_user_id'] ?? 0);
    $result = addProfileComment($db, $targetUserId);
    $commentError = $result['error'];
    $commentMessage = $result['success'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_contact'])) {
    if (!isset($_SESSION['user_id'])) {
        $contactError = 'Tu dois être connecté pour enregistrer un contact.';
    } else {
        $result = saveProfileContact($db, (int) $_SESSION['user_id']);
        $contactError = $result['error'];
        $contactMessage = $result['success'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['subscribe_newsletter'])) {
    $result = subscribeNewsletter($db, $_POST['newsletter_email'] ?? '');
    $newsletterError = $result['error'];
    $newsletterMessage = $result['success'];
}

if (isset($_GET['join']) && isset($_SESSION['user_id'])) {
    $userId = (int) $_SESSION['user_id'];

    $stmt = $db->prepare('UPDATE users SET joined_active = 1 WHERE id = ?');
    $stmt->execute([$userId]);

    $points = getUserPoints($db, $userId) + 15;

    $updatePointsStmt = $db->prepare('UPDATE users SET points = ?, badge = ? WHERE id = ?');
    $updatePointsStmt->execute([$points, badgeFromPoints($points), $userId]);

    header('Location: index.php#profils');
    exit;
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}