<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>Inscription - PhantomBurger</title>
    <style>
        /* Custom styles for the eye icon */
        .eye-icon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
        }

        /* Styles for the success popup */
        .popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #4CAF50;
            color: white;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .popup .close-btn {
            background-color: transparent;
            border: none;
            color: white;
            font-size: 18px;
            cursor: pointer;
            margin-top: 10px;
        }
    </style>
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
                <li><a href="a-propos.php">À propos</a></li>
                <li><a href="contact.php">Contact</a></li>
                <?php
                if (isset($_SESSION['utilisateur_id'])) {
                    echo '<a class="mobile-panier" href="panier.php">Panier</a>';
                } else {
                    echo '<a class="mobile-connect" href="connexion.php">Connexion</a>';
                }
                ?>
            </ul>

            <div class="user-actions">
                <?php
                if (isset($_SESSION['utilisateur_id'])) {
                    echo '<a class="panier" href="panier.php">Panier</a>';
                } else {
                    echo '<a class="connect" href="connexion.php">Connecter</a>';
                }
                ?>
            </div>
        </nav>
    </header>

    <div class="wrapper">
        <div class="container">
            <div class="form-box">
                <form action="inscription.php" method="POST" id="registration-form">
                    <h2>Inscription</h2>

                    <?php
                    // Affichage des erreurs s'il y en a
                    if (isset($error)) {
                        echo "<p class='error'>$error</p>";
                    }
                    ?>

                    <div class="input-box">
                        <input type="email" name="email" required>
                        <label>Email</label>
                        <i class='bx bxs-envelope'></i>
                    </div>

                    <div class="input-box">
                        <input type="password" name="password" id="password" required>
                        <label>Mot de passe</label>

                        <i class="bx bxs-show eye-icon" id="eye-icon"></i>
                    </div>

                    <div class="input-box">
                        <input type="password" name="confirm_password" id="confirm_password" required>
                        <label>Confirmer le mot de passe</label>
                        <i class="bx bxs-lock-alt"></i>
                    </div>

                    <button type="submit" class="btn">S'inscrire</button>

                    <div class="account-creation">
                        <span>Vous avez déjà un compte ? <a href="connexion.php" class="inscr-btn">Se
                                connecter</a></span>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Popup de succès -->
    <?php
    if (isset($_SESSION['registration_success']) && $_SESSION['registration_success'] === true) {
        echo '<div class="popup" id="success-popup">
                <p>Inscription réussie ! Vous pouvez maintenant vous connecter.</p>
                <button class="close-btn" onclick="closePopup()">Fermer</button>
              </div>';
        unset($_SESSION['registration_success']);  // Supprime le flag après l'affichage
    }
    ?>

    <!-- JavaScript pour gérer le popup et la visibilité du mot de passe -->
    <script>
        // Toggle password visibility
        const eyeIcon = document.getElementById('eye-icon');
        const passwordField = document.getElementById('password');
        const confirmPasswordField = document.getElementById('confirm_password');

        eyeIcon.addEventListener('click', function () {
            // Toggle password visibility
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeIcon.classList.remove('bxs-show');
                eyeIcon.classList.add('bxs-hide');
            } else {
                passwordField.type = 'password';
                eyeIcon.classList.remove('bxs-hide');
                eyeIcon.classList.add('bxs-show');
            }
        });

        // Close the success popup
        function closePopup() {
            document.getElementById('success-popup').style.display = 'none';
        }
    </script>

</body>

</html>