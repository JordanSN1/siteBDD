<?php
session_start();
include('../scripts/conn.php'); // Connexion à la base de données

// Initialisation des messages d'erreur ou de succès
$error = '';

// Fonction pour valider le mot de passe
function validatePassword($password)
{
    // Vérifie que le mot de passe a au moins 8 caractères
    if (strlen($password) < 8) {
        return "Le mot de passe doit contenir au moins 8 caractères.";
    }

    // Vérifie qu'il contient au moins une lettre (majuscule ou minuscule)
    if (!preg_match('/[a-zA-Z]/', $password)) {
        return "Le mot de passe doit contenir au moins une lettre.";
    }

    // Vérifie qu'il contient au moins un chiffre
    if (!preg_match('/\d/', $password)) {
        return "Le mot de passe doit contenir au moins un chiffre.";
    }

    // Vérifie qu'il contient au moins un caractère spécial
    if (!preg_match('/[\W_]/', $password)) {
        return "Le mot de passe doit contenir au moins un caractère spécial.";
    }

    return true;
}

// Vérification de la soumission du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération des données et nettoyage
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Vérification des champs
    if (empty($nom) || empty($prenom) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "Tous les champs sont obligatoires.";
    } elseif ($password !== $confirm_password) {
        $error = "Les mots de passe ne correspondent pas.";
    } else {
        // Validation du mot de passe
        $passwordValidation = validatePassword($password);
        if ($passwordValidation !== true) {
            $error = $passwordValidation;
        } else {
            try {
                // Vérification si l'email existe déjà
                $stmt = $conn->prepare("SELECT email FROM utilisateurs WHERE email = ?");
                $stmt->execute([$email]);

                if ($stmt->fetch()) {
                    $error = "Cet email est déjà utilisé.";
                } else {
                    // Hashage du mot de passe
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                    // Insertion dans la base de données
                    $stmt = $conn->prepare("INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, date_inscription, role_id) 
                                        VALUES (:nom, :prenom, :email, :mot_de_passe, NOW(), :role_id)");
                    $stmt->execute([
                        ':nom' => $nom,
                        ':prenom' => $prenom,
                        ':email' => $email,
                        ':mot_de_passe' => $hashed_password,
                        ':role_id' => 3 // 3 = Utilisateur
                    ]);

                    $_SESSION['registration_success'] = true;
                    header("Location: inscription.php");
                    exit();
                }
            } catch (Exception $e) {
                $error = "Erreur lors de l'inscription : " . $e->getMessage();
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
        .eye-icon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
        }

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

        .error {
            color: red;
            text-align: center;
        }
    </style>
</head>

<body>
    <header>
        <nav>
            <div class="logo">
                <img src="../assets/phantomBurgerlogo.png" alt="PhantomBurger logo">
            </div>
            <ul class="nav-links">
                <li><a href="index.php">Accueil</a></li>
                <li><a href="produits.php">Produits</a></li>
                <li><a href="a-propos.php">À propos</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
        </nav>
    </header>

    <div class="wrapper">
        <div class="container">
            <div class="form-box">
                <form action="inscription.php" method="POST" id="registration-form">
                    <h2>Inscription</h2>
                    <!-- Affichage des messages d'erreur -->
                    <?php if (!empty($error)) {
                        echo "<p class='error'>$error</p>";
                    } ?>

                    <!-- Champ pour Nom et Prénom -->
                    <div class="input-row">
                        <div class="input-box">
                            <input type="text" name="nom" required>
                            <label>Nom</label>
                            <i class='bx bxs-user'></i>
                        </div>
                        <div class="input-box">
                            <input type="text" name="prenom" required>
                            <label>Prénom</label>
                            <i class='bx bxs-user'></i>
                        </div>
                    </div>

                    <!-- Champ pour Email -->
                    <div class="input-box">
                        <input type="email" name="email" required>
                        <label>Email</label>
                        <i class='bx bxs-envelope'></i>
                    </div>

                    <!-- Champ pour le mot de passe -->
                    <div class="input-box">
                        <input type="password" name="password" id="password" required>
                        <label>Mot de passe</label>
                        <i class="bx bxs-show eye-icon" id="eye-icon"></i>
                    </div>

                    <!-- Champ pour la confirmation du mot de passe -->
                    <div class="input-box">
                        <input type="password" name="confirm_password" required>
                        <label>Confirmer le mot de passe</label>
                        <i class="bx bxs-lock-alt"></i>
                    </div>

                    <button type="submit" class="btn">S'inscrire</button>
                    <div class="account-creation">
                        <span>Vous avez déjà un compte ? <a href="connexion.php">Se connecter</a></span>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Popup de succès -->
    <?php if (isset($_SESSION['registration_success']) && $_SESSION['registration_success'] === true): ?>
        <div class="popup" id="success-popup">
            <p>Inscription réussie ! Vous allez être redirigé vers la page de connexion.</p>
        </div>
        <script>
            // Affichage de la popup
            document.getElementById('success-popup').style.display = 'block';

            // Redirection après 3 secondes
            setTimeout(function () {
                window.location.href = "connexion.php";
            }, 2500); // 2500 millisecondes = 2.5 secondes
        </script>
        <?php unset($_SESSION['registration_success']); endif; ?>

    <!-- JavaScript pour la visibilité du mot de passe -->
    <script>
        const eyeIcon = document.getElementById('eye-icon');
        const passwordField = document.getElementById('password');
        eyeIcon.addEventListener('click', function () {
            passwordField.type = passwordField.type === 'password' ? 'text' : 'password';
            eyeIcon.classList.toggle('bxs-show');
            eyeIcon.classList.toggle('bxs-hide');
        });
    </script>
</body>

</html>