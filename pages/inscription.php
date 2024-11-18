<?php
session_start();

// Vérification de la soumission du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Connexion à la base de données
    include('../scripts/conn.php');

    // Récupération et nettoyage des données du formulaire
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Vérification des champs vides
    if (empty($email) || empty($password) || empty($confirm_password)) {
        $error = "Tous les champs doivent être remplis.";
    } else {
        // Vérification que les mots de passe correspondent
        if ($password !== $confirm_password) {
            $error = "Les mots de passe ne correspondent pas.";
        } else {
            // Vérification si l'email existe déjà
            $stmt = $conn->prepare("SELECT utilisateur_id FROM utilisateurs WHERE email = ?");
            $stmt->bindParam(1, $email, PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $error = "Cet email est déjà utilisé.";
            } else {
                // Hachage du mot de passe
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Insertion dans la base de données
                $stmt = $conn->prepare("INSERT INTO utilisateurs (email, mot_de_passe) VALUES (?, ?)");
                $stmt->bindParam(1, $email, PDO::PARAM_STR);
                $stmt->bindParam(2, $hashed_password, PDO::PARAM_STR);

                if ($stmt->execute()) {
                    $_SESSION['utilisateur_id'] = $conn->lastInsertId(); // Récupérer l'ID de l'utilisateur
                    header("Location: connexion.php");  // Redirection vers la page de connexion
                    exit();
                } else {
                    $error = "Une erreur est survenue lors de l'inscription. Veuillez réessayer.";
                }
            }
        }
    }
}
?>

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
                <form action="inscription.php" method="POST">
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
                        <i class="bx bxs-lock-alt"></i>
                        <!-- Eye icon for showing/hiding password -->
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

    <!-- JavaScript to toggle password visibility -->
    <script>
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
    </script>

</body>

</html>