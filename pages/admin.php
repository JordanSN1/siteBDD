<?php

session_start();

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

            $query = "INSERT INTO menus (name, description, prix, picture) VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$name, $description, $prix, $picture]);
            $menu_id = $pdo->lastInsertId();


            if (isset($_POST['burgers']) && is_array($_POST['burgers'])) {
                foreach ($_POST['burgers'] as $burger_id) {
                    $query = "INSERT INTO burgers_appartient (menu_id, burger_id) VALUES (?, ?)";
                    $stmt = $pdo->prepare($query);
                    $stmt->execute([$menu_id, $burger_id]);
                }
            }

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

// Traitement de la suppression d'un utilisateur
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_user') {
    $user_id = $_POST['user_id'] ?? '';
    if ($user_id) {
        $query = "DELETE FROM utilisateurs WHERE utilisateur_id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$user_id]);
    }
}

// Traitement de la modification d'un utilisateur
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit_user') {
    $user_id = $_POST['user_id'] ?? '';
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($user_id && $name && $email && $password) {
        $query = "UPDATE utilisateurs SET nom = ?, email = ?, mot_de_passe = ? WHERE utilisateur_id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$name, $email, $password, $user_id]);
    }
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
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #45a049;
        }

        .product-item button {
            margin-top: 10px;
        }


        #addProductForm,
        #editProductForm,
        #editUserForm,
        #addUserForm {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            z-index: 1000;
            width: 90%;
            max-width: 600px;
        }

        #addProductForm label,
        #editProductForm label,
        #editUserForm label,
        #addUserForm label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
            color: #333;
        }

        #addProductForm input,
        #addProductForm select,
        #editProductForm input,
        #editProductForm select,
        #editUserForm input,
        #addUserForm input {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        #addProductForm button,
        #editProductForm button,
        #editUserForm button,
        #addUserForm button {
            margin-top: 15px;
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

        #userList {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            padding: 20px;
            width: 90%;
            max-width: 800px;
            z-index: 1000;
            overflow-y: auto;
            max-height: 80%;
        }

        #userList h3 {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
            text-align: center;
        }

        #userList table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        #userList table th,
        #userList table td {
            padding: 12px 15px;
            text-align: left;
            border: 1px solid #ddd;
        }

        #userList table th {
            background-color: #4CAF50;
            color: white;
            text-transform: uppercase;
            font-size: 14px;
            font-weight: bold;
        }

        #userList table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        #userList table tr:hover {
            background-color: #f1f1f1;
        }

        #userList table td {
            color: #333;
            font-size: 14px;
            vertical-align: middle;
        }

        #userList table td button {
            background-color: #ff4d4d;
            color: white;
            border: none;
            padding: 8px 15px;
            text-align: center;
            border-radius: 5px;
            font-size: 12px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        #userList table td button:hover {
            background-color: #e60000;
        }

        #userList button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            border-radius: 5px;
            margin-top: 15px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        #userList button:hover {
            background-color: #45a049;
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
                <li>
                    <button onclick="openUserList()" style="margin-left: 20px;">Voir les utilisateurs</button>
                </li>

            </ul>
        </div>
        <div id="userList" style="display: none;">
            <h3>Liste des utilisateurs</h3>
            <table border="1">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Mot de passe</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $users = $pdo->query("SELECT utilisateur_id, nom, email, mot_de_passe FROM utilisateurs")->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($users as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['utilisateur_id']); ?></td>
                            <td><?= htmlspecialchars($user['nom']); ?></td>
                            <td><?= htmlspecialchars($user['email']); ?></td>
                            <td><?= htmlspecialchars($user['mot_de_passe']); ?></td>

                            <td>
                                <form method="post" style="display:inline;"
                                    onsubmit="return confirm('Voulez-vous vraiment supprimer cet utilisateur ?');">
                                    <input type="hidden" name="action" value="delete_user">
                                    <input type="hidden" name="user_id" value="<?= $user['utilisateur_id']; ?>">
                                    <button type="submit">Supprimer</button>
                                </form>
                                <button type="button"
                                    onclick="openEditUserForm('<?= $user['utilisateur_id']; ?>', '<?= htmlspecialchars($user['nom']); ?>', '<?= htmlspecialchars($user['email']); ?>', '<?= htmlspecialchars($user['mot_de_passe']); ?>')">Modifier</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <button onclick="closeUserList()">Fermer</button>
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

    <div id="overlay" onclick="closeAddProductForm(); closeEditProductForm(); closeEditUserForm(); closeAddUserForm();">
    </div>
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
    </td>
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

    <div id="editUserForm">
        <form method="post" action="">
            <input type="hidden" name="action" value="edit_user">
            <input type="hidden" id="edit_user_id" name="user_id">
            <label for="edit_user_name">Nom:</label>
            <input type="text" id="edit_user_name" name="name" required><br>
            <label for="edit_user_email">Email:</label>
            <input type="email" id="edit_user_email" name="email" required><br>
            <label for="edit_user_password">Mot de passe:</label>
            <input type="text" id="edit_user_password" name="password" required><br>
            <button type="submit">Modifier</button>
            <button type="button" onclick="closeEditUserForm()">Annuler</button>
        </form>
    </div>

    <footer>
        <?php include('../footer/footer.php'); ?>
    </footer>

    <script>
        function openUserList() {
            document.getElementById('userList').style.display = 'block';
            document.getElementById('overlay').style.display = 'block';
        }

        function closeUserList() {
            document.getElementById('userList').style.display = 'none';
            document.getElementById('overlay').style.display = 'none';
        }
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

        function openEditUserForm(userId, name, email, password) {
            document.getElementById('edit_user_id').value = userId;
            document.getElementById('edit_user_name').value = name;
            document.getElementById('edit_user_email').value = email;
            document.getElementById('edit_user_password').value = password;
            document.getElementById('editUserForm').style.display = 'block';
            document.getElementById('overlay').style.display = 'block';
        }

        function closeEditUserForm() {
            document.getElementById('editUserForm').style.display = 'none';
            document.getElementById('overlay').style.display = 'none';
        }

        function openAddUserForm() {
            document.getElementById('addUserForm').style.display = 'block';
            document.getElementById('overlay').style.display = 'block';
        }

        function closeAddUserForm() {
            document.getElementById('addUserForm').style.display = 'none';
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
        function deleteProduct(deleteId, type) {
            if (confirm('Voulez-vous vraiment supprimer ce produit ?')) {
                // Créez une requête AJAX
                var xhr = new XMLHttpRequest();
                xhr.open("GET", "?delete_id=" + deleteId + "&type=" + type, true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        try {
                            var response = JSON.parse(xhr.responseText);
                            if (response.success) {
                                // Si la suppression est réussie, retirez l'élément de l'interface
                                var productItem = document.getElementById('product-' + deleteId);
                                if (productItem) {
                                    productItem.parentNode.removeChild(productItem);
                                }
                            } else {
                                alert('Erreur lors de la suppression. Veuillez réessayer.');
                            }
                        } catch (e) {
                            alert('Erreur de réponse du serveur.');
                        }
                    }
                };
                xhr.send();
            }
        }

    </script>
</body>

</html>