<?php
require_once 'vendor/autoload.php';
require_once 'mail/sendConfirmationEmail.php'; // Inclure le fichier d'envoi d'email
require_once('lang' . DIRECTORY_SEPARATOR . 'lang_func.php');

use M521\Taskforce\dbManager\DbManagerCRUD;
use M521\Taskforce\dbManager\Users;
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo t('signup_title'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include('includes/header.php'); ?>

    <main class="container mt-5">
        <h1 class="text-center mb-4"><?php echo t('signup_heading'); ?></h1>
        <p class="text-center mb-5"><?php echo t('signup_subheading'); ?></p>

        <!-- Formulaire d'inscription -->
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8 col-sm-12">
                <form action="signup.php" method="POST">
                    <div class="mb-3">
                        <label for="firstname" class="form-label">
                            <?php echo t('firstname'); ?>
                            <span class="text-danger"> *</span>
                        </label>
                        <input
                            type="text"
                            id="firstname"
                            name="firstname"
                            class="form-control"
                            required
                            minlength="3"
                            maxlength="20"
                            placeholder="<?php echo t('firstname_placeholder'); ?>"
                            value="<?php echo isset($_POST['firstname']) ? htmlspecialchars($_POST['firstname']) : ''; ?>">
                    </div>

                    <div class="mb-3">
                        <label for="lastname" class="form-label">
                            <?php echo t('lastname'); ?>
                            <span class="text-danger"> *</span>
                        </label>
                        <input
                            type="text"
                            id="lastname"
                            name="lastname"
                            class="form-control"
                            required
                            minlength="3"
                            maxlength="20"
                            placeholder="<?php echo t('lastname_placeholder'); ?>"
                            value="<?php echo isset($_POST['lastname']) ? htmlspecialchars($_POST['lastname']) : ''; ?>">
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <?php echo t('email'); ?>
                            <span class="text-danger"> *</span>
                        </label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control"
                            required
                            placeholder="<?php echo t('email_placeholder'); ?>"
                            value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">
                            <?php echo t('phone'); ?>
                            <span class="text-danger"> *</span>
                        </label>
                        <input
                            type="tel"
                            id="phone"
                            name="phone"
                            class="form-control"
                            required
                            placeholder="<?php echo t('phone_placeholder'); ?>"
                            value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">
                            <?php echo t('password'); ?>
                            <span class="text-danger"> *</span>
                        </label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control"
                            required
                            minlength="8"
                            placeholder="<?php echo t('password_placeholder'); ?>">
                    </div>

                    <button type="submit" name="submit" class="btn btn-primary w-100">
                        <?php echo t('submit_button'); ?>
                    </button>
                </form>

                <?php
                // Instancier le gestionnaire de base de données
                $dbManager = new DbManagerCRUD();
                // Créer la table si elle n'existe pas
                $dbManager->creeTable();

                // Si le formulaire est soumis
                if ($_SERVER["REQUEST_METHOD"] === "POST") {
                    // Récupérer et filtrer les données du formulaire
                    $lastName      = filter_input(INPUT_POST, 'lastname', FILTER_SANITIZE_SPECIAL_CHARS);
                    $firstName     = filter_input(INPUT_POST, 'firstname', FILTER_SANITIZE_SPECIAL_CHARS);
                    $userEmail     = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
                    $phoneNumber   = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_SPECIAL_CHARS);
                    $userPassword  = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);

                    // Tableau pour stocker d'éventuelles erreurs
                    $errors = [];

                    // Validation du prénom
                    if (!preg_match("/^[a-zA-ZÀ-ÿ' -]{3,20}$/", $firstName)) {
                        $errors[] = t('firstname_error');
                    }

                    // Validation du nom
                    if (!preg_match("/^[a-zA-ZÀ-ÿ' -]{3,20}$/", $lastName)) {
                        $errors[] = t('lastname_error');
                    }

                    // Validation de l'email
                    if (!$userEmail) {
                        $errors[] = t('email_error');
                    }

                    // Validation du téléphone
                    if (!preg_match("/^\+?[0-9]{10,15}$/", $phoneNumber)) {
                        $errors[] = t('phone_error');
                    }

                    // Validation du mot de passe (8+ caractères, 1 majuscule, 1 caractère spécial...)
                    if (!preg_match("/^(?=.*[A-Z])(?=.*[\W_])(?=.{8,})/", $userPassword)) {
                        $errors[] = t('password_error');
                    }

                    // Afficher les erreurs s'il y en a
                    if (!empty($errors)) {
                        foreach ($errors as $error) {
                            echo "<p style='color: red;'>$error</p>";
                        }
                    } else {
                        // Si tout est valide, on crée un nouvel utilisateur
                        $newUser = new Users($firstName, $lastName, $userEmail, $phoneNumber, $userPassword);
                        $token   = $newUser->rendToken(); // Le token pour validation

                        try {
                            // Ajouter l'utilisateur dans la base de données
                            $insertedUserId = $dbManager->ajoutePersonne($newUser);

                            // Envoyer l'email de confirmation
                            sendConfirmationEmail($firstName, $userEmail, $token);

                            echo "<p style='color: green;'>" . t('success_message') . "</p>";
                        } catch (PDOException $e) {
                            echo "<p style='color: red;'>" . t('error_message') . "</p>";
                        }
                    }
                }
                ?>
            </div>
        </div>
    </main>

    <?php include('includes/footer.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>