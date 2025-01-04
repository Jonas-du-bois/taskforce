<?php
// Inclusion des fichiers nécessaires pour charger les classes et fonctions
require_once 'vendor/autoload.php';
require_once 'includes/functions.php';

// Utilisation du gestionnaire de base de données DbManagerCRUD
use M521\Taskforce\dbManager\DbManagerCRUD;

// Initialisation du gestionnaire de base de données
$dbManager = new DbManagerCRUD();

// Démarrage de la session pour accéder aux informations utilisateur
session_start();

// Vérification de l'email de l'utilisateur connecté
$email = $_SESSION['email_user'] ?? null;
if (!$email) {
    // Si l'email n'est pas présent dans la session, bloquer l'accès
    die("Utilisateur non authentifié.");
}

// Récupération des informations de l'utilisateur via son email
$userInfo = getUserByEmail($email, $dbManager);
if (!$userInfo) {
    // Si l'utilisateur n'est pas trouvé dans la base de données, arrêter le script
    die("Utilisateur non trouvé.");
}

// Extraction de l'ID de l'utilisateur pour effectuer des actions spécifiques
$userId = $userInfo->rendId();

// Vérification de la présence d'un ID de tâche valide dans les paramètres GET
$taskId = $_GET['task_id'] ?? null;
if (!$taskId || intval($taskId) <= 0) {
    // Si l'ID est manquant ou invalide, arrêter le script
    die("ID de tâche invalide.");
}

// Récupération de la tâche correspondante depuis la base de données
$task = $dbManager->getTaskById($taskId);
if (!$task) {
    // Si la tâche n'est pas trouvée, rediriger avec un message d'erreur
    $_SESSION['errorMessage'] = "Tâche non trouvée.";
    header("Location: dashboard.php");
    exit; // Arrêter l'exécution après la redirection
}

try {
    // Tentative de suppression de la tâche dans la base de données
    $dbManager->deleteTask($taskId);

    // Stockage d'un message de succès dans la session
    $_SESSION['successMessage'] = "Tâche supprimée avec succès!";
} catch (\Exception $e) {
    // En cas d'erreur lors de la suppression, stockage d'un message d'erreur détaillé
    $_SESSION['errorMessage'] = "Erreur lors de la suppression de la tâche : " . $e->getMessage();
}

// Redirection vers le tableau de bord après suppression ou erreur
header("Location: dashboard.php");
exit; // Arrêter l'exécution après la redirection
?>
