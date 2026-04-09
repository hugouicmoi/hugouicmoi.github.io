<?php
session_start();

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';

$currentUser = null;
$claimedActionKeys = [];
$savedContacts = [];

if (isset($_SESSION['user_id'])) {
    $currentUserStmt = $db->prepare('SELECT * FROM users WHERE id = ?');
    $currentUserStmt->execute([$_SESSION['user_id']]);
    $currentUser = $currentUserStmt->fetch(PDO::FETCH_ASSOC);

    $claimedActionsStmt = $db->prepare('SELECT action_key FROM user_point_actions WHERE user_id = ?');
    $claimedActionsStmt->execute([$_SESSION['user_id']]);
    $claimedActionKeys = $claimedActionsStmt->fetchAll(PDO::FETCH_COLUMN);

    $savedContactsStmt = $db->prepare(
        'SELECT * FROM saved_contacts WHERE owner_user_id = ? ORDER BY id DESC LIMIT 10'
    );
    $savedContactsStmt->execute([$_SESSION['user_id']]);
    $savedContacts = $savedContactsStmt->fetchAll(PDO::FETCH_ASSOC);
}

$heroUser = $currentUser;

if (!$heroUser) {
    $heroUser = [
        'id' => 0,
        'fullname' => 'Nom Prénom',
        'job' => 'Métier',
        'bio' => 'Bio à remplir...',
        'points' => 0,
        'badge' => 'Badge',
        'profile_image' => '',
        'profile_image_file' => '',
        'contact_email' => '',
        'contact_phone' => '',
    ];
}

$heroName = $heroUser['fullname'] ?? 'Nom Prénom';
$heroJob = $heroUser['job'] ?? 'Métier';
$heroBio = $heroUser['bio'] ?? 'Bio à remplir...';
$heroPoints = (int) ($heroUser['points'] ?? 0);
$heroBadge = $heroUser['badge'] ?? 'Badge';
$heroImage = !empty($heroUser['profile_image_file'])
    ? $heroUser['profile_image_file']
    : ($heroUser['profile_image'] ?? '');
$heroContactEmail = $heroUser['contact_email'] ?? '';
$heroContactPhone = $heroUser['contact_phone'] ?? '';
$heroId = (int) ($heroUser['id'] ?? 0);
$heroComments = $heroId > 0 ? getUserComments($db, $heroId, 4) : [];
$heroActionCount = $heroId > 0 ? getUserActionCount($db, $heroId) : 0;

$leaderboardStmt = $db->query('SELECT * FROM users ORDER BY points DESC, id DESC');
$leaderboardUsers = $leaderboardStmt->fetchAll(PDO::FETCH_ASSOC);
$topMembers = array_slice($leaderboardUsers, 0, 3);

$activeUsersStmt = $db->query('SELECT * FROM users WHERE joined_active = 1 ORDER BY points DESC, id DESC');
$allActiveUsers = $activeUsersStmt->fetchAll(PDO::FETCH_ASSOC);

$defaultTop = [
    ['fullname' => 'Camille Petit', 'job' => 'Directrice Artistique', 'points' => 160, 'badge' => badgeFromPoints(160), 'profile_image' => '', 'profile_image_file' => ''],
    ['fullname' => 'Léa Martin', 'job' => 'UX Designer', 'points' => 140, 'badge' => badgeFromPoints(140), 'profile_image' => '', 'profile_image_file' => ''],
    ['fullname' => 'Sarah Morel', 'job' => 'Product Designer', 'points' => 132, 'badge' => badgeFromPoints(132), 'profile_image' => '', 'profile_image_file' => ''],
];

$carouselUsers = count($allActiveUsers) < 8 ? array_merge($allActiveUsers, $allActiveUsers) : $allActiveUsers;
[$topRowUsers, $bottomRowUsers] = splitUsersIntoRows($carouselUsers);

if (count($topRowUsers) < 4 && count($topRowUsers) > 0) {
    $topRowUsers = array_merge($topRowUsers, $topRowUsers);
}

if (count($bottomRowUsers) < 4 && count($bottomRowUsers) > 0) {
    $bottomRowUsers = array_merge($bottomRowUsers, $bottomRowUsers);
}

$commentsByUser = [];

foreach ($allActiveUsers as $user) {
    $commentsByUser[$user['id']] = getUserComments($db, (int) $user['id'], 3);
}

$searchProfiles = [];
foreach ($allActiveUsers as $user) {
    $searchProfiles[] = [
        'id' => (int) $user['id'],
        'fullname' => $user['fullname'],
        'job' => $user['job'],
        'bio' => $user['bio'],
        'badge' => badgeFromPoints((int) $user['points']),
        'points' => (int) $user['points'],
        'contact_email' => $user['contact_email'] ?? '',
        'contact_phone' => $user['contact_phone'] ?? '',
        'profile_image' => !empty($user['profile_image_file']) ? $user['profile_image_file'] : ($user['profile_image'] ?? ''),
        'comments' => $commentsByUser[$user['id']] ?? [],
    ];
}

$reviews = [
    [
        'title' => 'Produit simple et efficace',
        'text' => 'On sent que le produit a été pensé par des gens qui connaissent le terrain. Les cartes Neear sont faciles à partager et très pros.',
        'source' => 'Google',
    ],
    [
        'title' => 'Très pratique en rendez-vous',
        'text' => 'Je n’imprime presque plus de cartes papier. Je partage mon profil Neear en quelques secondes, c’est plus rapide et plus propre.',
        'source' => 'Google',
    ],
    [
        'title' => 'Bonne image de marque',
        'text' => 'Le rendu est soigné et ça donne une meilleure première impression en salon, en meeting ou après un appel découverte.',
        'source' => 'Trustpilot',
    ],
    [
        'title' => 'Parfait pour le networking',
        'text' => 'Le concept est clair, moderne et utile. Ça évite de perdre des contacts.',
        'source' => 'Google',
    ],
    [
        'title' => 'Top pour les indépendants',
        'text' => 'Je suis freelance et Neear me permet d’envoyer un profil propre à mes prospects au lieu d’un simple lien brut.',
        'source' => 'Avis client',
    ],
];

$posts = [
    [
        'author' => 'Léa Martin',
        'handle' => '@lea.ux',
        'job' => 'UX Designer',
        'content' => 'Je viens de remplacer mes cartes papier par Neear pour mes rendez-vous clients. Beaucoup plus simple à partager et bien plus clean visuellement.',
        'tag' => 'Retour d’expérience',
    ],
    [
        'author' => 'Thomas Bernard',
        'handle' => '@thomasbuilds',
        'job' => 'Développeur Front-End',
        'content' => 'Petit retour après une semaine avec Neear : je récupère plus facilement mes contacts après les events.',
        'tag' => 'Produit',
    ],
    [
        'author' => 'Camille Petit',
        'handle' => '@camillebrand',
        'job' => 'Directrice Artistique',
        'content' => 'Ce que j’aime chez Neear, c’est que la carte reste cohérente avec mon univers de marque. C’est pro, rapide et plus actuel que du papier.',
        'tag' => 'Branding',
    ],
    [
        'author' => 'Sarah Morel',
        'handle' => '@sarahproduct',
        'job' => 'Product Designer',
        'content' => 'Le vrai plus de Neear pour moi : en réunion ou en coworking, tu partages ton profil direct sans chercher une carte au fond du sac.',
        'tag' => 'Usage',
    ],
    [
        'author' => 'Julie Simon',
        'handle' => '@juliesimon.ui',
        'job' => 'UI Designer',
        'content' => 'J’ai montré ma carte Neear à plusieurs clients cette semaine. Ils ont tous trouvé le principe moderne et très pratique.',
        'tag' => 'Client',
    ],
];

include __DIR__ . '/home.php';