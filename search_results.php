<?php

require_once 'vendor/autoload.php'; // Charger les classes via Composer
require_once 'includes/functions.php'; // Inclure les fonctions utilitaires
// Lien vers le fichier de fonction pour le multilingue
require_once('lang' . DIRECTORY_SEPARATOR . 'lang_func.php');

use M521\Taskforce\dbManager\DbManagerCRUD;

// Création d'une instance du gestionnaire de base de données
$dbManager = new DbManagerCRUD();

// Démarrer la session si elle n'est pas déjà active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$email = $_SESSION['email_user'] ?? '';

// Vérification de l'email utilisateur et récupération des informations
$userInfo = getUserByEmail($email, $dbManager);
if (!$userInfo) {
    die("Utilisateur non trouvé.");
}

// Récupérer l'ID de l'utilisateur
$userId = $userInfo->rendId();

// Initialisation des variables
$query = $_GET['query'] ?? ''; // Récupération de la recherche

if($query == 'rick roll') {
    header("Location: https://www.youtube.com/watch?v=xvFZjo5PgG0");
}
// supprimer les + dans la query
$query = str_replace('+', '', $query);

$searchResults = [];

if (!empty($query)) {
    // Rechercher les tâches correspondant à la requête
    $searchResults = $dbManager->searchTasks($query, $userId);
}

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultats de recherche</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="style/tab.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Inclure la barre latérale -->
            <?php include 'Includes/sidebar.php'; ?>
            <!-- Contenu principal -->
            <div class="main-content ms-auto col-md-9 col-lg-10 p-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Tableau de bord</h2>
                    <!-- Bouton Loupe -->
                    <button id="searchButton" class="btn btn-outline-primary mt-3">
                        <i class="fas fa-search"></i> Recherche rapide
                    </button>
                </div>

                <!-- Message si aucun terme de recherche n'est entré -->
                <?php if (empty($query)): ?>
                    <div class="alert alert-warning" role="alert">
                        <p>Vous n'avez pas mis de terme de recherche, retournez sur la page de recheche</p>
                    </div>
                <?php else: ?>

                    <!-- Message si aucun résultat n'est trouvé -->
                    <?php if (empty($searchResults)): ?>
                        <div class="alert alert-info" role="alert">
                            <p>Aucun résultat trouvé pour : <strong><?php echo htmlspecialchars($query); ?></strong></p>
                        </div>
                    <?php else: ?>

                        <!-- Tableau des résultats de recherche -->
                        <div class="table-container p-3 mt-4">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                    <th scope="col" style="width: 20%;"><? echo t('title')?></th>
                                <th scope="col" style="width: 30%;"><? echo t('descr')?></th>
                                <th scope="col" style="width: 20%;"><? echo t('dueDate')?></th>
                                <th scope="col" style="width: 10%;"><? echo t('status')?></th>
                                <th scope="col" style="width: 10%;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($searchResults as $task): ?>
                                        <tr>
                                            <td scope="row" class="fw-bold text-dark p-3">
                                                <?php echo htmlspecialchars($task->rendTitre()); ?>
                                            </td>
                                            <td class="text-truncate" style="max-width: 300px;">
                                                <?php echo htmlspecialchars($task->rendDescription()); ?>
                                            </td>
                                            <td class="text-muted">
                                                <?php echo htmlspecialchars($task->getFormattedDateEcheance()); ?>
                                            </td>
                                            <td>
                                                <span class="badge <?php echo getStatusBadgeClass($task->getFormattedStatut()); ?>">
                                                    <?php echo htmlspecialchars($task->getFormattedStatut()); ?>
                                                </span>
                                            </td>
                                            <td>
                                            <!-- Bouton pour modifier -->
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
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>
