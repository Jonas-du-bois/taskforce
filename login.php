<?php
/**
 * Page de connexion (login)
 * -------------------------
 * Gère le formulaire et la logique d’authentification d’un utilisateur.
 * Vérifie ses identifiants, démarre la session correspondante et redirige vers la page principale
 * en cas de succès. Affiche un message d’erreur en cas d’échec.
 */

// Inclusion de l'autoload pour charger les dépendances via Composer
require_once 'vendor\autoload.php';

// Importation de la classe DbManagerCRUD pour gérer les opérations sur la base de données
use M521\Taskforce\dbManager\DbManagerCRUD;

// Création d'une instance de DbManagerCRUD pour interagir avec la base de données
$dbUser = new DbManagerCRUD();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style/styleSheet.css">
    
    <title>login</title>
</head>

<body>
    <!-- Inclusion du header (barre de navigation, logo, etc.) -->
    <?php include('includes/header.php'); ?>

    <main class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h1 class="text-center mb-4"><?php echo t('login_heading'); ?></h1>
                
                <!-- Affichage du nombre d'utilisateurs inscrits -->
                <div class="alert alert-info text-center">
                    <p>
                        <?php echo $dbUser->countUsers(); ?> <?php echo t('user_count_message'); ?>
                        <br><strong><?php echo t('user_count_congratulations'); ?></strong>
                    </p>
                </div>

                <!-- Formulaire de connexion -->
                <form action="login.php" method="POST" class="p-4 border rounded bg-light">
                    <!-- Champ email -->
                    <div class="mb-3">
                        <label for="email" class="form-label"><?php echo t('email_label'); ?></label>
                        <input type="email" id="email" name="email" class="form-control" required placeholder="<?php echo t('email_placeholder'); ?>">
                    </div>

                    <!-- Champ mot de passe -->
                    <div class="mb-3">
                        <label for="password" class="form-label"><?php echo t('password_label'); ?></label>
                        <input type="password" id="password" name="password" class="form-control" required minlength="6" placeholder="<?php echo t('password_placeholder'); ?>">
                    </div>

                    <!-- Bouton de soumission -->
                    <button type="submit" name="login" class="btn btn-primary w-100"><?php echo t('login_button'); ?></button>
                </form>

                <div class="mt-3">
                    <?php
                    // Vérification si l'utilisateur est déjà connecté
                    if (isset($_SESSION['user_connected']) && $_SESSION['user_connected']) {
                        echo "<div class='alert alert-success text-center'>" . t('already_logged_in_message') . "</div>";
                    } else {
                        $_SESSION['user_connected'] = false; // Initialisation de la session si non connecté
                    }

                    // Traitement du formulaire après soumission
                    if (filter_has_var(INPUT_POST, 'login')) {
                        // Récupération des valeurs du formulaire avec validation
                        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
                        $motDePasse = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);

                        // Vérification de la validité des données
                        if ($email && strlen($motDePasse) >= 8) {
                            // Vérification des identifiants dans la base de données
                            $loginStatus = $dbUser->verifyCredentials($email, $motDePasse);

                            // Affichage des messages en fonction du statut de la connexion
                            if ($loginStatus === 'success') {
                                echo "<div class='alert alert-success'>" . t('success_message_login') . "</div>";
                                $_SESSION['user_connected'] = true; // Mise à jour de la session
                                $_SESSION['email_user'] = $email; // Stockage de l'email dans la session

                                // Redirection vers la page d'accueil après connexion
                                header('Location: dashboard.php');
                                exit();
                            } elseif ($loginStatus === 'not_confirmed') {
                                echo "<div class='alert alert-warning'>" . t('email_not_confirmed_error'). "</div>";
                            } elseif ($loginStatus === 'wrong_password') {
                                echo "<div class='alert alert-danger'>" . t('wrong_password_error') . "</div>";
                            } else {
                                echo "<div class='alert alert-danger'>" . t('email_not_found_error') . "</div>";
                            }
                        } else {
                            // Affichage d'un message d'erreur si les données sont invalides
                            echo "<div class='alert alert-danger'>" . t('invalid_data_error') . "</div>";
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </main>

    <!-- Intégration de Bootstrap JS pour le fonctionnement des composants interactifs -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Inclusion du footer (informations de bas de page) -->
    <?php include('includes/footer.php'); ?>
</body>

</html>
