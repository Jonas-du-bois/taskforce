<?php
/*
 * Page du profil utilisateur
 * -------------------------
 * Cette page permet à un utilisateur connecté de consulter et modifier ses informations personnelles
 * (prénom, nom, email, numéro de téléphone, mot de passe). 
 */

require_once 'vendor/autoload.php';

use M521\Taskforce\dbManager\DbManagerCRUD;
use M521\Taskforce\dbManager\Users;

// Création d'une instance du gestionnaire de base de données
$dbManager = new DbManagerCRUD();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="style/styleSheet.css" rel="stylesheet">
</head>

<body>
    <div class="d-flex">
        <!-- Inclusion de la barre latérale -->
        <?php include 'includes/sidebar.php'; ?>

        <?php
        // Récupération de l'email de l'utilisateur connecté à partir de la session
        $userEmail = $_SESSION['email_user'];

        // Récupération des informations de l'utilisateur à partir de la base de données
        $userArray = $dbManager->getUser($userEmail);
        if (empty($userArray)) {
            die(t('user_not_found')); // Si l'utilisateur n'est pas trouvé, on arrête l'exécution
        }

        // L'utilisateur est unique, récupération de ses informations
        $userInfo = $userArray[0];

        // Définition des expressions régulières pour valider les champs
        $nameRegex = "/^[a-zA-ZÀ-ÿ' -]{3,20}$/";  
        // 3 à 20 caractères, lettres, espaces, apostrophes, tirets
        $phoneRegex = "/^\+?[0-9]{10,15}$/";  
        // Téléphone : 10 à 15 chiffres (format international ou local)
        $passwordRegex = "/^(?=.*[A-Z])(?=.*[\W_])(?=.{8,})/";
        // Mot de passe : min. 8 caractères, 1 majuscule et 1 caractère spécial

        // Tableau pour collecter les erreurs
        $errors = [];
        ?>

        <div class="main-content ms-auto col-md-9 col-lg-10 p-4">
            <main class="container py-5">
                <div class="p-4">
                    <h1 class="text-center mb-4"><?= t('profile_header'); ?></h1>

                    <!-- Formulaire de mise à jour du profil -->
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="prenom" class="form-label"><?= t('first_name'); ?></label>
                            <input 
                                type="text" 
                                class="form-control" 
                                id="prenom" 
                                name="prenom" 
                                value="<?= htmlspecialchars($userInfo->rendPrenom()) ?>" 
                                required
                            >
                        </div>
                        <div class="mb-3">
                            <label for="nom" class="form-label"><?= t('last_name'); ?></label>
                            <input 
                                type="text" 
                                class="form-control" 
                                id="nom" 
                                name="nom" 
                                value="<?= htmlspecialchars($userInfo->rendNom()) ?>" 
                                required
                            >
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label"><?= t('email'); ?></label>
                            <input 
                                type="email" 
                                class="form-control" 
                                id="email" 
                                name="email" 
                                value="<?= htmlspecialchars($userInfo->rendEmail()) ?>" 
                                required
                            >
                        </div>
                        <div class="mb-3">
                            <label for="noTel" class="form-label"><?= t('phone_number'); ?></label>
                            <input 
                                type="tel" 
                                class="form-control" 
                                id="noTel" 
                                name="noTel" 
                                value="<?= htmlspecialchars($userInfo->rendNoTel()) ?>" 
                                required
                            >
                        </div>
                        <div class="mb-3">
                            <label for="motDePasse" class="form-label"><?= t('password'); ?></label>
                            <input 
                                type="password" 
                                class="form-control" 
                                id="motDePasse" 
                                name="motDePasse"
                                placeholder="<?= t('password_placeholder'); ?>"
                            >
                        </div>
                        <button type="submit" class="btn btn-primary"><?= t('update_button'); ?></button>
                    </form>

                    <?php
                    // Traitement du formulaire
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        try {
                            // Sécurisation / conversion des champs
                            $firstName = htmlspecialchars(trim($_POST['prenom']));
                            $lastName  = htmlspecialchars(trim($_POST['nom']));
                            $email     = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);

                            if (!$email) {
                                $errors[] = t('invalid_email');
                            }
                            if (!preg_match($nameRegex, $firstName)) {
                                $errors[] = t('invalid_first_name');
                            }
                            if (!preg_match($nameRegex, $lastName)) {
                                $errors[] = t('invalid_last_name');
                            }

                            // Vérification du téléphone
                            $phone = preg_match($phoneRegex, $_POST['noTel']) ? trim($_POST['noTel']) : null;
                            if (!$phone) {
                                $errors[] = t('invalid_phone');
                            }

                            // Vérification du mot de passe (optionnel si pas rempli)
                            $password = !empty($_POST['motDePasse']) ? trim($_POST['motDePasse']) : '';
                            if ($password && !preg_match($passwordRegex, $password)) {
                                $errors[] = t('invalid_password');
                            }

                            // Afficher les erreurs s'il y en a
                            if (!empty($errors)) {
                                foreach ($errors as $error) {
                                    echo "<p style='color: red;'>$error</p>";
                                }
                            } else {
                                // Mise à jour en BDD
                                $passwordHash = (!empty($password)) 
                                    ? password_hash($password, PASSWORD_DEFAULT)
                                    : $userInfo->rendMotDePasse();

                                // Création d'une instance Users pour la mise à jour
                                $updatedUser = new Users(
                                    $firstName,
                                    $lastName,
                                    $email,
                                    $phone,
                                    $passwordHash,
                                    $userInfo->rendId() // On conserve l'ID existant
                                );

                                $dbManager->updateUser($userInfo->rendId(), $updatedUser);

                                // Mise à jour de la session si l'email change
                                $_SESSION['email_user'] = $email;

                                // Récupérer les dernières infos depuis la BDD
                                $userArray = $dbManager->getUser($email);
                                $userInfo  = $userArray[0];

                                $message = [
                                    'type' => 'success', 
                                    'text' => t('profile_updated')
                                ];
                            }
                        } catch (Exception $e) {
                            // Gestion d'erreurs SQL / uniques
                            if ($e->getCode() == 23000) {
                                $message = [
                                    'type' => 'danger', 
                                    'text' => t('unique_violation')
                                ];
                            } else {
                                $message = [
                                    'type' => 'danger', 
                                    'text' => t('unexpected_error') . $e->getMessage()
                                ];
                            }
                        }

                        // Rafraîchit la page après 2 secondes
                        header("Refresh:2");
                    }
                    ?>

                    <?php if (isset($message)) : ?>
                        <div class="alert alert-<?= htmlspecialchars($message['type']) ?>">
                            <?= htmlspecialchars($message['text']) ?>
                        </div>
                    <?php endif; ?>

                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
