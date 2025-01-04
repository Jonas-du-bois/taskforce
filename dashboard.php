<?php
require_once 'vendor/autoload.php'; // Chargement des dépendances via Composer
require_once 'includes/functions.php'; // Fonctions utilitaires
// Lien vers le fichier de fonction pour le multilingue
require_once('lang' . DIRECTORY_SEPARATOR . 'lang_func.php');

use M521\Taskforce\dbManager\DbManagerCRUD;

$dbManager = new DbManagerCRUD();

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$email = $_SESSION['email_user'] ?? null;
if (!$email) {
    die("L'utilisateur n'est pas connecté.");
}

$userInfo = getUserByEmail($email, $dbManager);
if (!$userInfo) {
    die("Utilisateur non trouvé.");
}

$userId = $userInfo->rendId();

$successMessage = '';
$errorMessage = '';

$sortColumn = $_GET['sort'] ?? 'title';
$order = $_GET['order'] ?? 'ASC';

$taches = $dbManager->getTasksByUserIdSorted($userId, $sortColumn, $order);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task_id'], $_POST['statut'])) {
    $taskId = intval($_POST['task_id']);
    $formattedStatut = $_POST['statut'];

    $statutTraduction = [
        'À faire' => 'a_faire',
        'En cours' => 'en_cours',
        'Terminé' => 'termine',
    ];

    $internalStatut = $statutTraduction[$formattedStatut] ?? null;

    if ($internalStatut) {
        $task = $dbManager->getTaskById($taskId);
        if ($task) {
            $task->setStatut($internalStatut);
            if ($dbManager->updateTask($task, $taskId)) {
                $successMessage = t('taskStatusUpdated'); // Traduction : "Statut de la tâche mis à jour avec succès."
            } else {
                $errorMessage = t('errorMessageUpdateTask') . $taskId; // Traduction : "Erreur lors de la mise à jour de la tâche : "
            }
        } else {
            $errorMessage = t('taskNotFound'); // Traduction : "Tâche non trouvée."
        }
    } else {
        $errorMessage = t('invalidStatusSelected'); // Traduction : "Statut invalide sélectionné."
    }

    $taches = $dbManager->getTasksByUserIdSorted($userId, $sortColumn, $order);
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Gestion des Tâches</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="style/tab.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <div class="container-fluid" id="loading">
        <div class="row">
            <?php include 'Includes/sidebar.php'; ?>

            <div class="main-content ms-auto col-md-9 col-lg-10 p-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><?php echo t('dashboard'); ?></h2>
                    <button id="searchButton" class="btn btn-outline-primary">
                        <i class="bi bi-search"></i> <?php echo t('search'); ?>
                    </button>
                </div>
                <!--
                <?php //if ($successMessage): 
                ?>
                    <div class="alert alert-success"><?php //echo htmlspecialchars($successMessage); 
                                                        ?></div>
                <?php //endif; 
                ?>
                -->
                <?php if ($errorMessage): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($errorMessage); ?></div>
                <?php endif; ?>

                <h4 class="text-secondary"><?php echo t('Mytask'); ?></h4>

                <?php if (empty($taches)): ?>
                    <div class="alert alert-info mt-4">
                        <p><?php echo t('NoTask'); ?></p>
                    </div>
                <?php else: ?>
                    <div class="table-container p-3 mt-4">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th scope="col">
                                        <a href="?sort=title&order=<?php echo getSortOrder('title'); ?>">
                                            <?php echo t('title'); ?>
                                            <i class="bi <?php echo $sortColumn === 'title' ? ($order === 'ASC' ? 'bi-arrow-up' : 'bi-arrow-down') : ''; ?>"></i>
                                        </a>
                                    </th>
                                    <th scope="col"><?php echo t('descr'); ?></th>
                                    <th scope="col">
                                        <a href="?sort=date_echeance&order=<?php echo getSortOrder('date_echeance'); ?>">
                                            <?php echo t('Date'); ?>
                                            <i class="bi <?php echo $sortColumn === 'date_echeance' ? ($order === 'ASC' ? 'bi-arrow-up' : 'bi-arrow-down') : ''; ?>"></i>
                                        </a>
                                    </th>
                                    <th scope="col">
                                        <a href="?sort=statut&order=<?php echo getSortOrder('statut'); ?>">
                                            <?php echo t('status'); ?>
                                            <i class="bi <?php echo $sortColumn === 'statut' ? ($order === 'ASC' ? 'bi-arrow-up' : 'bi-arrow-down') : ''; ?>"></i>
                                        </a>
                                    </th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($taches as $task): ?>
                                    <tr>
                                        <td class="fw-bold"><?php echo htmlspecialchars($task->rendTitre()); ?></td>
                                        <td class="text-truncate" style="max-width: 300px;">
                                            <?php echo htmlspecialchars($task->rendDescription()); ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($task->getFormattedDateEcheance()); ?></td>
                                        <td>
                                            <form method="POST" action="">
                                                <input type="hidden" name="task_id" value="<?php echo htmlspecialchars($task->rendId()); ?>">
                                                <div class="d-inline-block">
                                                    <select
                                                        name="statut"
                                                        class="form-select form-select-sm <?php
                                                        // Récupérer les classes de bordure et de fond
                                                        echo getStatusClasses($task->getFormattedStatut()); ?>"
                                                        onchange="this.form.submit()"
                                                        style="border-width: 2px;">
                                                        <?php foreach (['À faire', 'En cours', 'Terminé'] as $status): ?>
                                                            <option value="<?php echo htmlspecialchars($status); ?>" <?php echo $status === $task->getFormattedStatut() ? 'selected' : ''; ?>>
                                                                <?php echo htmlspecialchars($status); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </form>
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
    </div>
</body>

</html>