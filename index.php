<?php
include('includes/header.php');
?>

<main class="container mt-5">
    <div class="row">
        <!-- Colonne de gauche : Description de l'application -->
        <div class="col-md-6">
            <h1 class="mb-4"><?php echo t('welcomeTaskforce'); ?></h1>
            <p><?php echo t('description'); ?></p>
            <p>
                <?php echo t('features'); ?>
                <ul>
                    <li><?php echo t('feature1'); ?></li>
                    <li><?php echo t('feature2'); ?></li>
                    <li><?php echo t('feature3'); ?></li>
                </ul>
            </p>
            <p class="mt-4"><?php echo t('ctaJoin'); ?></p>

            <?php
            // Vérifie si l'utilisateur est connecté et affiche un message en conséquence
            if (isset($_SESSION['user_connected']) && $_SESSION['user_connected']) {
                echo "<p class='text-success'><strong>" . t('loggedInMessage') . "</strong></p>";
            } else {
                $_SESSION['user_connected'] = false;
                echo "<p class='text-warning'><strong>" . t('loggedOutMessage') . "</strong></p>";
            }
            ?>
        </div>

        <!-- Colonne de droite : Image d'illustration -->
        <div class="col-md-6 text-center">
            <img src="style/img/illustration_index.jpg" alt="Illustration de TaskForce" class="img-fluid rounded">
        </div>
    </div>
</main>

<?php include('includes/footer.php'); ?>
