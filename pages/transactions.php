<?php
// Affiche les erreurs PHP pour le débogage
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Inclure la connexion à la base de données
include("../scripts/conn.php");



// Vérifier si des données de panier existent dans la session
if (isset($_POST['cart_data'])) {
  $cart_data = unserialize(urldecode($_POST['cart_data']));

  }

// Vérifier si le formulaire a été soumis via POST

// Affiche les erreurs PHP pour le débogage
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Inclure la connexion à la base de données
include("../scripts/conn.php");

// Vérifier si des données de panier existent dans la session
if (isset($_POST['cart_data'])) {
  $cart_data = unserialize(urldecode($_POST['cart_data']));
}

// Vérifier si le formulaire a été soumis via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Vérifiez si des données de paiement ont été envoyées
    if (isset($_POST['name'], $_POST['card_number'], $_POST['card_type'], $_POST['exp_date'], $_POST['cvv'])) {
        // Inclure le fichier contenant le traitement de la transaction
        include("../scripts/transaction_form.php"); // Ce fichier gère l'insertion dans la base de données
        
        // Vider le panier après la transaction réussie
        unset($_SESSION['cart']); // Cette ligne vide le panier dans la session
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction</title>
    <link rel="stylesheet" href="../styles/style.css">
    <link rel="stylesheet" href="transactions.css">
</head>
<body>
    <header>
        <?php include("../header/header.php"); ?>
    </header>

    <main>
        <div class="card">
            <div class="leftside">
                <img src="../img/hamburguer4.webp" class="product" alt="Produit">
            </div>
            <div class="rightside">
                <form action="transactions.php" method="POST">
                    <h1>Paiement</h1>
                    <h2>Informations de Paiement</h2>
                    <p>Nom du titulaire de la carte</p>
                    <input type="text" class="inputbox" name="name" required />

                    <p>Numéro de carte</p>
                    <input type="text" class="inputbox" name="card_number" pattern="\d{16}" maxlength="16" title="Le numéro de carte doit comporter exactement 16 chiffres." oninput="this.value = this.value.replace(/[^0-9]/g, '')" required />

                    <p>Type de carte</p>
                    <select class="inputbox" name="card_type" id="card_type" required>
                        <option value="">--Sélectionnez un type de carte--</option>
                        <option value="Visa">Visa</option>
                        <option value="RuPay">RuPay</option>
                        <option value="MasterCard">MasterCard</option>
                    </select>

                    <div class="expcvv">
                        <p class="expcvv_text">Date d'expiration</p>
                        <input type="date" class="inputbox" name="exp_date" id="exp_date" required />

                        <p class="expcvv_text2">CVV</p>
                        <input type="password" class="inputbox" name="cvv" id="cvv" pattern="\d{3}" oninput="this.value = this.value.replace(/[^0-9]/g, '')" maxlength="3" required />
                    </div>

                    <?php
                    // Afficher le total du panier
                    $total_price = 0;
                    foreach ($cart_data as $item) {
                        $total_price += $item['price'] * $item['quantity']; // Calcul du total
                    }
                    echo "<h2 style='margin-top: 20px;'>Total : €" . number_format($total_price, 2, ',', ' ') . "</h2>";
                    ?>

                    <button type="submit" class="button">Payer</button>
                </form>
            </div>
        </div>
    </main>
    <h1 style="margin-bottom: 250px;"></h1>
    <footer>
        <?php include("../footer/footer.php"); ?>
    </footer>
</body>
</html>

<style>
    <?php include("../styles/style-transaction.css"); ?>
</style>
