<?php
// Démarrer la session


// Connexion à la base de données
include("../scripts/conn.php");

// Vérifier la connexion à la base de données
if (!$conn) {
    die("Erreur de connexion à la base de données.");
}

function getProductByName($conn, $name) {
    // Requête pour récupérer les produits, en s'assurant que l'ID spécifique à chaque type de produit est bien assigné
    $query = "SELECT 'menu' AS type, menu_id AS id, name, prix, picture FROM menus WHERE name = :name
              UNION ALL
              SELECT 'burger' AS type, burger_id AS id, name, prix, picture FROM burgers WHERE name = :name
              UNION ALL
              SELECT 'boisson' AS type, boisson_id AS id, name, prix, picture FROM boissons WHERE name = :name";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->execute();
    
    return $stmt->fetch(PDO::FETCH_ASSOC); // Retourner un seul produit
}





// Gérer les actions sur le panier (ajout, mise à jour, suppression)
if (isset($_GET['action'])) {
    $action = $_GET['action'];
 
    // Si l'action est "ajouter", on ajoute le produit au panier
    if ($action == 'add') {
        $name = $_GET['name'];
        $product = getProductByName($conn, $name);

        if ($product) {
            // Si le produit est déjà dans le panier, on met à jour la quantité
            if (isset($_SESSION['cart'][$name])) {
                $_SESSION['cart'][$name]['quantity']++;
            } else {
                // Sinon, on ajoute le produit avec une quantité de 1
                $product_data = [
                    'name' => $product['name'],
                    'price' => $product['prix'],
                    'picture' => $product['picture'],
                    'quantity' => 1
                ];

                // Ajouter l'ID correspondant en fonction du type de produit
                if ($product['type'] == 'burger') {
                    $product_data['burger_id'] = $product['id'];
                } elseif ($product['type'] == 'menu') {
                    $product_data['menu_id'] = $product['id'];
                } elseif ($product['type'] == 'boisson') {
                    $product_data['boisson_id'] = $product['id'];
                }

                // Ajouter le produit au panier
                $_SESSION['cart'][$product['name']] = $product_data;
            }
            header("Location: cart.php"); // Rediriger pour éviter d'ajouter plusieurs fois le même produit
            exit;
        }
    }


    // Si l'action est "remove", on retire le produit du panier
    if ($action == 'remove') {
        $name = $_GET['name'];
    
        // Vérifier si le produit existe dans le panier
        if (isset($_SESSION['cart'][$name])) {
            // Si la quantité est supérieure à 1, on diminue la quantité
            if ($_SESSION['cart'][$name]['quantity'] > 1) {
                $_SESSION['cart'][$name]['quantity']--;
            } else {
                // Si la quantité est 1, on supprime le produit du panier
                unset($_SESSION['cart'][$name]);
            }
            header("Location: cart.php"); // Rediriger pour éviter d'ajouter le produit plusieurs fois
            exit;
        }
    }
    if ($action == 'update') {
        $name = $_GET['name'];
    
        // Vérifier si le produit existe dans le panier
        if (isset($_SESSION['cart'][$name])) {
            // Si la quantité est supérieure à 1, on diminue la quantité
            if ($_SESSION['cart'][$name]['quantity'] > 0) {
                $_SESSION['cart'][$name]['quantity']++;
            } else {
                // Si la quantité est 1, on supprime le produit du panier
                unset($_SESSION['cart'][$name]);
            }
            header("Location: cart.php"); // Rediriger pour éviter d'ajouter le produit plusieurs fois
            exit;
        }
    }
    
}
?>