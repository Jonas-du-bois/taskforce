<?php

use M521\Taskforce\dbManager\DbManagerCRUD;

// Lien vers le fichier de fonction pour le multilingue
require_once('lang' . DIRECTORY_SEPARATOR . 'lang_func.php');

// Démarrage de la session si nécessaire
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Vérification et modification de la langue si un paramètre 'lang' est passé dans l'URL
if (isset($_GET['lang'])) {
    $lang = $_GET['lang'];
    if ($lang == 'fr' || $lang == 'en') {
        // Définir la langue dans la session
        $_SESSION['lang'] = $lang;
    }
}

// Vérification de l'utilisateur connecté
if (empty($_SESSION['user_connected']) || !$_SESSION['user_connected']) {
    header("Location: login.php");
    exit;
}

// Récupération des données utilisateur
$email = $_SESSION['email_user'] ?? null;
$dbManager = new DbManagerCRUD();
$users = $dbManager->getUser($email);
$userName = !empty($users) ? htmlspecialchars($users[0]->rendPrenom() . ' ' . $users[0]->rendNom()) : 'Utilisateur inconnu';

// Définition des liens de navigation
$navLinks = [
    'index.php' => '<i class="fas fa-home"></i> ' . t('homeSide'),
    'dashboard.php' => '<i class="fas fa-tachometer-alt"></i> ' . t('dashboardSide'),
    'ajouter_tache.php' => '<i class="fas fa-plus-circle"></i> ' . t('add_task'),
    'taches_a_faire.php' => '<i class="fas fa-list-ul"></i> ' . t('tasks_to_do'),
    'taches_en_cours.php' => '<i class="fas fa-spinner"></i> ' . t('tasks_in_progress'),
    'taches_terminees.php' => '<i class="fas fa-check-circle"></i> ' . t('tasks_completed'),
    'taches_partage.php' => '<i class="fas fa-share-alt"></i> ' . t('shared_tasks'),
];

// Ajout d'un lien admin si l'utilisateur est spécifique
if ($email === 'jonas.du.bois@outlook.com') {
    $navLinks['admin.php'] = '<i class="fas fa-user-shield"></i> ' . t('admin_panel');
}

?>

<!DOCTYPE html>
<html lang="<?php echo getLanguage(); ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style/sidebar.css">
    <link rel="stylesheet" href="style/stylesheet.css">
    <title>TaskForce</title>
</head>

    <!-- Sidebar -->
    <div class="col-md-3 col-lg-2 bg-dark text-white sidebar position-fixed vh-100 d-flex flex-column p-3 shadow-lg">
        <div class="border-bottom">
            <h4 class="mb-0 pt-3"><?php echo t('task_management'); ?></h4>
            <p class="mt-1"><?= $userName ?></p>
        </div>
        <ul class="nav nav-pills flex-column mb-auto pt-3">
            <?php foreach ($navLinks as $link => $label): ?>
                <?php $active = basename($_SERVER['PHP_SELF']) === $link ? 'active' : ''; ?>
                <li class="nav-item p-1">
                    <a href="<?= htmlspecialchars($link) ?>" class="nav-link text-white <?= $active ?>"><?= $label ?></a>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="mt-auto">
            <!-- Gestion du multilinguisme -->
            <div class="d-flex justify-content-between align-items-center p-1">
                <a href="?lang=<?php echo getLanguage() === 'en' ? 'fr' : 'en'; ?>" class="btn btn-outline-light btn-sm">
                    <?php echo getLanguage() === 'en' ? '<i class="bi bi-translate"></i> Français' : '<i class="bi bi-translate"></i> English'; ?>
                </a>
            </div>

            <!-- Liens de profil et déconnexion -->
            <div class="border-top pt-3">
                <a href="profile.php" class="nav-link text-white pb-3 <?= basename($_SERVER['PHP_SELF']) === 'profile.php' ? ' active' : '' ?>">
                    <i class="fas fa-user pe-2"></i> <?php echo t('my_profile'); ?>
                </a>
                <a href="logout.php" class="nav-link text-white pb-3">
                    <i class="fas fa-sign-out-alt pe-2"></i> <?php echo t('logoutSide'); ?>
                </a>
            </div>
        </div>
    </div>

    <!-- Spotlight -->
    <div class="overlay" id="overlay" aria-hidden="true"></div>
    <div class="spotlight" id="spotlight" role="dialog" aria-label="Recherche rapide">
        <form action="dashboard.php" method="GET">
            <input type="search" name="query" placeholder="Tapez pour rechercher..." aria-label="Recherche" autofocus />
        </form>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/spotlight.js"></script>
    <script src="js/function.js"></script>

</html>