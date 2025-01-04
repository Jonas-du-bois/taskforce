<?php
session_start(); // Commence une session
session_destroy(); // Détruit la session
header('Location: index.php'); // Redirige vers la page d'accueil ou de connexion
exit;


