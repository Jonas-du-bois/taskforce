<?php 
// Lien vers le fichier de fonction pour le multilangue
require_once('lang' . DIRECTORY_SEPARATOR . 'lang_func.php');
include('includes/header.php'); 
?>

<main class="container mt-5">
    <section class="mt-5">
        <h1 class="text-center text-primary mb-4"><? echo t('tips_and_tricks'); ?></h1>
        <p class="text-center mb-5">
            <? echo t('boost_productivity'); ?>
        </p>
        <div class="row g-4">
            <!-- Tip 1: Spotlight -->
            <div class="col-md-4">
                <div class="card shadow mb-4">
                    <img src="style/img/spotlight_exemple.gif" class="card-img-top" alt="Illustration Spotlight">
                    <div class="card-body">
                        <h5 class="card-title text-dark"><? echo t('search_spotlight'); ?></h5>
                        <p class="card-text">
                            <? echo t('spotlight_description'); ?>
                        </p>
                    </div>
                </div>
            </div>
            <!-- Tip 2: Quick Email -->
            <div class="col-md-4">
                <div class="card shadow mb-4">
                    <img src="style/img/MailClic_exemple.gif" class="card-img-top" alt="Illustration Email rapide">
                    <div class="card-body">
                        <h5 class="card-title text-dark"><? echo t('quick_email'); ?></h5>
                        <p class="card-text">
                            <? echo t('quick_email_description'); ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Tip 3: Sorting Tasks -->
            <div class="col-md-4">
                <div class="card shadow mb-4">
                    <img src="style/img/tri_exemple.gif" class="card-img-top" alt="Illustration Tri des tâches">
                    <div class="card-body">
                        <h5 class="card-title text-dark"><? echo t('task_sorting'); ?></h5>
                        <p class="card-text">
                            <? echo t('sorting_description'); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<style>
.card-title {
    font-size: 1.2rem;
    font-weight: bold;
}
.card-text {
    font-size: 0.95rem;
    line-height: 1.5;
}
</style>

<?php include('includes/footer.php'); ?>
