<?php
// Connexion à la base de données 
$host = 'localhost';
$db = 'phantomburger';
$user = 'root';  // Modifie ceci si tu as un utilisateur spécifique
$pass = '';      // Modifie ceci si tu as un mot de passe spécifique

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

// Définir la catégorie par défaut comme "tout"
$category = $_GET['category'] ?? 'tout';

// Fonction pour afficher les produits en fonction de la catégorie
function getProducts($pdo, $category)
{
    if ($category == 'menus') {
        // Jointure pour récupérer les informations des menus avec les burgers et les boissons
        $query = "SELECT menus.menu_id, menus.name, menus.description, menus.prix,
                         burgers.name AS burger_name, burgers.picture AS burger_picture, burgers.description AS burger_description,
                         boissons.name AS boisson_name, boissons.picture AS boisson_picture, menus.picture AS menu_picture
                  FROM menus
                  INNER JOIN burgers ON menus.burger_id = burgers.burger_id
                  INNER JOIN boissons ON menus.boisson_id = boissons.boisson_id";
    } elseif ($category == 'boissons') {
        // Affichage des boissons seules
        $query = "SELECT * FROM boissons";
    } elseif ($category == 'burgers') {
        // Affichage des burgers seuls
        $query = "SELECT * FROM burgers";
    } else {
        // Par défaut, on affiche tout (menus, burgers et boissons)
        $query = "SELECT 'menu' AS type, menus.menu_id AS id, menus.name AS name, menus.description, menus.prix, burgers.picture AS picture
                  FROM menus
                  INNER JOIN burgers ON menus.burger_id = burgers.burger_id
                  UNION
                  SELECT 'burger' AS type, burger_id AS id, name, description, prix, picture
                  FROM burgers
                  UNION
                  SELECT 'boisson' AS type, boisson_id AS id, name, description, prix, picture
                  FROM boissons";
    }

    return $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
}

$products = getProducts($pdo, $category);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../pages/produits.css">
    <title>PhantomBurger - produits</title>
</head>

<body>
    <header>
        <?php include '../header/header.php'; ?>
    </header>

    <div class="headband-product">
        <!-- Ajoute ici des éléments pour ta bannière si nécessaire -->
    </div>

    <div class="product-wrapper">
        <div class="product-sort">
            <ul>
                <li>
                    <a href="?category=tout">
                        <div><img src="../img/menu.png" alt=""><span>Tout</span></div>
                    </a>
                </li>
                <li>
                    <a href="?category=menus">
                        <div><img src="../img/food.96f3eaa.svg" alt=""><span>Menus</span></div>
                    </a>
                </li>
                <li>
                    <a href="?category=boissons">
                        <div><img src="../img/Burger.png" alt=""><span>Boissons</span></div>
                    </a>
                </li>
                <li>
                    <a href="?category=burgers">
                        <div><img src="../img/Cocacola.png" alt=""><span>Burgers</span></div>
                    </a>
                </li>
            </ul>
        </div>

        <div class="products-list">
            <?php if (count($products) > 0): ?>
                <?php foreach ($products as $product): ?>
                    <div class="product-item">
                        <?php if (isset($product['picture'])): ?>
                            <img src="../img/<?= htmlspecialchars($product['picture']); ?>"
                                alt="<?= htmlspecialchars($product['name']); ?>">
                        <?php elseif (isset($product['menu_picture'])): ?>
                            <img src="../img/<?= htmlspecialchars($product['menu_picture']); ?>"
                                alt="<?= htmlspecialchars($product['name']); ?>">
                        <?php elseif (isset($product['boisson_picture'])): ?>
                            <img src="../img/<?= htmlspecialchars($product['boisson_picture']); ?>"
                                alt="<?= htmlspecialchars($product['name']); ?>">
                        <?php endif; ?>

                        <h3>
                            <?= htmlspecialchars($product['name']); ?>
                        </h3>

                        <p>
                            <?= htmlspecialchars($product['description']); ?>
                        </p>
                        <p><strong>Prix:</strong> €<?= number_format($product['prix'], 2, ',', ' '); ?></p>
                        <a href="#" class="add-to-cart">Ajouter au panier</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <?php include '../footer/footer.php'; ?>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <?php include('../footer/footer.php'); ?>
    </footer>

</body>

</html>