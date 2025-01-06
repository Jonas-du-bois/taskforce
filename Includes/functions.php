<?php

/**
 * Fonction pour récupérer les informations d'un utilisateur à partir de son email.
 * 
 * @param string $email L'email de l'utilisateur à rechercher.
 * @param object $dbManager L'objet de gestion de la base de données.
 * 
 * @return object|null L'objet utilisateur trouvé, ou null si aucun utilisateur n'est trouvé.
 */
function getUserByEmail($email, $dbManager) {
    // Appel à la méthode getUser pour obtenir un tableau d'utilisateurs correspondant à l'email
    $userInfoArray = $dbManager->getUser($email);
    
    // Si le tableau est non vide, on retourne le premier utilisateur, sinon on retourne null
    return !empty($userInfoArray) ? $userInfoArray[0] : null;
}

/**
 * Fonction pour récupérer la classe CSS correspondant au statut d'une tâche.
 * 
 * @param string $statut Le statut de la tâche (À faire, En cours, Terminé).
 * 
 * @return string La classe CSS associée au statut.
 */
function getStatusBadgeClass(string $statut): string
{
    // Tableau associatif des statuts avec leurs classes CSS correspondantes
    $badgeClasses = [
        'À faire' => 'bg-danger',   // Rouge pour "À faire"
        'En cours' => 'bg-warning', // Jaune pour "En cours"
        'Terminé' => 'bg-success',  // Vert pour "Terminé"
    ];

    // Si le statut existe dans le tableau, on retourne la classe correspondante, sinon on retourne 'bg-secondary' (gris)
    return $badgeClasses[$statut] ?? 'bg-secondary';  // Par défaut, gris
}

function getStatusClasses($status)
{
    // Définir les couleurs des bordures et des fonds
    switch ($status) {
        case 'À faire':
            return 'border-danger bg-danger bg-opacity-25'; // Bordure rouge et fond rouge transparent
        case 'En cours':
            return 'border-warning bg-warning bg-opacity-25'; // Bordure orange et fond orange transparent
        case 'Terminé':
            return 'border-success bg-success bg-opacity-25'; // Bordure verte et fond vert transparent
        default:
            return 'border-secondary bg-secondary bg-opacity-25'; // Bordure grise et fond gris transparent
    }
}

/**
 * Fonction pour déterminer l'ordre de tri des données basé sur les paramètres GET de la requête.
 * 
 * @param string $column Le nom de la colonne sur laquelle trier.
 * 
 * @return string L'ordre de tri : 'ASC' ou 'DESC'.
 */
function getSortOrder(string $column): string
{
    // Récupère l'ordre actuel (ascendant ou descendant) de la requête GET, par défaut 'ASC'
    $currentOrder = $_GET['order'] ?? 'ASC';

    // Récupère la colonne de tri actuelle
    $currentSort = $_GET['sort'] ?? '';
    
    // Si l'utilisateur clique à nouveau sur la même colonne, on inverse l'ordre de tri
    if ($currentSort === $column) {
        return $currentOrder === 'ASC' ? 'DESC' : 'ASC';
    }

    // Par défaut, on trie par ordre croissant
    return 'ASC'; // Ordre par défaut
}
?>
