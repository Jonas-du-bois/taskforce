<?php
/**
 * Fichier des tâches partagées (taches_partage)
 * -------------------------------------------
 * Affiche et gère la liste des tâches partagées entre plusieurs utilisateurs y compris l’utilisateur connecté.
 */
require_once 'vendor/autoload.php';
require_once 'includes/functions.php';
// Lien vers le fichier de fonction pour le multilingue
require_once('lang' . DIRECTORY_SEPARATOR . 'lang_func.php');

use M521\Taskforce\dbManager\DbManagerCRUD;

// Création de l'objet DbManager pour interagir avec la base de données
$dbManager = new DbManagerCRUD();

// Démarrage de la session et récupération de l'email de l'utilisateur connecté
session_start();
$email = $_SESSION['email_user']; // Récupère l'email de l'utilisateur depuis la session

// Récupération des informations de l'utilisateur à partir de l'email
$userInfo = getUserByEmail($email, $dbManager);
if (!$userInfo) {
    die("Utilisateur non trouvé."); // Si l'utilisateur n'est pas trouvé, afficher une erreur et arrêter l'exécution
}

// Récupération de l'ID de l'utilisateur pour récupérer ses tâches partagées
$userId = $userInfo->rendId();

// Récupération des tâches partagées par l'utilisateur
$tachepartager = $dbManager->getTasksSharedByUserId($userId);

// Fonction pour formater les données des tâches avant affichage
function formatTaskData($task)
{
    $task['dateEcheance'] = formatDateEcheance($task['dateEcheance']); // Formatage de la date d'échéance
    $task['statut'] = formatStatut($task['statut']); // Traduction du statut
    return $task;
}

// Fonction pour formater la date d'échéance au format 'dd.mm.yyyy'
function formatDateEcheance($dateEcheance)
{
    if ($dateEcheance) {
        $d = DateTime::createFromFormat('Y-m-d', $dateEcheance); // Création d'un objet DateTime à partir de la date
        return $d ? $d->format('d.m.Y') : null; // Retourne la date au format souhaité
    }
    return null; // Si aucune date, retourne null
}

// Fonction pour traduire le statut d'une tâche en texte lisible
function formatStatut($statut)
{
    // Tableau associatif pour traduire les statuts de la tâche
    $statutTraduction = [
        'a_faire' => 'À faire',
        'en_cours' => 'En cours',
        'termine' => 'Terminé',
    ];
    return $statutTraduction[$statut] ?? 'Inconnu'; // Retourne le statut traduit ou 'Inconnu' si inconnu
}

// Application du formatage à chaque tâche récupérée
$tachepartager = array_map('formatTaskData', $tachepartager);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tâches partagées</title>
    <!-- Intégration de Bootstrap pour la mise en forme -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet"> <!-- Icones Bootstrap -->
    <link rel="stylesheet" href="style/tab.css">
</head>

<body>
    <div class="d-flex">
        <!-- Inclusion de la sidebar (menu latéral) -->
        <?php include 'includes/sidebar.php'; ?>

        <!-- Contenu principal -->
        <div class="main-content ms-auto col-md-9 col-lg-10 p-5">
            <h2 class="text-center mb-4"><? echo t('shared_tasks')?></h2>

            <!-- Vérification si des tâches partagées existent -->
            <?php if (empty($tachepartager)): ?>
                <div class="alert alert-info mt-4" role="alert">
                    <p><? echo t('noTasks')?></p>
                </div>
            <?php else: ?>
                <!-- Table responsive pour afficher les tâches partagées -->
                <div class="table-container p-3 mt-4">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th scope="col" style="width: 20%"><? echo t('title')?></th>
                                <th scope="col" style="width: 30%"><? echo t('descr')?></th>
                                <th scope="col"><? echo t('dueDate')?></th>
                                <th scope="col" style="width: 10%"><? echo t('status')?></th>
                                <th scope="col" style="width: 35%"><? echo t('assign')?></th>
                                <th scope="col" style="width: 5%;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Boucle pour afficher chaque tâche partagée -->
                            <?php foreach ($tachepartager as $task): ?>
                                <tr>
                                    <td class="fw-bold text-dark p-3"><?php echo htmlspecialchars($task['titre']); ?></td>
                                    <td class="text-truncate" style="max-width: 300px;">
                                        <?php echo htmlspecialchars($task['description']); ?>
                                    </td>
                                    <td class="text-muted"><?php echo htmlspecialchars($task['dateEcheance']); ?></td>
                                    <td>
                                        <!-- Affichage du statut de la tâche avec un badge -->
                                        <span class="badge <?php echo getStatusBadgeClass($task['statut']); ?> me-1">                                                                                     
                                            <?if ($task['statut'] === "À faire") {
                                                echo "<i class='bi bi-hourglass-split'>" . t('todo') . "</i> ";
                                            } elseif ($task['statut'] === "En cours") {
                                                echo "<i class='bi bi-arrow-repeat'>" . t('inProgress') . "</i> ";
                                            } elseif ($task['statut'] === "Terminé") {
                                                echo "<i class='bi bi-check-circle-fill'>" . t('done') . "</i> ";
                                            }
                                            ?>
                                        </span>
                                    </td>
                                    <td>
                                        <!-- Affichage des utilisateurs partagés (exclure l'utilisateur connecté) -->
                                        <?php
                                        $sharedUsers = explode(',', $task['shared_user_names']); // Récupère les emails des utilisateurs partagés
                                        foreach ($sharedUsers as $userEmailFromTask):
                                            // Ne pas afficher l'utilisateur connecté
                                            if ($_SESSION['email_user'] === $userEmailFromTask) {
                                                continue;
                                            }
                                        ?>
                                            <a href="mailto:<?php echo htmlspecialchars($userEmailFromTask); ?>"
                                                class="badge bg-primary me-1 text-decoration-none"
                                                data-bs-toggle="tooltip"
                                                title="<?php echo htmlspecialchars($userEmailFromTask); ?>">
                                                <?php echo htmlspecialchars(strlen($userEmailFromTask) > 10 ? substr($userEmailFromTask, 0, 10) . '...' : $userEmailFromTask); ?>
                                            </a>
                                        <?php endforeach; ?>
                                    </td>
                                    <td>
                                    <form method="GET" action="task_details.php">
                                            <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
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

    <!-- Inclusion de Bootstrap JS pour les composants interactifs -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialisation des tooltips Bootstrap pour afficher les informations sur les utilisateurs
        let tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        let tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    </script>
</body>

</html>