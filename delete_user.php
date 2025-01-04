<?php
// Inclusion des fichiers nécessaires pour charger les classes et fonctions
require_once 'vendor/autoload.php';
require_once 'includes/functions.php';
// Lien vers le fichier de fonction pour le multilangue
require_once('lang' . DIRECTORY_SEPARATOR . 'lang_func.php');

// Utilisation du gestionnaire de base de données DbManagerCRUD
use M521\Taskforce\dbManager\DbManagerCRUD;

// Initialisation du gestionnaire de base de données
$dbManager = new DbManagerCRUD();

// Démarrage de la session pour vérifier l'utilisateur connecté
session_start();

// Vérification de l'email de l'utilisateur connecté
$email = $_SESSION['email_user'] ?? null;
if (!$email) {
    // Si l'email n'est pas présent dans la session, bloquer l'accès
    die(t('admin_user_only')); // Utilisation du dictionnaire
}

// Vérification des privilèges administratifs (si nécessaire)
if ($email !== 'jonas.du.bois@outlook.com') {
    die(t('admin_user_only')); // Utilisation du dictionnaire
}

// Vérification de la présence d'un ID utilisateur valide dans les paramètres GET
$userIdToDelete = $_GET['user_id'] ?? null;
if (!$userIdToDelete || intval($userIdToDelete) <= 0) {
    // Si l'ID est manquant ou invalide, arrêter le script
    die("ID utilisateur invalide.");
}

// Vérification que l'utilisateur à supprimer existe
$userToDelete = $dbManager->getUserById($userIdToDelete);
if (!$userToDelete) {
    // Si l'utilisateur n'est pas trouvé, rediriger avec un message d'erreur
    $_SESSION['errorMessage'] = t('no_users_found'); // Utilisation du dictionnaire
    header("Location: admin.php");
    exit; // Arrêter l'exécution après la redirection
}

// Vérification pour éviter qu'un administrateur ne supprime son propre compte
if ($userIdToDelete == $_SESSION['user_id']) {
    $_SESSION['errorMessage'] = "Vous ne pouvez pas supprimer votre propre compte.";
    header("Location: admin.php");
    exit;
}

try {
    // Tentative de suppression de l'utilisateur dans la base de données
    $dbManager->supprimePersonne($userIdToDelete);

    // Stockage d'un message de succès dans la session
    $_SESSION['successMessage'] = t('user_deleted'); // Utilisation du dictionnaire
} catch (\Exception $e) {
    // En cas d'erreur lors de la suppression, stockage d'un message d'erreur détaillé
    $_SESSION['errorMessage'] = t('error_message_admin') . " " . $e->getMessage(); // Utilisation du dictionnaire
}

// Redirection vers la page admin après suppression ou erreur
header("Location: admin.php");
exit; // Arrêter l'exécution après la redirection
?>
