<?php
/**
 * Page de déconnexion (logout)
 * ----------------------------
 * Met fin à la session de l’utilisateur, le déconnecte et le redirige vers la page d'accueil
 */

session_start(); // Commence une session
session_destroy(); // Détruit la session
header('Location: index.php'); // Redirige vers la page d'accueil
exit;


