<?php
// Démarre une session pour utiliser les variables $_SESSION (utilisées pour la gestion des utilisateurs)
session_start();

// Détermine la page actuelle
$current_page = basename($_SERVER['PHP_SELF']);

// Lien vers le fichier de fonction pour le multilangue
require_once('lang' . DIRECTORY_SEPARATOR . 'lang_func.php');

// Vérification et modification de la langue si un paramètre 'lang' est passé dans l'URL
if (isset($_GET['lang'])) {
    $lang = $_GET['lang'];
    if ($lang == 'fr' || $lang == 'en') {
        // Définir la langue dans la session
        $_SESSION['lang'] = $lang;
    }
}
?>

<!DOCTYPE html>
<html lang="<?php echo getLanguage(); ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TaskForce</title>
    <!-- Lien vers le fichier CSS personnalisé pour le style de la page -->
    <link rel="stylesheet" href="style/styles.css">
    <!-- Lien vers le fichier CSS de Bootstrap pour des styles prédéfinis et réactifs -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Lien vers le fichier JS de Bootstrap pour les interactions et le menu mobile -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>

    <!-- Barre de navigation -->
    <nav class="navbar navbar-expand-lg bg-dark navbar-dark">
        <div class="container">
            <!-- Logo ou titre de l'application, redirige vers la page d'accueil -->
            <a class="navbar-brand" href="index.php">TaskForce</a>

            <!-- Bouton pour le menu mobile (affichage des liens sur petits écrans) -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Liste des liens de navigation qui s'affichent dans le menu -->
            <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>" href="index.php"><?php echo t('home'); ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'HowTo.php') ? 'active' : ''; ?>" href="HowTo.php"><?php echo t('howTo'); ?></a>
                    </li>
                    <?php
                    if (isset($_SESSION['user_connected']) && $_SESSION['user_connected']) {
                        echo '<li class="nav-item"><a class="nav-link ' . ($current_page == 'dashboard.php' ? 'active' : '') . '" href="dashboard.php">' . t('dashboard') . '</a></li>';
                        echo '<li class="nav-item"><a class="nav-link ' . ($current_page == 'logout.php' ? 'active' : '') . '" href="logout.php">' . t('logout') . '</a></li>';
                    } else {
                        echo '<li class="nav-item"><a class="nav-link ' . ($current_page == 'login.php' ? 'active' : '') . '" href="login.php">' . t('login') . '</a></li>';
                        echo '<li class="nav-item"><a class="nav-link ' . ($current_page == 'signup.php' ? 'active' : '') . '" href="signup.php">' . t('signup') . '</a></li>';
                    }
                    ?>
                </ul>
            </div>

            <!-- Lien pour changer de langue -->
            <div class="d-flex align-items-center">
                <a href="?lang=<?php echo getLanguage() === 'en' ? 'fr' : 'en'; ?>" class="btn btn-outline-light btn-sm">
                    <?php echo getLanguage() === 'en' ? '<i class="bi bi-translate"></i> Français' : '<i class="bi bi-translate"></i> English'; ?>
                </a>
            </div>

        </div>
    </nav>

</body>

</html>
