<?php
// Démarre la session PHP et récupère la valeur de 'success' dans l'URL si elle est définie
$success = isset($_GET['success']) ? $_GET['success'] : 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/style-contact.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">

    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css"
        integrity="sha512-+4zCK9k+qNFUR5X+cKL9EIR+ZOhtIloNl9GIKS57V1MyNsYpYcUrUeQc9vNfzsWfV28IaLL3i96P9sdNyeRssA=="
        crossorigin="anonymous" />

    <title>Document</title>
</head>

<body>
    <header>
        <?php include("../header/header.php"); ?>
    </header>

    <div class="wrapper">
        <h1>Contactez Nous</h1>
    </div>

    <section class="contact-section">
        <div class="contact-body">
            <div class="contact-info">
                <div>
                    <span><i class="fas fa-mobile-alt"></i></span>
                    <span>Numéro de téléphone</span>
                    <span class="text">+330611124281</span>
                </div>
                <div>
                    <span><i class="fas fa-envelope-open"></i></span>
                    <span>E-mail</span>
                    <span class="text">phantomburguer@gmail.com</span>
                </div>
                <div>
                    <span><i class="fas fa-map-marker-alt"></i></span>
                    <span>Addresse</span>
                    <span class="text">20 Bis Jardins Boiseldieu,92800,Puteaux</span>
                </div>
                <div>
                    <span><i class="fas fa-clock"></i></span>
                    <span>Horaires</span>
                    <span class="text">Lundi - Vendredi (11:00 h to 23:00 h)</span>
                    <span class="text">Samedi-Dimanche-Jours Fériés (11:00 h to 21:00 h)</span>
                </div>
            </div>

            <div class="contact-form" style="resize: none;">
                <form id="contact-form" method="POST">
                    <p><strong>Entrez vos informations</strong></p>
                    <div>
                        <input type="text" name="nom" class="form-control" placeholder="Nom" required
                            style="resize: none;">
                        <input type="text" name="prenom" class="form-control" placeholder="Prénom" required
                            style="resize: none;">
                    </div>
                    <div>
                        <input type="email" name="email" class="form-control" placeholder="E-mail" required
                            style="resize: none;">
                        <input type="text" name="telephone" class="form-control" placeholder="Téléphone" required
                            style="resize: none;">
                    </div>
                    <textarea rows="5" name="message" placeholder="Message" class="form-control" required
                        style="resize: none;"></textarea>
                    <input type="submit" class="send-btn" value="Envoyer un message" style="resize: none;">
                </form>


                <div>
                    <img src="../img/contact.gif" alt="">
                </div>
            </div>
        </div>

        <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2623.4518624814505!2d2.2351799759089905!3d48.88772507133613!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47e665caee7aa6cb%3A0xa2669dbf6c1ea643!2sEPSI%20-%20Ecole%20d%E2%80%99ing%C3%A9nierie%20informatique%20-%20Paris!5e0!3m2!1sfr!2sfr!4v1731426881269!5m2!1sfr!2sfr"
            width="100%" height="350" style="border:0;" allowfullscreen="" loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"></iframe>
    </section>

    <footer>
        <?php include("../footer/footer.php"); ?>
    </footer>

    <script>
        // Ajoutez ici vos scripts
        <?php include("../src/scripts.js"); ?>

        // Affiche la notification de succès si le paramètre success=1 est présent
        <?php if ($success == 1): ?>
            openNotification();
        <?php endif; ?>
    </script>
</body>

</html>

<style>
    <?php include("../styles/style-contact.css"); ?>
</style>