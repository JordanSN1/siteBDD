<header>
    <?php include("../header/header.php"); ?>
</header>
<style>
    <?php include("index.css"); ?>
</style>

<div class="product-list">
    <?php
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    include("../scripts/conn.php");


    $products = $conn->query("SELECT * FROM burgers WHERE burger_id <= 5;");
    foreach ($products as $product) {
        echo "
                <div class='product-card'>
                    <div class='product-image'>
                        <img src='../img/" . htmlspecialchars($product['picture']) . "' alt='" . htmlspecialchars($product['name']) . "'>
                    </div>
                    <td><a href ='../pages/articles.php?id=" . $product['burger_id'] . "'>" . $product['name'] . "</a></td>

                    <p class='prix'>" . htmlspecialchars($product['prix']) . " €</p>
                    <p class='description'>" . htmlspecialchars($product['description']) . "</p>
                    

                </div>
            ";
    }
    ?>
</div>
<div id="secondaire" class="secondContainer">
    <h2>Nos autres produits à découvrir</h2>
    <div class="picture1">
        <img src="../img/index3" alt="">


    </div>
    <div class="picture1_2">
        <img src="../img/index2" alt="">
    </div>
    <div class="picture1_3">
        <img src="../img/iStock-1152247466 (1).jpg" alt="">
    </div>


    <section class="cta-section">
        <h1>Repars avec du croustillant plein les poches</h1>
        <p>Cumulez des points pour vous offrir des produits croustillants</p>
        <a href="inscription.php" class="cta-button">Créer un compte</a>
    </section>



    <section class="carousel-section">
        <div class="carousel">
            <img src="../img/iStock-1152247466 (1).jpg" alt="Colonel Fish">
            <img src="../img/Grilled-Hamburgers-Social.jpg" alt="Boxmaster Veggie">
            <img src="../img/burger_spicy.jpg" alt="Plaisirs Gourmands">
            <img src="../img/Grilled-Hamburgers-Social.jpg" alt="Boxmaster Veggie">
            <img src="../img/iStock-1152247466 (1).jpg" alt="Colonel Fish">
        </div>
    </section>
    <div class="picture3">
        <img src="../img/INDEX" alt="">
    </div>



    <script src="../src/scripts.js"></script>
    </body>
    <footer>
        <?php include("../footer/footer.php"); ?>
    </footer>

    </html>