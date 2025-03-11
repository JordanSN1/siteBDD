<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/style-cart.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <title>Votre Panier</title>
</head>

<body>
    <header>
        <?php include("../header/header.php"); ?>
    </header>

    <h1 style="margin-bottom: 150px;"></h1>

    <?php
    include("../scripts/cart_form.php");
    // Masquer les erreurs PHP
    error_reporting(0);
    ini_set('display_errors', 0);
    ?>

    <div class="cart-container">
        <div class="cart-items">
            <h1 style="color:black;"> Panier</h1>
            <?php if (!empty($_SESSION['cart'])): ?>
                <?php foreach ($_SESSION['cart'] as $id => $item): ?>
                    <div class="cart-item">
                        <img src="../img/<?= htmlspecialchars($item['picture']); ?>"
                            alt="<?= htmlspecialchars($item['name']); ?>">
                        <div class="item-details">
                            <h2><?= htmlspecialchars($item['name']); ?></h2>
                            <p>Prix : €<?= number_format($item['price'], 2, ',', ' '); ?></p>
                            <p>Quantité : <?= $item['quantity']; ?></p>
                            <form action="cart.php?action=update&name=<?= urlencode($item['name']); ?>" method="post"
                                style="display: inline;">
                                <button type="submit" class="plus-btn"><i class="fa fa-plus"></i></button>
                            </form>
                        </div>
                        <a href="cart.php?action=remove&name=<?= urlencode($item['name']); ?>" class="remove-btn"><i
                                class="fa fa-minus"></i></a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Votre panier est vide.</p>
            <?php endif; ?>
        </div>

        <!-- Résumé -->
        <div class="cart-summary">
            <?php
            $total = 0;
            if (!empty($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] as $item) {
                    $total += $item['price'] * $item['quantity'];
                }
            }
            ?>
            <h3>Total : €<?= number_format($total, 2, ',', ' '); ?></h3>

            <!-- Vérification si le panier est vide avant d'afficher le bouton "Passer à la caisse" -->
            <?php if (!empty($_SESSION['cart'])): ?>
                <form action="transactions.php" method="post">
                    <input type="hidden" name="cart_data" value="<?= urlencode(serialize($_SESSION['cart'])); ?>">
                    <button type="submit" class="checkout-btn">Passer à la caisse</button>
                </form>
            <?php else: ?>
                <p><strong>Votre panier est vide, vous devez ajouter des articles pour passer à la caisse.</strong></p>

            <?php endif; ?>
        </div>
    </div>

    <h1 style="margin-bottom: 40px;"></h1>
    <footer>
        <?php include("../footer/footer.php"); ?>
    </footer>
</body>

</html>

<style>
    <?php include("../styles/style-cart.css"); ?>
</style>