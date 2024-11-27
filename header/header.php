<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/style.css">

    <title>Produits - PhantomBurger</title>
</head>

<body>
    <header>
        <nav>
            <div class="logo">
                <img src="../assets/phantomBurgerlogo.png" alt="PhantomBurger logo">
            </div>

            <!-- Menu burger pour mobile -->
            <div class="menu-toggle" id="menu-toggle">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </div>

            <!-- Liens de navigation principaux -->
            <ul class="nav-links" id="nav-links">
                <li><a href="index.php">Accueil</a></li>
                <li><a href="produits.php">Produits</a></li>
                <li><a href="about.php">À propos</a></li>
                <li><a href="contact.php">Contact</a></li>
                <!-- Liens utilisateur (se connecter/panier) visibles uniquement en version mobile -->
                <li class="mobile-user-actions"></li>
                </li>
                <?php
                session_start();
                if (isset($_SESSION['user_id'])) {
                    echo '<a class="mobile-panier" href="panier.php">Panier</a>';
                } else {
                    echo '<a class="mobile-connect" href="login.php">Se connecter</a>';
                }
                ?>
                </li>
            </ul>

            <!-- Liens utilisateur (se connecter/panier) visibles uniquement en version desktop -->
            <div class="user-actions">
                <?php

                if (isset($_SESSION['user_id'])) {
                    echo '<a class="panier" href="panier.php">Panier</a>';
                } else {
                    echo '<a class="connect" href="connexion.php">connecter</a>';
                }
                ?>
            </div>
        </nav>
    </header>