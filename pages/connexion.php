<?php
session_start();

// Vérification si l'utilisateur est déjà connecté
if (isset($_SESSION['utilisateur_id_'])) {
    header("Location: index.php");
    exit();
}

// Vérification de la soumission du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Connexion à la base de données
    include('../scripts/conn.php');  // Assurez-vous que ce fichier contient une connexion PDO

    // Vérification que la connexion PDO est correcte
    if (!$conn instanceof PDO) {
        die("Erreur de connexion à la base de données.");
    }

    // Récupération et nettoyage des données du formulaire
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Vérification des champs vides
    if (empty($email) || empty($password)) {
        $error = "Tous les champs doivent être remplis.";
    } else {
        try {
            // Requête pour obtenir les informations de l'utilisateur (y compris le role_id)
            $stmt = $conn->prepare("SELECT utilisateur_id_, mot_de_passe, role_id FROM utilisateurs WHERE email = :email");
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();

            // Vérification si l'utilisateur existe
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // Vérification du mot de passe
                if (password_verify($password, $user['mot_de_passe'])) {
                    // Authentification réussie, démarrage de la session
                    $_SESSION['utilisateur_id_'] = $user['utilisateur_id_'];
                    $_SESSION['role_id'] = $user['role_id'];
                    if ($user['role_id'] == 1) {
                        // Si le rôle est client, redirection vers la page admin
                        header("Location: admin.php");
                    }
                    // Redirection en fonction du role_id
                    else if ($user['role_id'] == 2) {
                        // Si le rôle est admin, redirection vers la page admin
                        header("Location: administrateur.php");
                    } else {
                        // Sinon, redirection vers la page index (ou une autre page par défaut)
                        header("Location: index.php");
                    }
                    exit();
                } else {
                    $error = "Mot de passe incorrect.";
                }
            } else {
                $error = "Aucun compte associé à cet email.";
            }

        } catch (Exception $e) {
            $error = "Une erreur est survenue lors de la connexion : " . $e->getMessage();
        }
    }
}
?>

<!-- HTML Code (rest remains the same) -->
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>Connexion - PhantomBurger</title>
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
                if (isset($_SESSION['utilisateur_id_'])) {
                    echo '<a class="mobile-panier" href="panier.php">Panier</a>';
                } else {
                    echo '<a class="mobile-connect" href="connexion.php">Inscription</a>';
                }
                ?>
            </ul>

            <div class="user-actions">
                <?php
                if (isset($_SESSION['utilisateur_id_'])) {
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
                <form action="connexion.php" method="POST">
                    <h2>Connexion</h2>

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
                    <div class="forget-section">
                        <p>
                            <input type="checkbox" name="remember">
                            Se souvenir de moi
                        </p>
                        <a href="#" class="forg-btn">Mot de passe oublié</a>
                    </div>
                    <button type="submit" class="btn">Connexion</button>
                    <div class="account-creation">
                        <span>Vous n'avez pas de compte ? <a href="inscription.php"
                                class="inscr-btn">S'inscrire</a></span>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript to toggle password visibility -->
    <script>
        const eyeIcon = document.getElementById('eye-icon');
        const passwordField = document.getElementById('password');

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