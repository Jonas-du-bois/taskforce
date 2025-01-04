<?php
/**
 * Fichier des tâches en cours (taches_en_cours)
 * -------------------------------------------
 * Affiche et gère la liste des tâches marquées "En cours" pour l’utilisateur connecté.
 */
require_once 'vendor/autoload.php';
require_once 'includes/functions.php';
// Lien vers le fichier de fonction pour le multilingue
require_once('lang' . DIRECTORY_SEPARATOR . 'lang_func.php');

use M521\Taskforce\dbManager\DbManagerCRUD;

// Initialisation de la gestionnaire de base de données
$dbManager = new DbManagerCRUD();

// Démarrage de la session pour récupérer l'email de l'utilisateur connecté
session_start();
$email = $_SESSION['email_user']; // Récupération de l'email de l'utilisateur depuis la session

// Récupération des informations de l'utilisateur à partir de l'email
$userInfo = getUserByEmail($email, $dbManager);
if (!$userInfo) {
    die("Utilisateur non trouvé."); // Si l'utilisateur n'est pas trouvé, afficher un message d'erreur et stopper l'exécution
}

// Récupération de l'ID de l'utilisateur
$userId = $userInfo->rendId();

// Récupération des tâches en cours de l'utilisateur depuis la base de données
$tachesEnCours = $dbManager->getTasksByUserIdAndStatus($userId, 'en_cours');
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tâches en cours</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="style/tab.css">
</head>

<body>
    <div class="d-flex">
        <!-- Inclusion de la sidebar (menu latéral) -->
        <?php include 'includes/sidebar.php'; ?>

        <!-- Contenu principal -->
        <div class="main-content ms-auto col-md-9 col-lg-10 p-5">
            <h2 class="text-center mb-4 text-warning"><? echo t('tasks_in_progress')?></h2>

            <!-- Vérification si l'utilisateur a des tâches en cours -->
            <?php if (empty($tachesEnCours)): ?>
                <div class="alert alert-info mt-4" role="alert">
                    <p><? echo t('noTasks')?></p>
                </div>
            <?php else: ?>
                <!-- Table des tâches en cours -->
                <div class="table-container p-3 mt-4">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <!-- En-têtes de la table -->
                                <th scope="col" style="width: 20%;"><? echo t('title')?></th>
                                <th scope="col" style="width: 30%;"><? echo t('descr')?></th>
                                <th scope="col" style="width: 20%;"><? echo t('dueDate')?></th>
                                <th scope="col" style="width: 10%;"><? echo t('status')?></th>
                                <th scope="col" style="width: 10%;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Boucle pour afficher chaque tâche -->
                            <?php foreach ($tachesEnCours as $task): ?>
                                <tr>
                                    <!-- Affichage des informations de la tâche -->
                                    <td scope="row" class="fw-bold text-dark p-3"><?php echo htmlspecialchars($task->rendTitre()); ?></td>
                                    <td class="text-truncate" style="max-width: 300px;">
                                        <?php echo htmlspecialchars($task->rendDescription()); ?>
                                    </td>
                                    <td class="text-muted"><?php echo htmlspecialchars($task->getFormattedDateEcheance()); ?></td>
                                    <td>
                                        <!-- Affichage du statut de la tâche avec badge -->
                                        <span class="badge <?php echo getStatusBadgeClass($task->getFormattedStatut()); ?> me-1">
                                        <i class="bi bi-arrow-repeat"><? echo t('inProgress') ?></i> <?php htmlspecialchars($task->getFormattedStatut()); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <form method="GET" action="task_details.php">
                                            <input type="hidden" name="task_id" value="<?php echo htmlspecialchars($task->rendId()); ?>">
                                            <button type="submit" class="btn btn-link p-0">
                                                <i class="bi bi-pencil" title="Modifier"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Inclusion de Bootstrap JS pour les interactions dynamiques -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>