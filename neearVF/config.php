<?php
/**
 * config.php
 */

try {
    $db = new PDO('sqlite:' . __DIR__ . '/database.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $db->exec(<<<SQL
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            fullname TEXT NOT NULL,
            email TEXT NOT NULL UNIQUE,
            password TEXT NOT NULL,
            job TEXT DEFAULT 'Brand & Web Designer',
            bio TEXT DEFAULT 'J’aide les projets à se construire visuellement et je participe au rayonnement de la communauté Neear.',
            points INTEGER DEFAULT 95,
            badge TEXT DEFAULT 'Pionnier',
            joined_active INTEGER DEFAULT 0,
            profile_image TEXT DEFAULT '',
            profile_image_file TEXT DEFAULT '',
            contact_email TEXT DEFAULT '',
            contact_phone TEXT DEFAULT '',
            referrals_count INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    SQL);

    try { $db->exec("ALTER TABLE users ADD COLUMN profile_image TEXT DEFAULT ''"); } catch (PDOException $e) {}
    try { $db->exec("ALTER TABLE users ADD COLUMN profile_image_file TEXT DEFAULT ''"); } catch (PDOException $e) {}
    try { $db->exec("ALTER TABLE users ADD COLUMN contact_email TEXT DEFAULT ''"); } catch (PDOException $e) {}
    try { $db->exec("ALTER TABLE users ADD COLUMN contact_phone TEXT DEFAULT ''"); } catch (PDOException $e) {}
    try { $db->exec("ALTER TABLE users ADD COLUMN referrals_count INTEGER DEFAULT 0"); } catch (PDOException $e) {}

    $db->exec(<<<SQL
        CREATE TABLE IF NOT EXISTS user_point_actions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            action_key TEXT NOT NULL,
            points_earned INTEGER NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    SQL);

    $db->exec(<<<SQL
        CREATE TABLE IF NOT EXISTS profile_comments (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            target_user_id INTEGER NOT NULL,
            author_name TEXT NOT NULL,
            comment TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    SQL);

    $db->exec(<<<SQL
        CREATE TABLE IF NOT EXISTS saved_contacts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            owner_user_id INTEGER NOT NULL,
            target_user_id INTEGER NOT NULL,
            saved_name TEXT NOT NULL,
            saved_job TEXT DEFAULT '',
            saved_email TEXT DEFAULT '',
            saved_phone TEXT DEFAULT '',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    SQL);

    $db->exec(<<<SQL
        CREATE TABLE IF NOT EXISTS newsletter_subscribers (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            email TEXT NOT NULL UNIQUE,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    SQL);

    $fakeProfiles = [
        [
            'fullname' => 'Léa Martin',
            'email' => 'lea.martin@neear-demo.fr',
            'job' => 'UX Designer',
            'bio' => 'Je conçois des expériences claires et utiles pour des produits digitaux.',
            'points' => 140,
            'joined_active' => 1,
            'profile_image' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=400&q=80',
            'contact_email' => 'lea.martin@neear-demo.fr',
            'contact_phone' => '06 11 22 33 44',
            'referrals_count' => 1,
        ],
        [
            'fullname' => 'Thomas Bernard',
            'email' => 'thomas.bernard@neear-demo.fr',
            'job' => 'Développeur Front-End',
            'bio' => 'Je transforme les maquettes en interfaces fluides et performantes.',
            'points' => 125,
            'joined_active' => 1,
            'profile_image' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=400&q=80',
            'contact_email' => 'thomas.bernard@neear-demo.fr',
            'contact_phone' => '06 22 33 44 55',
            'referrals_count' => 0,
        ],
        [
            'fullname' => 'Camille Petit',
            'email' => 'camille.petit@neear-demo.fr',
            'job' => 'Directrice Artistique',
            'bio' => 'J’aide les marques à construire une identité forte et cohérente.',
            'points' => 160,
            'joined_active' => 1,
            'profile_image' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?auto=format&fit=crop&w=400&q=80',
            'contact_email' => 'camille.petit@neear-demo.fr',
            'contact_phone' => '06 33 44 55 66',
            'referrals_count' => 2,
        ],
        [
            'fullname' => 'Enzo Robert',
            'email' => 'enzo.robert@neear-demo.fr',
            'job' => 'Motion Designer',
            'bio' => 'Je crée des animations pour rendre les messages plus vivants.',
            'points' => 88,
            'joined_active' => 1,
            'profile_image' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&w=400&q=80',
            'contact_email' => 'enzo.robert@neear-demo.fr',
            'contact_phone' => '06 44 55 66 77',
            'referrals_count' => 0,
        ],
        [
            'fullname' => 'Sarah Morel',
            'email' => 'sarah.morel@neear-demo.fr',
            'job' => 'Product Designer',
            'bio' => 'Je relie stratégie, besoins utilisateurs et design produit.',
            'points' => 132,
            'joined_active' => 1,
            'profile_image' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=400&q=80',
            'contact_email' => 'sarah.morel@neear-demo.fr',
            'contact_phone' => '06 55 66 77 88',
            'referrals_count' => 1,
        ],
        [
            'fullname' => 'Nina Garcia',
            'email' => 'nina.garcia@neear-demo.fr',
            'job' => 'Social Media Manager',
            'bio' => 'Je développe la visibilité des projets grâce à des contenus engageants.',
            'points' => 77,
            'joined_active' => 1,
            'profile_image' => 'https://images.unsplash.com/photo-1488426862026-3ee34a7d66df?auto=format&fit=crop&w=400&q=80',
            'contact_email' => 'nina.garcia@neear-demo.fr',
            'contact_phone' => '06 66 77 88 99',
            'referrals_count' => 0,
        ],
        [
            'fullname' => 'Hugo Laurent',
            'email' => 'hugo.laurent@neear-demo.fr',
            'job' => 'Brand Designer',
            'bio' => 'Je travaille sur les identités visuelles et les supports de marque.',
            'points' => 115,
            'joined_active' => 1,
            'profile_image' => 'https://images.unsplash.com/photo-1504593811423-6dd665756598?auto=format&fit=crop&w=400&q=80',
            'contact_email' => 'hugo.laurent@neear-demo.fr',
            'contact_phone' => '06 77 88 99 00',
            'referrals_count' => 0,
        ],
        [
            'fullname' => 'Julie Simon',
            'email' => 'julie.simon@neear-demo.fr',
            'job' => 'UI Designer',
            'bio' => 'Je dessine des interfaces propres, modernes et faciles à utiliser.',
            'points' => 98,
            'joined_active' => 1,
            'profile_image' => 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&w=400&q=80',
            'contact_email' => 'julie.simon@neear-demo.fr',
            'contact_phone' => '06 88 99 00 11',
            'referrals_count' => 0,
        ],
        [
            'fullname' => 'Maxime Renaud',
            'email' => 'maxime.renaud@neear-demo.fr',
            'job' => 'Développeur No-Code',
            'bio' => 'Je prototype et lance rapidement des outils utiles pour les équipes.',
            'points' => 91,
            'joined_active' => 1,
            'profile_image' => 'https://images.unsplash.com/photo-1504257432389-52343af06ae3?auto=format&fit=crop&w=400&q=80',
            'contact_email' => 'maxime.renaud@neear-demo.fr',
            'contact_phone' => '06 99 00 11 22',
            'referrals_count' => 0,
        ],
        [
            'fullname' => 'Clara Dubois',
            'email' => 'clara.dubois@neear-demo.fr',
            'job' => 'Content Designer',
            'bio' => 'Je rends les interfaces plus compréhensibles grâce à de meilleurs contenus.',
            'points' => 103,
            'joined_active' => 1,
            'profile_image' => 'https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&w=400&q=80',
            'contact_email' => 'clara.dubois@neear-demo.fr',
            'contact_phone' => '07 11 22 33 44',
            'referrals_count' => 1,
        ],
        [
            'fullname' => 'Yanis Leroy',
            'email' => 'yanis.leroy@neear-demo.fr',
            'job' => 'Webflow Developer',
            'bio' => 'Je développe des sites rapides, propres et faciles à maintenir.',
            'points' => 86,
            'joined_active' => 1,
            'profile_image' => 'https://images.unsplash.com/photo-1508341591423-4347099e1f19?auto=format&fit=crop&w=400&q=80',
            'contact_email' => 'yanis.leroy@neear-demo.fr',
            'contact_phone' => '07 22 33 44 55',
            'referrals_count' => 0,
        ],
        [
            'fullname' => 'Manon Faure',
            'email' => 'manon.faure@neear-demo.fr',
            'job' => 'Illustratrice',
            'bio' => 'J’apporte une dimension visuelle sensible aux projets créatifs.',
            'points' => 79,
            'joined_active' => 1,
            'profile_image' => 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&w=400&q=80',
            'contact_email' => 'manon.faure@neear-demo.fr',
            'contact_phone' => '07 33 44 55 66',
            'referrals_count' => 0,
        ],
    ];

    $defaultPassword = password_hash('demo1234', PASSWORD_DEFAULT);

    $checkUserStmt = $db->prepare('SELECT id FROM users WHERE email = ?');
    $insertUserStmt = $db->prepare(
        'INSERT INTO users (
            fullname, email, password, job, bio, points, badge, joined_active,
            profile_image, profile_image_file, contact_email, contact_phone, referrals_count
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );

    foreach ($fakeProfiles as $profile) {
        $checkUserStmt->execute([$profile['email']]);

        if (!$checkUserStmt->fetch()) {
            $insertUserStmt->execute([
                $profile['fullname'],
                $profile['email'],
                $defaultPassword,
                $profile['job'],
                $profile['bio'],
                $profile['points'],
                badgeFromPoints((int) $profile['points']),
                $profile['joined_active'],
                $profile['profile_image'],
                '',
                $profile['contact_email'],
                $profile['contact_phone'],
                $profile['referrals_count'],
            ]);
        }
    }

    $allUsers = $db->query('SELECT id, points FROM users')->fetchAll(PDO::FETCH_ASSOC);
    $updateBadgeStmt = $db->prepare('UPDATE users SET badge = ? WHERE id = ?');

    foreach ($allUsers as $user) {
        $updateBadgeStmt->execute([
            badgeFromPoints((int) $user['points']),
            (int) $user['id']
        ]);
    }
} catch (PDOException $e) {
    die('Erreur base de données : ' . $e->getMessage());
}

function avatarLetters(string $name): string
{
    $parts = preg_split('/\s+/', trim($name));
    $letters = '';

    foreach ($parts as $part) {
        if ($part !== '') {
            $letters .= strtoupper(substr($part, 0, 1));
        }
    }

    return substr($letters, 0, 2);
}

function splitUsersIntoRows(array $users): array
{
    $topRow = [];
    $bottomRow = [];

    foreach ($users as $index => $user) {
        if ($index % 2 === 0) {
            $topRow[] = $user;
        } else {
            $bottomRow[] = $user;
        }
    }

    return [$topRow, $bottomRow];
}

function badgeFromPoints(int $points): string
{
    if ($points < 50) {
        return 'Curieux';
    }

    if ($points <= 150) {
        return 'Pionnier';
    }

    return 'Ambassadeur';
}

function getUserComments(PDO $db, int $userId, int $limit = 4): array
{
    $stmt = $db->prepare(
        'SELECT author_name, comment, created_at
         FROM profile_comments
         WHERE target_user_id = ?
         ORDER BY id DESC
         LIMIT ?'
    );
    $stmt->bindValue(1, $userId, PDO::PARAM_INT);
    $stmt->bindValue(2, $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAllUserComments(PDO $db, int $userId): array
{
    $stmt = $db->prepare(
        'SELECT author_name, comment, created_at
         FROM profile_comments
         WHERE target_user_id = ?
         ORDER BY id DESC'
    );
    $stmt->execute([$userId]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getUserActionCount(PDO $db, int $userId): int
{
    $stmt = $db->prepare('SELECT COUNT(*) FROM user_point_actions WHERE user_id = ?');
    $stmt->execute([$userId]);
    $baseCount = (int) $stmt->fetchColumn();

    $stmt2 = $db->prepare('SELECT referrals_count FROM users WHERE id = ?');
    $stmt2->execute([$userId]);
    $referralsCount = (int) $stmt2->fetchColumn();

    return $baseCount + $referralsCount;
}