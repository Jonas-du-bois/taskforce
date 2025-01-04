<?php
require_once 'vendor/autoload.php';
require_once 'includes/functions.php';
// Lien vers le fichier de fonction pour le multilingue
require_once('lang' . DIRECTORY_SEPARATOR . 'lang_func.php');

use M521\Taskforce\dbManager\DbManagerCRUD;

// Initialisation du gestionnaire de base de données
$dbManager = new DbManagerCRUD();

// Démarrer la session pour récupérer l'email de l'utilisateur connecté
session_start();

// Vérification si l'email est bien défini dans la session, sinon arrêter l'exécution
$email = $_SESSION['email_user'] ?? null;  // Utilisation de l'opérateur de coalescence null
if (!$email) {
    die("Utilisateur non authentifié.");
}

// Récupérer les informations de l'utilisateur connecté à partir de l'email
$userInfo = getUserByEmail($email, $dbManager);
if (!$userInfo) {
    die("Utilisateur non trouvé.");
}

// Récupérer l'ID de l'utilisateur
$userId = $userInfo->rendId();

// Vérification si l'ID de la tâche est bien passé dans l'URL
$taskId = $_GET['task_id'] ?? null;
if (!$taskId || $taskId <= 0) {
    die("ID de tâche invalide.");
}

// Récupérer les détails de la tâche en fonction de son ID
$task = $dbManager->getTaskById($taskId);
if (!$task) {
    die("Tâche non trouvée.");
}

// Récupérer les utilisateurs associés à cette tâche
$taskUsers = $dbManager->getTasksSharedByUserId($taskId);

// Vérification de la soumission du formulaire pour modifier la tâche
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupérer les données du formulaire
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $deadline = $_POST['deadline'] ?? '';
    $status = $_POST['status'] ?? '';
    $assignedUsers = $_POST['assigned_users'] ?? [];
    $unassignedUsers = $_POST['unassigned_users'] ?? [];

    try {
        // Mettre à jour les informations de la tâche
        $task->setTitre($title);
        $task->setDescription($description);
        $task->setDateEcheance($deadline);
        $task->setStatut($status);

        // Mettre à jour la tâche dans la base de données
        $dbManager->updateTask($task, $taskId);

         // Assigner des utilisateurs à la tâche
         if (!empty($assignedUsers)) {
            $dbManager->assignUsersToTask($taskId, $assignedUsers);
        }
        
        // Désassigner des utilisateurs de la tâche
        if (!empty($unassignedUsers)) {
            $dbManager->unassignUsersFromTask($taskId, $unassignedUsers);
        }

        // Ajouter un message de succès dans la session
        $_SESSION['successMessage'] = t('taskUpdateSuccess');
    } catch (\Exception $e) {
        // Ajouter un message d'erreur dans la session
        $_SESSION['errorMessage'] = t('errorMessageUpdateTask') . $e->getMessage();
    }

    // Rediriger vers la page de détails de la tâche après la mise à jour
    header("Location: task_details.php?task_id=" . $taskId);
    exit;  // Toujours utiliser exit après un header pour arrêter l'exécution du script
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de la Tâche</title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome pour les icônes -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Feuille de style personnalisée -->
    <link href="style/styleSheet.css" rel="stylesheet">
</head>

<body>
    <div class="d-flex flex-column flex-md-row vh-100">
        <!-- Sidebar incluse -->
        <?php include 'includes/sidebar.php'; ?>

        <main class="container py-4">
            <div class="main-content ms-auto col-md-9 col-lg-10 pt-4">
                    <h2 class="text-center mb-4">
                        <i class="fa-solid fa-list-check me-2"></i>
                        <?php echo t('editTask')?><?php echo htmlspecialchars($task->rendTitre()); ?>
                    </h2>
                    <!-- Affichage des messages de feedback -->
                    <?php if (isset($_SESSION['successMessage'])) : ?>
                        <div class="alert alert-success">
                            <i class="fa-solid fa-check-circle me-2"></i>
                            <?php echo htmlspecialchars($_SESSION['successMessage']); ?>
                        </div>
                        <?php unset($_SESSION['successMessage']); ?>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['errorMessage'])) : ?>
                        <div class="alert alert-danger">
                            <i class="fa-solid fa-triangle-exclamation me-2"></i>
                            <?php echo htmlspecialchars($_SESSION['errorMessage']); ?>
                        </div>
                        <?php unset($_SESSION['errorMessage']); ?>
                    <?php endif; ?>
                    <!-- Formulaire de modification -->
                    <div class="shadow p-4 rounded bg-white">
                        <form action="task_details.php?task_id=<?php echo $taskId; ?>" method="POST" class="needs-validation">
                            <div class="mb-3">
                                <label for="title" class="text-primary mb-3 fs-5"><?php echo t('taskTitle') ?></label>
                                <input type="text" id="title" name="title" class="form-control" value="<?php echo htmlspecialchars($task->rendTitre()); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="text-primary mb-3 fs-5"><?php echo t('taskDescription') ?></label>
                                <textarea id="description" name="description" class="form-control" rows="4"><?php echo htmlspecialchars($task->rendDescription()); ?></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="deadline" class="text-primary mb-3 fs-5"><?php echo t('dueDate') ?></label>
                                    <input type="date" id="deadline" name="deadline" class="form-control" value="<?php echo $task->rendDateEcheance(); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="status" class="text-primary mb-3 fs-5"><?php echo t('taskStatusLabel') ?></label>
                                    <select id="status" name="status" class="form-select">
                                        <option value="a_faire" <?php echo $task->rendStatut() == 'a_faire' ? 'selected' : ''; ?>><?echo t('todo')?></option>
                                        <option value="en_cours" <?php echo $task->rendStatut() == 'en_cours' ? 'selected' : ''; ?>><?echo t('inProgress')?></option>
                                        <option value="termine" <?php echo $task->rendStatut() == 'termine' ? 'selected' : ''; ?>><?echo t('done')?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <!-- Colonne des utilisateurs à assigner -->
                                <div class="col-md-6 mb-3">
                                    <label for="assigned_users" class="text-primary mb-3 fs-5"><?= t('assignUsersLabel'); ?></label>
                                    <select multiple id="assigned_users" name="assigned_users[]" class="form-select">
                                        <?php
                                        $unassignedUsers = $dbManager->getUsersNotAssignedToTask($taskId);
                                        foreach ($unassignedUsers as $user) {
                                            echo "<option value='" . $user['id'] . "'>" . htmlspecialchars($user['nom']) . " " . htmlspecialchars($user['prenom']) . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <!-- Colonne des utilisateurs à désassigner -->
                                <div class="col-md-6 mb-3">
                                    <label for="unassigned_users" class="text-primary mb-3 fs-5"><?= t('unassignUsersLabel'); ?></label>
                                    <select multiple id="unassigned_users" name="unassigned_users[]" class="form-select">
                                        <?php
                                        $assignedUsers = $dbManager->getUsersAssignedToTask($taskId);
                                        foreach ($assignedUsers as $user) {
                                            echo "<option value='" . $user['id'] . "'>" . htmlspecialchars($user['nom']) . " " . htmlspecialchars($user['prenom']) . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa-solid fa-floppy-disk me-2"></i><? echo t('updateButton')?>
                                </button>
                                <a id="backButton" class="btn btn-secondary"><i class="fa-solid fa-arrow-left me-2"></i> <? echo t('backButton')?></a>
                            </div>
                        </form>
                    </div>
                    <!-- Bouton de suppression -->
                    <form method="POST" action="delete_task.php?task_id=<?php echo $taskId; ?>" class="mt-4 p-3">
                        <button type="submit" name="delete_task" class="btn btn-danger w-100">
                            <i class="fa-solid fa-trash me-2"></i> <? echo t('deleteButton')?>
                        </button>
                    </form>
                </div>
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const backButton = document.getElementById("backButton");
        const errorMessage = document.getElementById("errorMessage");

        // Ajout de l'événement "click" au bouton
        backButton.addEventListener("click", () => {
            if (document.referrer) {
                // Si un lien précédent est disponible, redirection
                window.location.href = document.referrer;
            } else {
                // Sinon, affichage d'un message d'erreur
                errorMessage.style.display = "block";
            }
        });
    </script>
</body>

</html>
