<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
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

            <div class="menu-toggle" id="menu-toggle">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </div>

            <ul class="nav-links" id="nav-links">
                <li><a href="index.php">Accueil</a></li>
                <li><a href="produits.php">Produits</a></li>
                <li><a href="about.php">À propos</a></li>
                <li><a href="contact.php">Contact</a></li>

                <!-- Liens utilisateur (se connecter/panier) visibles uniquement en version mobile -->
                <li class="mobile-user-actions"></li>
                <?php
                if (isset($_SESSION['user_id'])) {
                    echo '<a class="mobile-panier" href="panier.php">Panier</a>';
                } else {
                    echo '<a class="mobile-connect" href="login.php">Se connecter</a>';
                }
                ?>

            </ul>

            <div class="user-actions">
                <?php if (isset($_SESSION['utilisateur_id_'])): ?>
                    <a class="panier" href="cart.php">Panier</a>
                    <a class="deconnexion-btn" href="deconnexion.php">Déconnexion</a>
                <?php else: ?>
                    <a class="connect" href="connexion.php">Connecter</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>
</body>

</html>