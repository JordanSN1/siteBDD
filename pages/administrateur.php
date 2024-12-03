<?php

$host = 'localhost';
$db = 'phantomburger';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_product') {
    $type = $_POST['type'] ?? '';
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $prix = $_POST['prix'] ?? 0;
    $picture = $_POST['picture'] ?? '';

    if ($type && $name && $prix && $picture) {
        if ($type === 'menus') {
            // Ajout du menu
            $query = "INSERT INTO menus (name, description, prix, picture) VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$name, $description, $prix, $picture]);
            $menu_id = $pdo->lastInsertId();

            // Ajout des relations avec les burgers
            if (isset($_POST['burgers']) && is_array($_POST['burgers'])) {
                foreach ($_POST['burgers'] as $burger_id) {
                    $query = "INSERT INTO burgers_appartient (menu_id, burger_id) VALUES (?, ?)";
                    $stmt = $pdo->prepare($query);
                    $stmt->execute([$menu_id, $burger_id]);
                }
            }

            // Ajout des relations avec les boissons
            if (isset($_POST['boissons']) && is_array($_POST['boissons'])) {
                foreach ($_POST['boissons'] as $boisson_id) {
                    $query = "INSERT INTO boissons_appartient (menu_id, boisson_id) VALUES (?, ?)";
                    $stmt = $pdo->prepare($query);
                    $stmt->execute([$menu_id, $boisson_id]);
                }
            }
        } elseif ($type === 'boissons') {
            $query = "INSERT INTO boissons (name, description, prix, picture) VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$name, $description, $prix, $picture]);
        } elseif ($type === 'burgers') {
            $query = "INSERT INTO burgers (name, description, prix, picture) VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$name, $description, $prix, $picture]);
        }
    }
}

if (isset($_GET['delete_id']) && isset($_GET['type'])) {
    $delete_id = $_GET['delete_id'];
    $type = $_GET['type'];

    if ($type === 'menu') {
        $query = "DELETE FROM menus WHERE menu_id = ?";
    } elseif ($type === 'burger') {
        $query = "DELETE FROM burgers WHERE burger_id = ?";
    } elseif ($type === 'boisson') {
        $query = "DELETE FROM boissons WHERE boisson_id = ?";
    }

    $stmt = $pdo->prepare($query);
    $stmt->execute([$delete_id]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit_product') {
    $edit_id = $_POST['edit_id'] ?? '';
    $type = $_POST['type'] ?? '';
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $prix = $_POST['prix'] ?? 0;
    $picture = $_POST['picture'] ?? '';

    if ($type && $name && $prix && $picture && $edit_id) {
        if ($type === 'menus') {
            $query = "UPDATE menus SET name = ?, description = ?, prix = ?, picture = ? WHERE menu_id = ?";
        } elseif ($type === 'boissons') {
            $query = "UPDATE boissons SET name = ?, description = ?, prix = ?, picture = ? WHERE boisson_id = ?";
        } elseif ($type === 'burgers') {
            $query = "UPDATE burgers SET name = ?, description = ?, prix = ?, picture = ? WHERE burger_id = ?";
        }

        $stmt = $pdo->prepare($query);
        $stmt->execute([$name, $description, $prix, $picture, $edit_id]);
    }
}

$category = $_GET['category'] ?? 'tout';

function getProducts($pdo, $category)
{
    if ($category == 'menus') {
        $query = "SELECT menu_id AS id, name, description, prix, picture, 'menu' AS type FROM menus";
    } elseif ($category == 'boissons') {
        $query = "SELECT boisson_id AS id, name, description, prix, picture, 'boisson' AS type FROM boissons";
    } elseif ($category == 'burgers') {
        $query = "SELECT burger_id AS id, name, description, prix, picture, 'burger' AS type FROM burgers";
    } else {
        $query = "SELECT menu_id AS id, name, description, prix, picture, 'menu' AS type FROM menus
                  UNION
                  SELECT burger_id AS id, name, description, prix, picture, 'burger' AS type FROM burgers
                  UNION
                  SELECT boisson_id AS id, name, description, prix, picture, 'boisson' AS type FROM boissons";
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
    <style>
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 5px;
        }

        button:hover {
            background-color: #45a049;
        }

        .product-item button {
            margin-top: 10px;
        }

        #addProductForm,
        #editProductForm {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.3);
            border-radius: 10px;
            z-index: 1000;
        }

        #addProductForm label,
        #editProductForm label {
            display: block;
            margin-top: 10px;
        }

        #addProductForm input,
        #addProductForm select,
        #editProductForm input,
        #editProductForm select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
        }

        #addProductForm button,
        #editProductForm button {
            margin-top: 15px;
        }

        #overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        .menu-options-wrapper {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 15px;
        }

        .menu-options-wrapper label {
            font-weight: bold;
        }
    </style>
    <title>PhantomBurger - Produits</title>
</head>

<body>
    <header>
        <?php include '../header/header.php'; ?>
    </header>

    <div class="headband-product"></div>

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
                <li>
                    <button onclick="openAddProductForm()" style="margin-left: 20px;">Ajouter Un Produit</button>
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
                        <a href="?delete_id=<?= $product['id']; ?>&type=<?= $product['type']; ?>"
                            onclick="return confirm('Voulez-vous vraiment supprimer ce produit ?');">
                            <button type="button">Supprimer</button>
                        </a>
                        <button type="button"
                            onclick="openEditProductForm('<?= $product['id']; ?>', '<?= $product['type']; ?>', '<?= htmlspecialchars($product['name']); ?>', '<?= htmlspecialchars($product['description']); ?>', '<?= $product['prix']; ?>', '<?= htmlspecialchars($product['picture']); ?>')">Modifier</button>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Aucun produit trouvé pour cette catégorie.</p>
            <?php endif; ?>
        </div>
    </div>

    <div id="overlay" onclick="closeAddProductForm(); closeEditProductForm();"></div>
    <div id="addProductForm">
        <form method="post" action="">
            <input type="hidden" name="action" value="add_product">
            <label for="type">Type:</label>
            <select name="type" id="type" required onchange="toggleMenuOptions()">
                <option value="menus">Menu</option>
                <option value="boissons">Boisson</option>
                <option value="burgers">Burger</option>
            </select><br>

            <div id="menuOptions" class="menu-options-wrapper">
                <label for="burgers_appartient">Choisissez des burgers :</label>
                <select name="burgers[]" id="burgers_appartient" multiple>
                    <?php
                    $burgers = $pdo->query("SELECT burger_id, name FROM burgers")->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($burgers as $burger) {
                        echo "<option value='{$burger['burger_id']}'>{$burger['name']}</option>";
                    }
                    ?>
                </select>

                <label for="boissons_appartient">Choisissez des boissons :</label>
                <select name="boissons[]" id="boissons_appartient" multiple>
                    <?php
                    $boissons = $pdo->query("SELECT boisson_id, name FROM boissons")->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($boissons as $boisson) {
                        echo "<option value='{$boisson['boisson_id']}'>{$boisson['name']}</option>";
                    }
                    ?>
                </select>
            </div>

            <label for="name">Nom:</label>
            <input type="text" id="name" name="name" required><br>
            <label for="description">Description:</label>
            <input type="text" id="description" name="description"><br>
            <label for="prix">Prix:</label>
            <input type="number" id="prix" name="prix" step="0.01" required><br>
            <label for="picture">Image (nom du fichier):</label>
            <input type="text" id="picture" name="picture" required><br>
            <button type="submit">Ajouter</button>
            <button type="button" onclick="closeAddProductForm()">Annuler</button>
        </form>
    </div>

    <div id="editProductForm">
        <form method="post" action="">
            <input type="hidden" name="action" value="edit_product">
            <input type="hidden" id="edit_id" name="edit_id">
            <label for="edit_type">Type:</label>
            <select name="type" id="edit_type" required>
                <option value="menus">Menu</option>
                <option value="boissons">Boisson</option>
                <option value="burgers">Burger</option>
            </select><br>
            <label for="edit_name">Nom:</label>
            <input type="text" id="edit_name" name="name" required><br>
            <label for="edit_description">Description:</label>
            <input type="text" id="edit_description" name="description"><br>
            <label for="edit_prix">Prix:</label>
            <input type="number" id="edit_prix" name="prix" step="0.01" required><br>
            <label for="edit_picture">Image (nom du fichier):</label>
            <input type="text" id="edit_picture" name="picture" required><br>
            <button type="submit">Modifier</button>
            <button type="button" onclick="closeEditProductForm()">Annuler</button>
        </form>
    </div>

    <footer>
        <?php include('../footer/footer.php'); ?>
    </footer>

    <script>
        function openAddProductForm() {
            document.getElementById('addProductForm').style.display = 'block';
            document.getElementById('overlay').style.display = 'block';
        }

        function closeAddProductForm() {
            document.getElementById('addProductForm').style.display = 'none';
            document.getElementById('overlay').style.display = 'none';
        }

        function openEditProductForm(id, type, name, description, prix, picture) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_type').value = type;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_description').value = description;
            document.getElementById('edit_prix').value = prix;
            document.getElementById('edit_picture').value = picture;
            document.getElementById('editProductForm').style.display = 'block';
            document.getElementById('overlay').style.display = 'block';
        }

        function closeEditProductForm() {
            document.getElementById('editProductForm').style.display = 'none';
            document.getElementById('overlay').style.display = 'none';
        }

        function toggleMenuOptions() {
            const type = document.getElementById('type').value;
            if (type === 'menus') {
                document.getElementById('menuOptions').style.display = 'block';
            } else {
                document.getElementById('menuOptions').style.display = 'none';
            }
        }
    </script>
</body>

</html>