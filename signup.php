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
                        <label for="firstname" class="form-label"><?php echo t('firstname'); ?><span class="text-danger"> *</span></label>
                        <input type="text" id="firstname" name="firstname" class="form-control" required minlength="3" maxlength="20" placeholder="<?php echo t('firstname_placeholder'); ?>" value="<?php echo isset($_POST['firstname']) ? htmlspecialchars($_POST['firstname']) : ''; ?>">
                    </div>

                    <div class="mb-3">
                        <label for="lastname" class="form-label"><?php echo t('lastname'); ?><span class="text-danger"> *</span></label>
                        <input type="text" id="lastname" name="lastname" class="form-control" required minlength="3" maxlength="20" placeholder="<?php echo t('lastname_placeholder'); ?>" value="<?php echo isset($_POST['lastname']) ? htmlspecialchars($_POST['lastname']) : ''; ?>">
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label"><?php echo t('email'); ?><span class="text-danger"> *</span></label>
                        <input type="email" id="email" name="email" class="form-control" required placeholder="<?php echo t('email_placeholder'); ?>" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label"><?php echo t('phone'); ?><span class="text-danger"> *</span></label>
                        <input type="tel" id="phone" name="phone" class="form-control" required placeholder="<?php echo t('phone_placeholder'); ?>" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label"><?php echo t('password'); ?><span class="text-danger"> *</span></label>
                        <input type="password" id="password" name="password" class="form-control" required minlength="8" placeholder="<?php echo t('password_placeholder'); ?>">
                    </div>

                    <button type="submit" name="submit" class="btn btn-primary w-100"><?php echo t('submit_button'); ?></button>
                </form>

                <?php
                $dbUser = new DbManagerCRUD();
                $dbUser->creeTable();

                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    // Récupérer les données du formulaire
                    $nom = filter_input(INPUT_POST, 'lastname', FILTER_SANITIZE_SPECIAL_CHARS);
                    $prenom = filter_input(INPUT_POST, 'firstname', FILTER_SANITIZE_SPECIAL_CHARS);
                    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
                    $noTel = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_SPECIAL_CHARS);
                    $motDePasse = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);

                    // Validation des données
                    $errors = [];

                    // Validation du prénom, nom, téléphone, email, et mot de passe
                    if (!preg_match("/^[a-zA-ZÀ-ÿ' -]{3,20}$/", $prenom)) {
                        $errors[] = t('firstname_error');
                    }
                    if (!preg_match("/^[a-zA-ZÀ-ÿ' -]{3,20}$/", $nom)) {
                        $errors[] = t('lastname_error');
                    }
                    if (!$email) {
                        $errors[] = t('email_error');
                    }
                    if (!preg_match("/^\+?[0-9]{10,15}$/", $noTel)) {
                        $errors[] = t('phone_error');
                    }
                    if (!preg_match("/^(?=.*[A-Z])(?=.*[\W_])(?=.{8,})/", $motDePasse)) {
                        $errors[] = t('password_error');
                    }

                    // Afficher les erreurs
                    if (!empty($errors)) {
                        foreach ($errors as $error) {
                            echo "<p style='color: red;'>$error</p>";
                        }
                    } else {
                        // Si toutes les validations passent, traiter les données
                        $newUser = new Users($prenom, $nom, $email, $noTel, $motDePasse);
                        $token = $newUser->rendToken();

                        try {
                            // Ajouter l'utilisateur dans la base de données
                            $id = $dbUser->ajoutePersonne($newUser);

                            // Envoi du mail de confirmation
                            sendConfirmationEmail($prenom, $email, $token);

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