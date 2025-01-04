<?php

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
        $userInfoArray = $dbManager->rendPersonnes($userEmail);
        if (empty($userInfoArray)) {
            die(t('user_not_found')); // Si l'utilisateur n'est pas trouvé, on arrête l'exécution
        }

        // L'utilisateur est unique, récupération de ses informations
        $userInfo = $userInfoArray[0];

        // Définition des expressions régulières pour valider les champs
        $namePattern = "/^[a-zA-ZÀ-ÿ' -]{3,20}$/";  // Prénom et Nom : 3 à 20 caractères, lettres et espaces
        $telPattern = "/^\+?[0-9]{10,15}$/";  // Téléphone : 10 à 15 chiffres (format international ou local)
        $passwordPattern = "/^(?=.*[A-Z])(?=.*[\W_])(?=.{8,})/";  // Mot de passe : minimum 8 caractères, une majuscule et un caractère spécial

        // Initialisation des erreurs
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
                            <input type="text" class="form-control" id="prenom" name="prenom" value="<?= htmlspecialchars($userInfo->rendPrenom()) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="nom" class="form-label"><?= t('last_name'); ?></label>
                            <input type="text" class="form-control" id="nom" name="nom" value="<?= htmlspecialchars($userInfo->rendNom()) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label"><?= t('email'); ?></label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($userInfo->rendEmail()) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="noTel" class="form-label"><?= t('phone_number'); ?></label>
                            <input type="tel" class="form-control" id="noTel" name="noTel" value="<?= htmlspecialchars($userInfo->rendNoTel()) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="motDePasse" class="form-label"><?= t('password'); ?></label>
                            <input type="password" class="form-control" id="motDePasse" name="motDePasse">
                        </div>
                        <button type="submit" class="btn btn-primary"><?= t('update_button'); ?></button>
                    </form>

                    <?php
                    // Traitement du formulaire
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        try {
                            $prenom = htmlspecialchars(trim($_POST['prenom']));
                            $nom = htmlspecialchars(trim($_POST['nom']));
                            $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);

                            if (!$email) {
                                $errors[] = t('invalid_email');
                            }

                            if (!preg_match($namePattern, $prenom)) {
                                $errors[] = t('invalid_first_name');
                            }

                            if (!preg_match($namePattern, $nom)) {
                                $errors[] = t('invalid_last_name');
                            }

                            $noTel = preg_match($telPattern, $_POST['noTel']) ? trim($_POST['noTel']) : null;
                            if (!$noTel) {
                                $errors[] = t('invalid_phone');
                            }

                            $motDePasse = !empty($_POST['motDePasse']) ? trim($_POST['motDePasse']) : '';
                            if ($motDePasse && !preg_match($passwordPattern, $motDePasse)) {
                                $errors[] = t('invalid_password');
                            }

                            if (!empty($errors)) {
                                foreach ($errors as $error) {
                                    echo "<p style='color: red;'>$error</p>";
                                }
                            } else {
                                $motDePasseHash = !empty($motDePasse) ? password_hash($motDePasse, PASSWORD_DEFAULT) : $userInfo->rendMotDePasse();
                                $updatedUser = new Users(
                                    $prenom,
                                    $nom,
                                    $email,
                                    $noTel,
                                    $motDePasseHash,
                                    $userInfo->rendId()
                                );

                                $dbManager->modifiePersonne($userInfo->rendId(), $updatedUser);

                                $_SESSION['email_user'] = $email;

                                $userInfoArray = $dbManager->rendPersonnes($email);
                                $userInfo = $userInfoArray[0];

                                $message = ['type' => 'success', 'text' => t('profile_updated')];
                            }
                        } catch (Exception $e) {
                            if ($e->getCode() == 23000) {
                                $message = ['type' => 'danger', 'text' => t('unique_violation')];
                            } else {
                                $message = ['type' => 'danger', 'text' => t('unexpected_error') . $e->getMessage()];
                            }
                        }

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