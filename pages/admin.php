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

if (isset($_GET['delete_user_id'])) {
    $delete_user_id = $_GET['delete_user_id'];
    $query = "DELETE FROM utilisateurs WHERE utilisateur_id_ = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$delete_user_id]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'edit_user') {
        $edit_user_id = $_POST['edit_user_id'] ?? '';
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $role = $_POST['role'] ?? '';

        if ($username && $email && $role && $edit_user_id) {
            $query = "UPDATE utilisateurs SET nom = ?, email = ?, role_id = ? WHERE utilisateur_id_ = ?";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$username, $email, $role, $edit_user_id]);
        }
    } elseif ($_POST['action'] === 'add_user') {
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $role = $_POST['role'] ?? '';
        $password = password_hash($_POST['password'] ?? '', PASSWORD_BCRYPT);

        if ($username && $email && $role && $password) {
            $query = "INSERT INTO utilisateurs (nom, email, role_id, mot_de_passe) VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$username, $email, $role, $password]);
        }
    } elseif ($_POST['action'] === 'add_product') {
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
    } elseif ($_POST['action'] === 'edit_product') {
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
}

function getUsers($pdo)
{
    $query = "SELECT utilisateur_id_, nom, email, role_id FROM utilisateurs";
    return $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
}

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

$users = getUsers($pdo);
$category = $_GET['category'] ?? 'tout';
$products = getProducts($pdo, $category);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../pages/produits.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Sour+Gummy:ital,wght@0,100..900;1,100..900&display=swap');

        /* --- Global Styles --- */
        :root {
            --color-green: #7c9c5f;
            --color-brown: #baa893;
            --color-brown-darker: #361b13;
            --color-white: #f9fdf7;
            --color-link-hover: #fff;
        }

        /* --- Headband (Background Image) --- */
        .headband-product {
            margin-top: 100px;
            width: 100%;
            height: 350px;
            background-image: url('../img/best-hamburger-patties-1.jpg');
            background-size: cover;
            background-position: center;
            position: relative;
        }

        /* --- Product Sorting Section --- */
        .product-sort {
            border-top: 1.5px solid rgba(143, 135, 135, 0.7);
            width: 100%;
            padding-top: 15px;
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
            z-index: 2;
            background-color: rgba(255, 255, 255, 0.8);
        }

        .product-sort ul {
            display: flex;
            align-items: center;
            gap: 30px;
            /* Adjust gap for better spacing */
        }

        .product-sort li {
            list-style-type: none;
        }

        .product-sort li a,
        .product-sort li button {
            text-decoration: none;
            color: #361b13;
            font-family: 'Sour Gummy', cursive;
            font-size: 20px;
            padding: 10px 15px;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: block;
        }

        .product-sort li a:hover,
        .product-sort li button:hover {
            background-color: #7c9c5f;
            color: #fff;
            transform: scale(1.1);
            /* Slight scaling on hover */
        }

        /* --- Product Wrapper --- */
        .product-wrapper {
            margin-top: 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 0 15px;
        }

        /* --- Product List Grid --- */
        .products-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            width: 100%;
            max-width: 1200px;
        }

        /* --- Product Item Style --- */
        .product-item {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            text-align: center;
            padding: 15px;
            transition: transform 0.3s ease;
            background-color: #fafafa;
            position: relative;
        }

        .product-item:hover {
            transform: translateY(-5px);
        }

        .product-item img {
            width: 150px;
            height: 150px;
            border-radius: 8px;
            margin-bottom: 15px;
            object-fit: cover;
        }

        .product-item h3 {
            font-size: 18px;
            font-weight: bold;
            margin: 10px 0;
        }

        .product-item p {
            font-size: 14px;
            color: #666;
            margin-bottom: 15px;
        }

        .product-item p strong {
            font-weight: bold;
            color: #333;
        }

        .product-item .add-to-cart {
            background-color: var(--color-green);
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 25px;
            font-weight: bold;
            display: inline-block;
            transition: background-color 0.3s ease;
        }

        .product-item .add-to-cart:hover {
            background-color: var(--color-green);
        }

        /* --- Animations --- */
        @keyframes loadBar {
            from {
                width: 0;
            }

            to {
                width: 100%;
            }
        }

        #editUserForm,
        #addUserForm,
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

        #editUserForm label,
        #addUserForm label,
        #addProductForm label,
        #editProductForm label {
            display: block;
            margin-top: 10px;
        }

        #editUserForm input,
        #editUserForm select,
        #addUserForm input,
        #addUserForm select,
        #addProductForm input,
        #addProductForm select,
        #editProductForm input,
        #editProductForm select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
        }

        #editUserForm button,
        #addUserForm button,
        #addProductForm button,
        #editProductForm button {
            margin-top: 15px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        #editUserForm button:hover,
        #addUserForm button:hover,
        #addProductForm button:hover,
        #editProductForm button:hover {
            background-color: #45a049;
        }

        .users-list,
        .products-list {
            margin-top: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .user-item,
        .product-item {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            flex: 1 1 calc(33.333% - 15px);
            box-sizing: border-box;
        }

        .user-item h3,
        .product-item h3 {
            margin: 0;
            font-size: 1.2em;
            color: #333;
        }

        .user-item p,
        .product-item p {
            margin: 5px 0;
            color: #666;
        }

        .user-item button,
        .product-item button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            margin-top: 10px;
            transition: background-color 0.3s;
        }

        .user-item button:hover,
        .product-item button:hover {
            background-color: #45a049;
        }
    </style>
    <title>PhantomBurger - Utilisateurs et Produits</title>
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
                    <button onclick="openAddUserForm()">Ajouter Un Utilisateur</button>
                </li>
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
                    <button onclick="openAddProductForm()">Ajouter Un Produit</button>
                </li>
            </ul>
        </div>

        <div class="users-list">
            <?php if (count($users) > 0): ?>
                <?php foreach ($users as $user): ?>


                    <div class="user-item">
                        <h3><?= htmlspecialchars($user['nom']); ?></h3>
                        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']); ?></p>
                        <p><strong>Role:</strong>
                            <?= htmlspecialchars($pdo->query("SELECT role.role_name FROM role INNER JOIN utilisateurs ON utilisateurs.role_id = role.role_id WHERE utilisateurs.role_id = {$user['role_id']}")->fetch(PDO::FETCH_ASSOC)['role_name']); ?>
                        </p>
                        <a href="?delete_user_id=<?= $user['utilisateur_id_']; ?>"
                            onclick="return confirm('Voulez-vous vraiment supprimer cet utilisateur ?');">
                            <button type="button">Supprimer</button>
                        </a>
                        <button type="button"
                            onclick="openEditUserForm('<?= $user['utilisateur_id_']; ?>', '<?= htmlspecialchars($user['nom']); ?>', '<?= htmlspecialchars($user['email']); ?>', '<?= $user['role_id']; ?>')">Modifier</button>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Aucun utilisateur trouvé.</p>
            <?php endif; ?>
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

    <div id="editUserForm">
        <form method="post" action="">
            <input type="hidden" name="action" value="edit_user">
            <input type="hidden" id="edit_user_id" name="edit_user_id">
            <label for="edit_username">Nom d'utilisateur:</label>
            <input type="text" id="edit_username" name="username" required><br>
            <label for="edit_email">Email:</label>
            <input type="email" id="edit_email" name="email" required><br>
            <label for="edit_role">Role:</label>
            <select name="role" id="edit_role" required>
                <option value="1">Admin</option>
                <option value="2">Modérateur</option>
                <option value="3">Utilisateur</option>
            </select><br>
            <button type="submit">Modifier</button>
            <button type="button" onclick="closeEditUserForm()">Annuler</button>
        </form>
    </div>

    <div id="addUserForm">
        <form method="post" action="">
            <input type="hidden" name="action" value="add_user">
            <label for="add_username">Nom d'utilisateur:</label>
            <input type="text" id="add_username" name="username" required><br>
            <label for="add_email">Email:</label>
            <input type="email" id="add_email" name="email" required><br>
            <label for="add_role">Role:</label>
            <select name="role" id="add_role" required>
                <option value="1">Admin</option>
                <option value="2">Modérateur</option>
                <option value="3">Utilisateur</option>
            </select><br>
            <label for="add_password">Mot de passe:</label>
            <input type="password" id="add_password" name="password" required><br>
            <button type="submit">Ajouter</button>
            <button type="button" onclick="closeAddUserForm()">Annuler</button>
        </form>
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
        function openEditUserForm(user_id, username, email, role) {
            document.getElementById('edit_user_id').value = user_id;
            document.getElementById('edit_username').value = username;
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_role').value = role;
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