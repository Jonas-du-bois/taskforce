<?php

/**
 * Fichier du tableau de bord (dashboard) pour gérer et trier les tâches.
 * Permet également la recherche de tâches et la mise à jour de leur statut.
 */

require_once 'vendor/autoload.php';
require_once 'includes/functions.php';
require_once 'lang' . DIRECTORY_SEPARATOR . 'lang_func.php'; // Gestion multilingue

use M521\Taskforce\dbManager\DbManagerCRUD;

// Instanciation du gestionnaire de base de données
$dbManager = new DbManagerCRUD();

// Démarrage de la session si nécessaire
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérification de l'utilisateur connecté
$email = $_SESSION['email_user'] ?? null;
if (!$email) {
    die("L'utilisateur n'est pas connecté.");
}

// Récupération des informations de l'utilisateur
$userInfo = getUserByEmail($email, $dbManager);
if (!$userInfo) {
    die("Utilisateur non trouvé.");
}

// On récupère l'ID utilisateur pour les requêtes
$userId = $userInfo->rendId();

// Variables pour les messages de succès / erreur
$successMessage = '';
$errorMessage   = '';

// Paramètres de tri
$sortColumn = $_GET['sort']  ?? 'title';
$order      = $_GET['order'] ?? 'ASC';

// Paramètre de recherche (query)
$query = $_GET['query'] ?? '';

// 1. Chargement des tâches : si $query n'est pas vide => recherche + tri, sinon liste complète + tri
if (!empty($query)) {
    $tasks = $dbManager->searchTasksSorted($query, $userId, $sortColumn, $order);
} else {
    $tasks = $dbManager->getTasksByUserIdSorted($userId, $sortColumn, $order);
}

// 2. Mise à jour du statut des tâches via formulaire POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task_id'], $_POST['statut'])) {
    $taskId          = intval($_POST['task_id']);
    $formattedStatus = $_POST['statut'];

    // Tableau de correspondance entre les labels et la valeur interne
    $statusMap = [
        'À faire'  => 'a_faire',
        'En cours' => 'en_cours',
        'Terminé'  => 'termine',
    ];
    $internalStatus = $statusMap[$formattedStatus] ?? null;

    if ($internalStatus) {
        $task = $dbManager->getTaskById($taskId);
        if ($task) {
            // Mise à jour du statut de la tâche
            $task->setStatut($internalStatus);

            // Écriture en base de données
            if ($dbManager->updateTask($task, $taskId)) {
                $successMessage = t('taskStatusUpdated');
            } else {
                $errorMessage = t('errorMessageUpdateTask') . $taskId;
            }
        } else {
            $errorMessage = t('taskNotFound');
        }
    } else {
        $errorMessage = t('invalidStatusSelected');
    }

    // Après la mise à jour, on recharge la liste (recherche ou liste complète)
    if (!empty($query)) {
        $tasks = $dbManager->searchTasksSorted($query, $userId, $sortColumn, $order);
    } else {
        $tasks = $dbManager->getTasksByUserIdSorted($userId, $sortColumn, $order);
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Dashboard - Gestion des Tâches</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Styles personnalisés -->
    <link rel="stylesheet" href="style/tab.css">

    <!-- Scripts Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <div class="container-fluid" id="loading">
        <div class="row">

            <!-- Sidebar -->
            <?php include 'Includes/sidebar.php'; ?>

            <!-- Contenu principal -->
            <div class="main-content ms-auto col-md-9 col-lg-10 p-5">

                <!-- Titre et barre de recherche -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><?php echo t('dashboard'); ?></h2>

                    <div class="d-inline-flex align-items-center">
                        <?php if (!empty($query)): ?>
                            <!-- Bouton pour effacer la recherche -->
                            <a href="dashboard.php" class="btn btn-outline-danger d-flex align-items-center me-2">
                                <i class="bi bi-x"></i>
                            </a>
                        <?php endif; ?>

                        <!-- Bouton Recherche -->
                        <button id="searchButton" class="btn btn-outline-primary">
                            <i class="bi bi-search"></i> <?php echo t('search'); ?>
                        </button>
                    </div>
                </div>

                <!-- Affichage des messages d'erreur -->
                <?php if ($errorMessage): ?>
                    <div class="alert alert-danger">
                        <?php echo htmlspecialchars($errorMessage); ?>
                    </div>
                <?php endif; ?>

                <!-- Sous-titre -->
                <h4 class="text-secondary"><?php echo t('Mytask'); ?></h4>

                <!-- Liste des tâches ou message "NoTask" si vide -->
                <?php if (empty($tasks)): ?>
                    <div class="alert alert-info mt-4">
                        <p><?php echo t('NoTask'); ?></p>
                    </div>
                <?php else: ?>
                    <div class="table-container p-3 mt-4">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th scope="col">
                                        <a href="?sort=titre&order=<?php echo getSortOrder('titre');
                                                                    if (!empty($query)) echo '&query=' . urlencode($query); ?>">
                                            <?php echo t('title'); ?>
                                            <i class="bi <?php
                                                            echo ($sortColumn === 'titre')
                                                                ? (($order === 'ASC') ? 'bi-arrow-up' : 'bi-arrow-down')
                                                                : '';
                                                            ?>"></i>
                                        </a>
                                    </th>
                                    <th scope="col"><?php echo t('descr'); ?></th>
                                    <th scope="col">
                                        <a href="?sort=dateEcheance&order=<?php echo getSortOrder('dateEcheance');
                                                                            if (!empty($query)) echo '&query=' . urlencode($query); ?>">
                                            <?php echo t('Date'); ?>
                                            <i class="bi <?php
                                                            echo ($sortColumn === 'dateEcheance')
                                                                ? (($order === 'ASC') ? 'bi-arrow-up' : 'bi-arrow-down')
                                                                : '';
                                                            ?>"></i>
                                        </a>
                                    </th>
                                    <th scope="col">
                                        <a href="?sort=statut&order=<?php echo getSortOrder('statut');
                                                                    if (!empty($query)) echo '&query=' . urlencode($query); ?>">
                                            <?php echo t('status'); ?>
                                            <i class="bi <?php
                                                            echo ($sortColumn === 'statut')
                                                                ? (($order === 'ASC') ? 'bi-arrow-up' : 'bi-arrow-down')
                                                                : '';
                                                            ?>"></i>
                                        </a>
                                    </th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tasks as $task): ?>
                                    <tr>
                                        <td class="fw-bold">
                                            <?php echo htmlspecialchars($task->rendTitre()); ?>
                                        </td>
                                        <td class="text-truncate" style="max-width: 300px;">
                                            <?php echo htmlspecialchars($task->rendDescription()); ?>
                                        </td>
                                        <td class="<?php echo isDateEcheanceDepassee($task->rendDateEcheance()) ? 'text-danger' : ''; ?>">
                                            <?php echo htmlspecialchars($task->getFormattedDateEcheance()); ?>
                                        </td>
                                        <td>
                                            <!-- Formulaire pour mettre à jour le statut -->
                                            <form method="POST" action="">
                                                <input type="hidden" name="task_id" value="<?php echo htmlspecialchars($task->rendId()); ?>">
                                                <div class="d-inline-block">
                                                    <select
                                                        name="statut"
                                                        class="form-select form-select-sm <?php echo getStatusClasses($task->getFormattedStatut()); ?>"
                                                        onchange="this.form.submit()"
                                                        style="border-width: 2px;">
                                                        <?php
                                                        // Tableau des statuts traduits
                                                        $statuses = [
                                                            t('todo') => 'À faire',
                                                            t('inProgress') => 'En cours',
                                                            t('done') => 'Terminé'
                                                        ];
                                                        ?>
                                                        <?php foreach ($statuses as $translated => $original): ?>
                                                            <option
                                                                value="<?php echo htmlspecialchars($original); ?>"
                                                                <?php echo ($original === $task->getFormattedStatut()) ? 'selected' : ''; ?>>
                                                                <?php echo htmlspecialchars($translated); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </form>
                                        </td>
                                        <td>
                                            <!-- Lien vers la page de détails de la tâche -->
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