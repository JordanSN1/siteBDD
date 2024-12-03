<?php
session_start();
session_destroy(); // Détruit toutes les données de session
header("Location: connexion.php"); // Redirige vers la page de connexion
exit();
?>