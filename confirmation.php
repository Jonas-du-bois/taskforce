<?php

// Chargement des dépendances via Composer
require_once 'vendor/autoload.php';

use M521\Taskforce\dbManager\DbManagerCRUD;

// Initialisation du gestionnaire de base de données
$dbUser = new DbManagerCRUD();

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEJb3QJDKW8Q8f4C8E4z6Y5zFzUq7kz9T8F9LdzjM7x3G5K0VFS9c6E40PVja" crossorigin="anonymous">
    <title>Confirmation de l'inscription</title>
</head>

<body class="bg-light">
    <?php include('includes/header.php'); ?>
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card shadow-lg p-4" style="width: 400px;">
            <?php
            // Récupération et validation sécurisée du token depuis l'URL
            $token = filter_input(INPUT_GET, 'token', FILTER_SANITIZE_STRING);

            if ($token && preg_match('/^[a-zA-Z0-9]{32}$/', $token)) {
                // Vérification de l'utilisateur correspondant au token
                $user = $dbUser->getUserByToken($token);

                if ($user) {
                    // Confirmer l'inscription pour l'utilisateur
                    if ($dbUser->confirmeInscription($user['id'])) {
                        echo "<p class='alert alert-success'>Votre inscription a été confirmée avec succès !</p>";
                    } else {
                        echo "<p class='alert alert-danger'>Une erreur est survenue lors de la confirmation. Veuillez réessayer plus tard.</p>";
                    }
                } else {
                    echo "<p class='alert alert-danger'>Lien de confirmation invalide ou expiré.</p>";
                }
            } else {
                echo "<p class='alert alert-danger'>Token non valide ou absent. Veuillez vérifier le lien envoyé par e-mail.</p>";
            }
            ?>

            <!-- Bouton de retour -->
            <div class="mt-3 text-center">
                <a href="login.php" class="btn btn-primary">Retour à la page de connexion</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pzjw8f+ua7Kw1TIq0gbsYgX3p6bfK7MzDzQdJlIne8w6R6wDR6PZ9D4NB+HjlmS4" crossorigin="anonymous"></script>
    <?php include('includes/footer.php'); ?>
</body>

</html>
