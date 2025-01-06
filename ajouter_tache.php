<?php
// Chargement automatique des classes et des fonctions personnalisées
require_once 'vendor/autoload.php';
require_once 'includes/functions.php';
// Lien vers le fichier de fonction pour le multilingue
require_once('lang' . DIRECTORY_SEPARATOR . 'lang_func.php');

use M521\Taskforce\dbManager\DbManagerCRUD;
use M521\Taskforce\dbManager\Task;

// Initialisation du gestionnaire de base de données
$dbManager = new DbManagerCRUD();

// Démarrer la session si elle n'est pas déjà active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialisation des variables
$statut = 'a_faire'; // Statut par défaut
$email = $_SESSION['email_user'] ?? '';

// Vérification de l'email utilisateur et récupération des informations
$userInfo = getUserByEmail($email, $dbManager);
if (!$userInfo) {
    die(t('taskNotFound'));
}

// Récupérer l'ID de l'utilisateur
$userId = $userInfo->rendId();

// Récupération de tous les utilisateurs pour le champ de sélection multiple
$allUsers = $dbManager->getAllUsers();

// Traitement du formulaire d'ajout de tâche
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération et validation des données du formulaire
    $titre = $_POST['titre'] ?? '';
    $description = $_POST['description'] ?? '';
    $dateEcheance = $_POST['dateEcheance'] ?? '';
    $statut = $_POST['statut'] ?? 'a_faire';

    // Récupération des IDs des utilisateurs assignés ou utilisateur courant par défaut
    $userIds = isset($_POST['userIds']) ? $_POST['userIds'] : [];

    // Vérification et ajout de l'ID de l'utilisateur courant s'il n'est pas déjà présent
    if (!in_array($userInfo->rendId(), $userIds)) {
        $userIds[] = $userInfo->rendId();
    }

    // Validation des champs obligatoires
    if (empty($titre) || empty($dateEcheance)) {
        $errorMessage = t('titleRequired') . ' ' . t('dueDateRequired');
    } elseif (!in_array($statut, ['a_faire', 'en_cours', 'termine'])) {
        $errorMessage = t('invalidStatus');
    } else {
        try {
            // Création de l'objet tâche
            $task = new Task(
                $titre,
                $description,
                $userIds,
                $dateEcheance,
                $statut
            );

            // Ajout de la tâche à la base de données
            $taskId = $dbManager->createTask($task);
            $task->setId($taskId);
            $successMessage = t('taskAddedSuccess');
        } catch (\Exception $e) {
            $errorMessage = "Erreur : " . $e->getMessage();
        }
    }
}
?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajout de tâches</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="style/styleSheet.css" rel="stylesheet">
</head>

<body>
    <div class="d-flex flex-column flex-md-row vh-100">
        <!-- Inclusion de la barre latérale -->
        <?php include 'includes/sidebar.php'; ?>

        <main class="container py-4">
            <div class="main-content ms-auto col-md-9 col-lg-10 pt-4">
                <h2 class="text-center mb-4"><?= t('addTask'); ?></h2>

                <!-- Affichage des messages de succès ou d'erreur -->
                <?php if (isset($errorMessage)): ?>
                    <div class="alert alert-danger">
                        <?= htmlspecialchars($errorMessage); ?>
                    </div>
                <?php elseif (isset($successMessage)): ?>
                    <div class="alert alert-success">
                        <?= htmlspecialchars($successMessage); ?>
                    </div>
                <?php endif; ?>

                <!-- Formulaire d'ajout de tâche -->
                <form method="POST" action="" class="shadow p-4 rounded bg-white">
                    <h5 class="text-primary mb-3"><?= t('taskDetails'); ?> <span class="text-danger"></span></h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="titre" class="form-label"><?= t('taskTitle'); ?> <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="titre" name="titre" required
                                value="<?= htmlspecialchars($titre ?? ''); ?>" placeholder="<?= t('enterTaskTitle') ?>">
                        </div>

                        <div class="col-md-6">
                            <label for="statut" class="form-label"><?= t('status'); ?> <span class="text-danger">*</span></label>
                            <select class="form-select" id="statut" name="statut" required>
                                <option value="a_faire" <?= ($statut === 'a_faire') ? 'selected' : ''; ?>><?= t('todo'); ?></option>
                                <option value="en_cours" <?= ($statut === 'en_cours') ? 'selected' : ''; ?>><?= t('inProgress'); ?></option>
                                <option value="termine" <?= ($statut === 'termine') ? 'selected' : ''; ?>><?= t('done'); ?></option>
                            </select>
                        </div>
                    </div>

                    <h5 class="text-primary mt-4 mb-3"><?= t('taskDescription'); ?></h5>
                    <div class="mb-3">
                        <textarea class="form-control" id="description" name="description" rows="4" placeholder="<?= t('describeTask') ?>"><?= htmlspecialchars($description ?? ''); ?></textarea>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="dateEcheance" class="form-label"><?= t('dueDate'); ?> <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="dateEcheance" name="dateEcheance" required
                                value="<?= htmlspecialchars($dateEcheance ?? ''); ?>">
                        </div>

                        <div class="col-md-6">
                            <label for="userIds" class="form-label"><?= t('assignUsers'); ?></label>
                            <select multiple class="form-select" id="userIds" name="userIds[]" size="4">
                                <?php
                                if (!empty($allUsers)) {
                                    foreach ($allUsers as $user) {
                                        if ($user->rendId() == $userId) {
                                            continue;
                                        } else {
                                            echo "<option value='" . htmlspecialchars($user->rendId()) . "'>"
                                                . htmlspecialchars($user->rendNom() . " " . $user->rendPrenom())
                                                . "</option>";
                                        }
                                    }
                                } else {
                                    echo "<option disabled>" . t('noUsersAvailable') . "</option>";
                                }
                                ?>
                            </select>
                            <small class="form-text text-muted"><?= t('selectMultipleUsers'); ?></small>
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-plus-circle"></i> <?= t('addTaskButton'); ?></button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>