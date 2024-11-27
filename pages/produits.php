<?php
// Connexion à la base de données
$host = 'localhost';
$db = 'phantomburger';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}

// Définir la catégorie par défaut comme "tout"
$category = $_GET['category'] ?? 'tout';

// Fonction pour afficher les produits en fonction de la catégorie
function getProducts($pdo, $category)
{
    if ($category == 'menus') {
        // Affiche les menus sans jointure avec les burgers
        $query = "SELECT menu_id, name, description, prix, picture FROM menus";
    } elseif ($category == 'boissons') {
        // Affichage des boissons seules
        $query = "SELECT * FROM boissons";
    } elseif ($category == 'burgers') {
        // Affichage des burgers seuls
        $query = "SELECT * FROM burgers";
    } else {
        // Par défaut, on affiche tout (menus, burgers et boissons)
        $query = "SELECT 'menu' AS type, menu_id AS id, name, description, prix, picture
                  FROM menus
                  UNION
                  SELECT 'burger' AS type, burger_id AS id, name, description, prix, picture
                  FROM burgers
                  UNION
                  SELECT 'boisson' AS type, boisson_id AS id, name, description, prix, picture
                  FROM boissons";
    }

    return $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
}

// Récupérer les produits
$products = getProducts($pdo, $category);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../pages/produits.css">
    <title>PhantomBurger - Produits</title>
</head>

<body>
    <header>
        <?php include '../header/header.php'; ?>
    </header>

    <div class="headband-product">
        <!-- Bannière ou contenu supplémentaire -->
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
                        <div><img src="../img/Cocacola.png" alt=""><span>Boissons</span></div>
                    </a>
                </li>
                <li>
                    <a href="?category=burgers">
                        <div><img src="../img/Burger.png" alt=""><span>Burgers</span></div>
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
                        <?php endif; ?>

                        <h3><?= htmlspecialchars($product['name']); ?></h3>

                        <p><?= htmlspecialchars($product['description']); ?></p>
                        <p><strong>Prix:</strong> €<?= number_format($product['prix'], 2, ',', ' '); ?></p>

                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Aucun produit trouvé pour cette catégorie.</p>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <?php include('../footer/footer.php'); ?>
    </footer>
</body>

</html>