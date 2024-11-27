<?php
// Activer les erreurs pour le débogage
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Inclure la connexion à la base de données
include("../scripts/conn.php");

// Vérifier la connexion à la base de données
if (!$conn) {
    die("Erreur de connexion à la base de données.");
}

// Gestion de l'ajout de commentaire avec rating
if (isset($_POST['submit_comment'])) {
    $username = htmlspecialchars(trim($_POST['username']));
    $comment_text = htmlspecialchars(trim($_POST['comment_text']));
    $rating = intval($_POST['rating']);
    $burger_id = intval($_GET['id']);

    // Insertion du commentaire dans la base de données
    $stmt = $conn->prepare("INSERT INTO comments (burger_id, username, comment_text, rating) VALUES (:burger_id, :username, :comment_text, :rating)");
    $stmt->bindParam(':burger_id', $burger_id, PDO::PARAM_INT);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->bindParam(':comment_text', $comment_text, PDO::PARAM_STR);
    $stmt->bindParam(':rating', $rating, PDO::PARAM_INT);

    if ($stmt->execute()) {
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit();
    } else {
        echo "<p>Erreur lors de l'ajout du commentaire.</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/style.css">
    <link rel="stylesheet" href="articles.css">
    <title>Produits - PhantomBurger</title>
</head>
<body>
    <?php include("../header/header.php"); ?>

    <main>
        <?php
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $burger_id = intval($_GET['id']);
            
            $stmt = $conn->prepare("SELECT * FROM burgers WHERE burger_id = :id");
            $stmt->bindParam(':id', $burger_id, PDO::PARAM_INT);
            $stmt->execute();
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            // Récupérer le nombre de commentaires pour le produit
            $stmt = $conn->prepare("SELECT COUNT(*) as total_comments FROM comments WHERE burger_id = :burger_id");
            $stmt->bindParam(':burger_id', $burger_id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $total_comments = $result['total_comments'];

            if ($product) {
                echo "
                <section id='productInfo'>
                    <div class='product-image'>
                        <img src='../img/" . htmlspecialchars($product['picture']) . "' alt='Image du produit'>
                    </div>
                    <div class='product-details'>
                        <h2>" . htmlspecialchars($product['name']) . "</h2>
                        <p class='tagline'>Mettez du poisson dans votre menu</p>
                        <p class='description'>" . htmlspecialchars($product['description']) . "</p>
                        <p class='price'>Prix : " . htmlspecialchars($product['prix']) . "€</p>
                        <a href='../commandes/commandes.php?id=" . htmlspecialchars($product['burger_id']) . "' class='order-button'>Commandez</a>
                        <button id='commentButton' onclick='openCommentPopup()'>🗨️ Voir les commentaires ($total_comments)</button>
                    </div>
                </section>";

                echo "
                <div id='commentPopup' class='popup'>
                    <div class='popup-content'>
                        <span class='close' onclick='closeCommentPopup()'>&times;</span>
                        <h3>Avis des Clients</h3>";

                $stmt = $conn->prepare("SELECT * FROM comments WHERE burger_id = :burger_id ORDER BY comment_date DESC");
                $stmt->bindParam(':burger_id', $burger_id, PDO::PARAM_INT);
                $stmt->execute();
                $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($comments) {
                    foreach ($comments as $comment) {
                        echo "
                        <div class='review'>
                            <p><strong>" . htmlspecialchars($comment['username']) . "</strong> - " . str_repeat("⭐", intval($comment['rating'])) . "</p>
                            <p>" . htmlspecialchars($comment['comment_text']) . "</p>
                            <small>" . htmlspecialchars($comment['comment_date']) . "</small>
                        </div>";
                    }
                } else {
                    echo "<p>Aucun commentaire pour l'instant. Soyez le premier à laisser un avis !</p>";
                }

                echo "
                <div id='addComment'>
                    <h3>Ajouter un commentaire</h3>
                    <form action='' method='POST'>
                        <input type='text' name='username' placeholder='Votre nom' required>
                        <textarea name='comment_text' placeholder='Écrivez votre commentaire ici...' required></textarea>
                        <label for='rating'>Note :</label>
                        <select name='rating' id='rating' required>
                            <option value='1'>1 étoile</option>
                            <option value='2'>2 étoiles</option>
                            <option value='3'>3 étoiles</option>
                            <option value='4'>4 étoiles</option>
                            <option value='5'>5 étoiles</option>
                        </select>
                        <button type='submit' name='submit_comment'>Soumettre</button>
                    </form>
                </div>
                    </div>
                </div>";
            } else {
                echo "<p>Produit non trouvé.</p>";
            }
        } else {
            echo "<p>ID de produit non spécifié ou invalide.</p>";
        }
        ?>
    </main>

    <?php include("../footer/footer.php"); ?>

    <script>
        function openCommentPopup() {
            document.getElementById('commentPopup').style.display = 'flex';
        }

        function closeCommentPopup() {
            document.getElementById('commentPopup').style.display = 'none';
        }

        window.onclick = function(event) {
            let popup = document.getElementById('commentPopup');
            if (event.target == popup) {
                popup.style.display = 'none';
            }
        }
    </script>
</body>
</html>
