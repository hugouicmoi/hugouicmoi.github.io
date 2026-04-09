<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Neear - Communauté</title>
<link rel="stylesheet" href="style.css">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
</head>
<body>

<div class="topbar">
    Parrainez un proche et obtenez-lui sa carte Neear offerte. En retour, bénéficiez de 20% de réduction sur votre abonnement annuel.
</div>

<header class="header">
    <div class="logo">
        <img src="assets/logo-neear.png" alt="Logo Neear">
    </div>

    <nav class="nav">
        <a href="#">Accueil</a>
        <a href="#profils">Profils</a>
        <a href="#avis">Avis</a>
        <a href="#posts">Posts</a>
    </nav>

    <div class="header-actions">
        <button class="score-btn" id="openScoreModal" type="button">Score</button>

        <?php if ($currentUser): ?>
            <a class="join-btn" href="?logout=1">Déconnexion</a>
        <?php else: ?>
            <button class="join-btn" id="openAuth" type="button">Rejoindre</button>
        <?php endif; ?>
    </div>
</header>

<section class="hero">
    <div class="hero-left">
        <span class="pill pill-light">COMMUNAUTÉ NEEAR</span>

        <h1>
            Le mur vivant<br>
            dédié aux<br>
            <span class="accent">bâtisseurs</span> de la<br>
            communauté<br>
            Neear.
        </h1>

        <p>
            Vous nous aidez à construire Neear.<br>
            On veut mettre la page sous votre lumière.
        </p>

        <div class="hero-buttons">
            <a href="#profils" class="dark-btn">Voir les profils</a>
            <a href="#avis" class="yellow-btn">Lire les avis</a>
        </div>
    </div>

    <div class="hero-right">
        <div class="profile-card">
            <div class="profile-top-action">
                <?= (int) $heroActionCount ?> action<?= $heroActionCount > 1 ? 's' : '' ?>
            </div>

            <div class="profile-header">
<?php $canCustomizeHeroFrame = $currentUser && (int) $currentUser['id'] === (int) $heroId; ?>

<?php if ($canCustomizeHeroFrame): ?>
    <button
        type="button"
        class="hero-avatar-button"
        id="heroAvatarButton"
        data-user-id="<?= (int) $heroId ?>"
        aria-label="Changer le cadre de la photo de profil"
    >
        <div class="hero-avatar-stack">
            <?php if (!empty($heroImage)): ?>
                <img
                    src="<?= htmlspecialchars($heroImage) ?>"
                    alt="<?= htmlspecialchars($heroName) ?>"
                    class="profile-photo big-avatar-photo hero-avatar-base"
                >
            <?php else: ?>
                <div class="avatar-circle big-avatar hero-avatar-base"><?= avatarLetters($heroName) ?></div>
            <?php endif; ?>

            <img
                src=""
                alt=""
                class="hero-avatar-frame"
                id="heroAvatarFrame"
            >
        </div>
    </button>
<?php else: ?>
    <?php if (!empty($heroImage)): ?>
        <img src="<?= htmlspecialchars($heroImage) ?>" alt="<?= htmlspecialchars($heroName) ?>" class="profile-photo big-avatar-photo">
    <?php else: ?>
        <div class="avatar-circle big-avatar"><?= avatarLetters($heroName) ?></div>
    <?php endif; ?>
<?php endif; ?>

<div>
    <h3><?= htmlspecialchars($heroName) ?></h3>
    <p><?= htmlspecialchars($heroJob) ?></p>
</div>
</div>

            <div class="profile-tags">
                <span><?= htmlspecialchars($heroBadge) ?></span>
                <span><?= (int) $heroPoints ?> points</span>
            </div>

            <div class="profile-about">
                <strong>À propos</strong>
                <p><?= htmlspecialchars($heroBio) ?></p>
            </div>

            <?php if ($currentUser && (int) $currentUser['id'] === $heroId): ?>
                <button type="button" class="edit-profile-btn" data-open-modal="editProfileModal">
                    Modifier mon profil
                </button>
            <?php endif; ?>

            <div class="profile-actions">
                <button
                    type="button"
                    class="js-open-comment"
                    data-user-id="<?= (int) $heroId ?>"
                    data-user-name="<?= htmlspecialchars($heroName) ?>"
                >
                    Commentaire
                </button>

                <button
                    type="button"
                    class="js-open-contact"
                    data-user-id="<?= (int) $heroId ?>"
                    data-user-name="<?= htmlspecialchars($heroName) ?>"
                    data-user-job="<?= htmlspecialchars($heroJob) ?>"
                    data-user-email="<?= htmlspecialchars($heroContactEmail) ?>"
                    data-user-phone="<?= htmlspecialchars($heroContactPhone) ?>"
                >
                    Contacter
                </button>

                <button type="button" class="profile-action-link js-open-score">
                    Action
                </button>
            </div>

            <?php
                $heroAllComments = $heroId > 0 ? getAllUserComments($db, $heroId) : [];
                $heroLastComments = array_slice($heroAllComments, 0, 3);
            ?>
            <?php if (!empty($heroLastComments)): ?>
                <div class="profile-comments-preview">
                    <strong>Derniers commentaires</strong>

                    <?php foreach ($heroLastComments as $comment): ?>
                        <div class="comment-preview-item">
                            <h5><?= htmlspecialchars($comment['author_name']) ?></h5>
                            <p><?= htmlspecialchars($comment['comment']) ?></p>
                        </div>
                    <?php endforeach; ?>

                    <?php if (count($heroAllComments) > 3): ?>
                        <button
                            type="button"
                            class="see-more-comments-btn js-open-all-comments"
                            data-comments='<?= htmlspecialchars(json_encode($heroAllComments, JSON_UNESCAPED_UNICODE), ENT_QUOTES, "UTF-8") ?>'
                            data-user-name="<?= htmlspecialchars($heroName) ?>"
                        >
                            Voir plus
                        </button>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="how-section">
    <span class="pill">COMMENT APPARAÎTRE</span>

    <h2>Comment gagner sa<br>place sur Neear ?</h2>

    <p>
        Chaque action compte. Plus vous participez à faire connaître, améliorer ou recommander Neear,
        plus vous gagnez en visibilité.
    </p>

    <div class="how-grid">
        <div class="how-card">
            <h4>Parrainer</h4>
            <p>Invitez un nouveau membre</p>
            <span>+50 pts</span>
        </div>

        <div class="how-card">
            <h4>Donner un avis</h4>
            <p>Partagez votre expérience</p>
            <span>+30 pts</span>
        </div>

        <div class="how-card">
            <h4>Partager</h4>
            <p>Parlez de Neear</p>
            <span>+20 pts</span>
        </div>

        <div class="how-card">
            <h4>Feedback</h4>
            <p>Aidez à améliorer</p>
            <span>+15 pts</span>
        </div>
    </div>
</section>

<section class="top-members dotted-bg-light">
    <span class="pill">TOP MEMBRES</span>

    <h2>Les membres les plus<br>engagés Neear.</h2>
    <p class="top-members-subtitle">
        Récompenses du leaderboard : <br>
        le premier gagne -20% de réduction, <br>
        le deuxième gagne -15% de réduction, <br>
        et le troisième gagne -10% de réduction
    </p>

    <div class="top-cards">
        <?php if (count($topMembers) >= 3): ?>
            <?php foreach ($topMembers as $index => $member): ?>
                <?php $rank = $index + 1; ?>
                <?php
                    $memberImage = !empty($member['profile_image_file'])
                        ? $member['profile_image_file']
                        : ($member['profile_image'] ?? '');
                ?>
                <div class="top-card rank-<?= $rank ?>">
                    <div class="rank-label">#<?= $rank ?></div>

                    <?php if (!empty($memberImage)): ?>
                        <img src="<?= htmlspecialchars($memberImage) ?>" alt="<?= htmlspecialchars($member['fullname']) ?>" class="profile-photo top-photo">
                    <?php else: ?>
                        <div class="avatar-circle"><?= avatarLetters($member['fullname']) ?></div>
                    <?php endif; ?>

                    <h4><?= htmlspecialchars($member['fullname']) ?></h4>
                    <p><?= htmlspecialchars($member['job']) ?></p>
                    <span><?= (int) $member['points'] ?> pts</span>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <?php foreach ($defaultTop as $index => $member): ?>
                <div class="top-card rank-<?= $index + 1 ?>">
                    <div class="rank-label">#<?= $index + 1 ?></div>
                    <div class="avatar-circle"><?= avatarLetters($member['fullname']) ?></div>
                    <h4><?= htmlspecialchars($member['fullname']) ?></h4>
                    <p><?= htmlspecialchars($member['job']) ?></p>
                    <span><?= (int) $member['points'] ?> pts</span>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<section class="active-section" id="profils">
    <div class="active-left">
        <span class="pill pill-light">LES PROFILS</span>

        <h2>
            Les profils actifs<br>
            de la<br>
            <span class="accent">communauté</span><br>
            Neear.
        </h2>

        <p>
            Découvrez les profils créateurs et membres qui font vivre la communauté.
        </p>

        <div class="profiles-actions">
            <?php if ($currentUser): ?>
                <?php if (!$currentUser['joined_active']): ?>
                    <a href="?join=1" class="dark-btn">Rejoindre les profils</a>
                <?php else: ?>
                    <span class="joined-badge">Vous faites partie des profils actifs</span>
                <?php endif; ?>
            <?php else: ?>
                <button class="dark-btn" id="openAuth2" type="button">Rejoindre les profils</button>
            <?php endif; ?>

            <button type="button" class="yellow-btn search-profile-btn" data-open-modal="searchProfileModal">
                Rechercher un profil
            </button>
        </div>
    </div>

    <div class="active-right active-carousel-wrap">
        <div class="carousel-row row-top">
            <div class="carousel-track">
                <?php foreach (array_merge($topRowUsers, $topRowUsers) as $user): ?>
                    <?php
                        $userImage = !empty($user['profile_image_file'])
                            ? $user['profile_image_file']
                            : ($user['profile_image'] ?? '');
                    ?>

                    <button
                        type="button"
                        class="mini-profile js-open-user-profile"
                        data-user-id="<?= (int) $user['id'] ?>"
                        data-user-name="<?= htmlspecialchars($user['fullname']) ?>"
                        data-user-job="<?= htmlspecialchars($user['job']) ?>"
                        data-user-bio="<?= htmlspecialchars($user['bio']) ?>"
                        data-user-badge="<?= htmlspecialchars(badgeFromPoints((int) $user['points'])) ?>"
                        data-user-points="<?= (int) $user['points'] ?>"
                        data-user-email="<?= htmlspecialchars($user['contact_email']) ?>"
                        data-user-phone="<?= htmlspecialchars($user['contact_phone']) ?>"
                        data-user-image="<?= htmlspecialchars($userImage) ?>"
                        data-user-comments='<?= htmlspecialchars(json_encode($commentsByUser[$user["id"]] ?? [], JSON_UNESCAPED_UNICODE), ENT_QUOTES, "UTF-8") ?>'
                    >
                        <?php if (!empty($userImage)): ?>
                            <img src="<?= htmlspecialchars($userImage) ?>" alt="<?= htmlspecialchars($user['fullname']) ?>" class="profile-photo mini-photo">
                        <?php else: ?>
                            <div class="avatar-circle small"><?= avatarLetters($user['fullname']) ?></div>
                        <?php endif; ?>

                        <h4><?= htmlspecialchars($user['fullname']) ?></h4>
                        <p><?= htmlspecialchars($user['job']) ?></p>

                        <div class="mini-meta">
                            <span><?= htmlspecialchars(badgeFromPoints((int) $user['points'])) ?></span>
                            <span><?= (int) $user['points'] ?> pts</span>
                        </div>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="carousel-row row-bottom">
            <div class="carousel-track">
                <?php foreach (array_merge($bottomRowUsers, $bottomRowUsers) as $user): ?>
                    <?php
                        $userImage = !empty($user['profile_image_file'])
                            ? $user['profile_image_file']
                            : ($user['profile_image'] ?? '');
                    ?>

                    <button
                        type="button"
                        class="mini-profile js-open-user-profile"
                        data-user-id="<?= (int) $user['id'] ?>"
                        data-user-name="<?= htmlspecialchars($user['fullname']) ?>"
                        data-user-job="<?= htmlspecialchars($user['job']) ?>"
                        data-user-bio="<?= htmlspecialchars($user['bio']) ?>"
                        data-user-badge="<?= htmlspecialchars(badgeFromPoints((int) $user['points'])) ?>"
                        data-user-points="<?= (int) $user['points'] ?>"
                        data-user-email="<?= htmlspecialchars($user['contact_email']) ?>"
                        data-user-phone="<?= htmlspecialchars($user['contact_phone']) ?>"
                        data-user-image="<?= htmlspecialchars($userImage) ?>"
                        data-user-comments='<?= htmlspecialchars(json_encode($commentsByUser[$user["id"]] ?? [], JSON_UNESCAPED_UNICODE), ENT_QUOTES, "UTF-8") ?>'
                    >
                        <?php if (!empty($userImage)): ?>
                            <img src="<?= htmlspecialchars($userImage) ?>" alt="<?= htmlspecialchars($user['fullname']) ?>" class="profile-photo mini-photo">
                        <?php else: ?>
                            <div class="avatar-circle small"><?= avatarLetters($user['fullname']) ?></div>
                        <?php endif; ?>

                        <h4><?= htmlspecialchars($user['fullname']) ?></h4>
                        <p><?= htmlspecialchars($user['job']) ?></p>

                        <div class="mini-meta">
                            <span><?= htmlspecialchars(badgeFromPoints((int) $user['points'])) ?></span>
                            <span><?= (int) $user['points'] ?> pts</span>
                        </div>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<section class="reviews-section" id="avis">
    <span class="pill">LES AVIS</span>

    <div class="reviews-head">
        <div>
            <h2>Ce que les utilisateurs<br>pensent vraiment.</h2>
            <p>Consultez les avis laissés par les utilisateurs et les moteurs de la communauté.</p>
        </div>

        <a
            class="dark-btn small-btn"
            href="https://search.google.com/local/writereview?placeid=ChIJ_06a56OvthIRHA1d6rBPwa0"
            target="_blank"
            rel="noopener"
        >
            Rédiger un avis
        </a>
    </div>

    <div class="reviews-carousel">
        <div class="reviews-track">
            <?php foreach (array_merge($reviews, $reviews) as $review): ?>
                <button
                    type="button"
                    class="review-card js-open-review"
                    data-review-title="<?= htmlspecialchars($review['title']) ?>"
                    data-review-text="<?= htmlspecialchars($review['text']) ?>"
                    data-review-source="<?= htmlspecialchars($review['source']) ?>"
                >
                    <div class="stars">★★★★★</div>
                    <h4><?= htmlspecialchars($review['title']) ?></h4>
                    <p><?= htmlspecialchars($review['text']) ?></p>
                    <span><?= htmlspecialchars($review['source']) ?></span>
                </button>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="posts-section dotted-bg" id="posts">
    <span class="pill">LES POSTS</span>

    <h2>Les posts de la<br>communauté Neear.</h2>

    <p>
        Explorez les opinions partagées, les membres qui aident et jugez l’activité de la communauté.
    </p>

    <div class="posts-fall-wrap" id="postsFallWrap">
        <?php foreach ($posts as $index => $post): ?>
            <article
                class="post-card falling-post js-open-post"
                data-index="<?= $index ?>"
                data-post-author="<?= htmlspecialchars($post['author']) ?>"
                data-post-handle="<?= htmlspecialchars($post['handle']) ?>"
                data-post-job="<?= htmlspecialchars($post['job']) ?>"
                data-post-content="<?= htmlspecialchars($post['content']) ?>"
                data-post-tag="<?= htmlspecialchars($post['tag']) ?>"
            >
                <div class="post-top">
                    <div class="avatar-circle post-avatar"><?= avatarLetters($post['author']) ?></div>

                    <div class="post-author-block">
                        <h4><?= htmlspecialchars($post['author']) ?></h4>
                        <p><?= htmlspecialchars($post['job']) ?> · <?= htmlspecialchars($post['handle']) ?></p>
                    </div>

                    <span class="post-tag"><?= htmlspecialchars($post['tag']) ?></span>
                </div>

                <div class="post-content">
                    <?= htmlspecialchars($post['content']) ?>
                </div>

                <div class="post-footer">
                    <span>#Neear</span>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<footer class="footer footer-v2">
    <div class="footer-card footer-mission-card">
        <h3>Notre mission</h3>

        <p>
            Nous sommes trois fondateurs convaincus qu'une poignée de main
            peut tout changer. Neear, c'est notre réponse à toutes les rencontres
            qu'on oublie, les contacts qu'on égare et les opportunités qui
            s'évaporent. Transformer chaque rencontre en opportunité, c'est
            notre obsession.
        </p>

        <div class="footer-founders">Matthieu, Baptiste & Edith</div>

        <div class="footer-socials">
            <a href="https://www.linkedin.com/company/neear/" target="_blank" rel="noopener" aria-label="LinkedIn">
                <img src="assets/LinkedIn_icon.png" alt="LinkedIn">
            </a>
            <a href="https://www.instagram.com/neear.io/?hl=fr%2F" target="_blank" rel="noopener" aria-label="Instagram">
                <img src="assets/Instagram_icon.webp" alt="Instagram">
            </a>
            <a href="https://www.tiktok.com/@neear.io" target="_blank" rel="noopener" aria-label="TikTok">
                <img src="assets/Tiktok_icon.webp" alt="TikTok">
            </a>
        </div>

        <div class="footer-big-logo">
            <img src="assets/white-logo-neear.png" alt="Logo Neear">
        </div>
    </div>

    <div class="footer-card footer-links-card">
        <div class="newsletter-top-box">
            <h3>Tips, promos et nouveautés<br>dans votre boîte mail</h3>

            <form method="POST" action="index.php" class="newsletter-form-v2">
                <input
                    type="email"
                    name="newsletter_email"
                    placeholder="bonjour@maboite.com"
                    required
                >
                <button type="submit" name="subscribe_newsletter" aria-label="S'inscrire à la newsletter">→</button>
            </form>

            <?php if (!empty($newsletterError)): ?>
                <div class="footer-message footer-message-error"><?= htmlspecialchars($newsletterError) ?></div>
            <?php endif; ?>

            <?php if (!empty($newsletterMessage)): ?>
                <div class="footer-message footer-message-success"><?= htmlspecialchars($newsletterMessage) ?></div>
            <?php endif; ?>
        </div>

        <div class="footer-columns">
            <div>
                <h4>NEEAR</h4>
                <a href="https://www.neear.fr/" target="_blank" rel="noopener">Accueil</a>
                <a href="https://neear.gitbook.io/neear-docs" target="_blank" rel="noopener">Documentation</a>
                <a href="https://www.neear.fr/contact" target="_blank" rel="noopener">Contact</a>
                <a href="https://www.neear.io/auth/login" target="_blank" rel="noopener">Se connecter</a>
                <a href="https://www.neear.io/auth/signup" target="_blank" rel="noopener">Créer un compte</a>
            </div>

            <div>
                <h4>LEGAL</h4>
                <a href="https://www.neear.fr/mentions-legales" target="_blank" rel="noopener">Mentions légales</a>
                <a href="https://www.neear.fr/conditions-generales-d-utilisation" target="_blank" rel="noopener">CGU</a>
                <a href="https://www.neear.fr/conditions-generales-de-vente" target="_blank" rel="noopener">CGV</a>
                <a href="https://www.neear.fr/cookies" target="_blank" rel="noopener">Cookies</a>
                <a href="https://www.neear.fr/politique-de-confidentialite" target="_blank" rel="noopener">Politique de confidentialité</a>
            </div>
        </div>

        <small>© 2026 Neear. Tous droits réservés.</small>
    </div>
</footer>

<?php if (!$currentUser): ?>
    <div class="modal" id="authModal">
        <div class="modal-box">
            <button class="close-modal" id="closeAuth" type="button">×</button>

            <div class="auth-switch">
                <button type="button" class="auth-switch-btn <?= $authTab === 'login' ? 'active' : '' ?>" id="showLogin">
                    Connexion
                </button>

                <button type="button" class="auth-switch-btn <?= $authTab === 'register' ? 'active' : '' ?>" id="showRegister">
                    Créer un compte
                </button>
            </div>

            <div class="auth-panels">
                <div class="auth-panel <?= $authTab === 'login' ? 'active' : '' ?>" id="loginPanel">
                    <div class="auth-block single">
                        <h3>Se connecter</h3>
                        <p class="auth-subtext">Connectez-vous pour rejoindre les profils actifs de la communauté Neear.</p>

                        <form method="POST" action="index.php">
                            <input type="email" name="login_email" placeholder="Email" required>
                            <input type="password" name="login_password" placeholder="Mot de passe" required>
                            <button type="submit" name="login" class="yellow-btn auth-btn full-btn">Se connecter</button>
                        </form>
                    </div>
                </div>

                <div class="auth-panel <?= $authTab === 'register' ? 'active' : '' ?>" id="registerPanel">
                    <div class="auth-block single">
                        <h3>Créer un compte</h3>
                        <p class="auth-subtext">Créez votre profil pour apparaître dans la communauté Neear.</p>

                        <form method="POST" action="index.php">
                            <input type="text" name="fullname" placeholder="Nom complet" required>
                            <input type="text" name="job" placeholder="Métier" required>
                            <input type="email" name="email" placeholder="Email" required>
                            <input type="password" name="password" placeholder="Mot de passe" required>
                            <button type="submit" name="register" class="yellow-btn auth-btn full-btn">S'inscrire</button>
                        </form>
                    </div>
                </div>
            </div>

            <?php if (!empty($error)): ?>
                <div class="message error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="message success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <script>
                window.defaultAuthTab = "<?= htmlspecialchars($authTab) ?>";
            </script>
        </div>
    </div>
<?php endif; ?>

<div class="modal" id="editProfileModal">
    <div class="modal-box profile-modal-box">
        <button class="close-modal" data-close-modal="editProfileModal" type="button">×</button>
        <h3>Modifier mon profil</h3>

        <form method="POST" action="index.php" class="profile-form-grid" enctype="multipart/form-data">
            <input type="text" name="job" placeholder="Métier" value="<?= htmlspecialchars($currentUser['job'] ?? '') ?>" required>
            <input type="text" name="profile_image" placeholder="URL de la photo de profil (optionnel)" value="<?= htmlspecialchars($currentUser['profile_image'] ?? '') ?>">

            <div class="file-upload-field">
                <label for="profile_image_upload">Ou télécharge une photo (PNG ou JPEG)</label>
                <input type="file" name="profile_image_upload" id="profile_image_upload" accept=".png,.jpg,.jpeg,image/png,image/jpeg">
            </div>

            <input type="email" name="contact_email" placeholder="Email de contact" value="<?= htmlspecialchars($currentUser['contact_email'] ?? '') ?>">
            <input type="text" name="contact_phone" placeholder="Téléphone" value="<?= htmlspecialchars($currentUser['contact_phone'] ?? '') ?>">
            <textarea name="bio" placeholder="À propos" required><?= htmlspecialchars($currentUser['bio'] ?? '') ?></textarea>
            <button type="submit" name="update_profile" class="yellow-btn full-btn">Enregistrer</button>
        </form>

        <?php if (!empty($profileError)): ?>
            <div class="message error"><?= htmlspecialchars($profileError) ?></div>
        <?php endif; ?>

        <?php if (!empty($profileMessage)): ?>
            <div class="message success"><?= htmlspecialchars($profileMessage) ?></div>
        <?php endif; ?>
    </div>
</div>

<div class="modal" id="commentModal">
    <div class="modal-box profile-modal-box">
        <button class="close-modal" data-close-modal="commentModal" type="button">×</button>
        <h3>Laisser un commentaire</h3>
        <p class="modal-subtitle">Pour <span id="commentTargetName">ce profil</span></p>

        <form method="POST" action="index.php" class="profile-form-grid">
            <input type="hidden" name="target_user_id" id="commentTargetUserId">
            <input type="text" name="author_name" placeholder="Votre nom" required>
            <textarea name="comment" placeholder="Votre commentaire" required></textarea>
            <button type="submit" name="add_comment" class="yellow-btn full-btn">Publier le commentaire</button>
        </form>

        <?php if (!empty($commentError)): ?>
            <div class="message error"><?= htmlspecialchars($commentError) ?></div>
        <?php endif; ?>

        <?php if (!empty($commentMessage)): ?>
            <div class="message success"><?= htmlspecialchars($commentMessage) ?></div>
        <?php endif; ?>
    </div>
</div>

<div class="modal" id="contactModal">
    <div class="modal-box profile-modal-box">
        <button class="close-modal" data-close-modal="contactModal" type="button">×</button>
        <h3>Ajouter ce contact</h3>
        <p class="modal-subtitle">Enregistrer les coordonnées de <span id="contactTargetName">ce profil</span></p>

        <form method="POST" action="index.php" class="profile-form-grid">
            <input type="hidden" name="target_user_id" id="contactTargetUserId">
            <input type="text" name="saved_name" id="contactSavedName" placeholder="Nom" required>
            <input type="text" name="saved_job" id="contactSavedJob" placeholder="Métier">
            <input type="email" name="saved_email" id="contactSavedEmail" placeholder="Email">
            <input type="text" name="saved_phone" id="contactSavedPhone" placeholder="Téléphone">
            <button type="submit" name="save_contact" class="yellow-btn full-btn">Enregistrer le contact</button>
        </form>

        <?php if (!empty($contactError)): ?>
            <div class="message error"><?= htmlspecialchars($contactError) ?></div>
        <?php endif; ?>

        <?php if (!empty($contactMessage)): ?>
            <div class="message success"><?= htmlspecialchars($contactMessage) ?></div>
        <?php endif; ?>
    </div>
</div>

<div class="modal" id="userProfileModal">
    <div class="modal-box user-detail-box">
        <button class="close-modal" data-close-modal="userProfileModal" type="button">×</button>

        <div class="user-detail-header">
            <div id="userProfileAvatarWrap"></div>
            <div>
                <h3 id="userProfileName">Nom</h3>
                <p id="userProfileJob">Métier</p>
            </div>
        </div>

        <div class="user-detail-tags">
            <span id="userProfileBadge">Badge</span>
            <span id="userProfilePoints">0 points</span>
        </div>

        <div class="user-detail-about">
            <strong>À propos</strong>
            <p id="userProfileBio"></p>
        </div>

        <div class="user-detail-contact">
            <strong>Contact</strong>
            <p>Email : <span id="userProfileEmail">-</span></p>
            <p>Téléphone : <span id="userProfilePhone">-</span></p>
        </div>

        <div class="user-detail-comments">
            <strong>Commentaires</strong>
            <div id="userProfileCommentsList"></div>
            <button type="button" id="userProfileSeeMoreComments" class="see-more-comments-btn" style="display:none;">
                Voir plus
            </button>
        </div>

        <div class="detail-actions">
            <button type="button" class="dark-btn js-comment-from-profile">Commenter</button>
            <button type="button" class="yellow-btn js-contact-from-profile">Ajouter le contact</button>
        </div>
    </div>
</div>

<div class="modal" id="searchProfileModal">
    <div class="modal-box profile-modal-box">
        <button class="close-modal" data-close-modal="searchProfileModal" type="button">×</button>
        <h3>Rechercher un profil</h3>
        <p class="modal-subtitle">Trouve un membre de la communauté puis ouvre sa fiche.</p>

        <div class="search-profile-box">
            <input
                type="text"
                id="profileSearchInput"
                placeholder="Recherche par nom, métier, bio ou badge..."
                autocomplete="off"
            >
            <div id="profileSearchResults" class="search-results-list"></div>
        </div>
    </div>
</div>

<div class="modal" id="scoreModal">
    <div class="modal-box score-box">
        <button class="close-modal" id="closeScoreModal" type="button">×</button>

        <h3 class="score-title">Comment avoir des points ?</h3>

        <div class="score-list">
            <?php foreach ($pointActions as $actionKey => $actionData): ?>
                <?php $alreadyClaimed = $actionKey === 'signup' && in_array($actionKey, $claimedActionKeys, true); ?>

                <form method="POST" action="index.php" class="score-item-form">
                    <input type="hidden" name="claim_points" value="1">
                    <input type="hidden" name="action_key" value="<?= htmlspecialchars($actionKey) ?>">

                    <button
                        type="submit"
                        class="score-item <?= $alreadyClaimed ? 'claimed' : '' ?>"
                        <?= $alreadyClaimed ? 'disabled' : '' ?>
                    >
                        <span class="score-item-label"><?= htmlspecialchars($actionData['label']) ?></span>
                        <span class="score-item-points"><?= $alreadyClaimed ? 'Validé' : '+' . (int) $actionData['points'] ?></span>
                    </button>
                </form>
            <?php endforeach; ?>
        </div>

        <?php if (!empty($scoreError)): ?>
            <div class="message error"><?= htmlspecialchars($scoreError) ?></div>
        <?php endif; ?>

        <?php if (!empty($scoreMessage)): ?>
            <div class="message success"><?= htmlspecialchars($scoreMessage) ?></div>
        <?php endif; ?>
    </div>
</div>

<div class="modal" id="postDetailModal">
    <div class="modal-box content-detail-box">
        <button class="close-modal" data-close-modal="postDetailModal" type="button">×</button>
        <div class="content-detail-head">
            <span class="content-detail-pill" id="postDetailTag">Post</span>
            <h3 id="postDetailAuthor">Auteur</h3>
            <p id="postDetailMeta">Métier · @handle</p>
        </div>
        <div class="content-detail-body">
            <p id="postDetailContent"></p>
        </div>
    </div>
</div>

<div class="modal" id="reviewDetailModal">
    <div class="modal-box content-detail-box">
        <button class="close-modal" data-close-modal="reviewDetailModal" type="button">×</button>
        <div class="content-detail-head">
            <div class="stars">★★★★★</div>
            <h3 id="reviewDetailTitle">Titre</h3>
            <p id="reviewDetailSource">Source</p>
        </div>
        <div class="content-detail-body">
            <p id="reviewDetailContent"></p>
        </div>
    </div>
</div>

<div class="modal" id="allCommentsModal">
    <div class="modal-box profile-modal-box">
        <button class="close-modal" data-close-modal="allCommentsModal" type="button">×</button>
        <h3 id="allCommentsTitle">Tous les commentaires</h3>
        <div id="allCommentsList" class="all-comments-list"></div>
    </div>
</div>

<script>
    window.openScoreAfterLoad = <?= (!empty($scoreError) || !empty($scoreMessage)) ? 'true' : 'false' ?>;
    window.openProfileEditAfterLoad = <?= (!empty($profileError) || !empty($profileMessage)) ? 'true' : 'false' ?>;
    window.openCommentAfterLoad = <?= (!empty($commentError) || !empty($commentMessage)) ? 'true' : 'false' ?>;
    window.openContactAfterLoad = <?= (!empty($contactError) || !empty($contactMessage)) ? 'true' : 'false' ?>;
    window.searchProfiles = JSON.parse(<?= json_encode(json_encode($searchProfiles, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) ?>);
</script>

<script src="script.js"></script>
</body>
</html>