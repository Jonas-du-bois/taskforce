<footer class="bg-light text-center text-lg-start mt-5">
    <div class="container p-4">
        <div class="row">
            <!-- Colonne 1 : À propos -->
            <div class="col-lg-6 col-md-12 mb-4">
                <h5 class="text-uppercase"><?php echo t('about_taskforce'); ?></h5>
                <p>
                    <?php echo t('taskforce_description'); ?>
                </p>
            </div>

            <!-- Colonne 2 : Contact -->
            <div class="col-lg-6 col-md-12 mb-4">
                <h5 class="text-uppercase"><?php echo t('contact'); ?></h5>
                <ul class="list-unstyled">
                    <li>
                        <?= t('email') ?> :
                        <a href="mailto:support@taskforce.com" class="text-dark">support@taskforce.com</a>
                    </li>
                    <li>
                        <?= t('phone') ?> :
                        <a href="tel:+41789338141" class="text-dark">+41 78 933 81 41</a>
                    </li>
                    <li>
                        <?= t('address') ?> :
                        Av. des Sports 20, 1401 Yverdon-les-Bains
                    </li>
                </ul>

            </div>
        </div>
    </div>

    <!-- Barre inférieure -->
    <div class="text-center p-3 bg-dark text-white">
        &copy; <?php echo date("Y"); ?> TaskForce. <?php echo t('all_rights_reserved'); ?>
    </div>
</footer>