<header>
    <?php include("../header/header.php"); ?>
</header>
<style>
    <?php include("index.css"); ?>
</style>

<!-- Main Product List Section -->
<div class="product-list">
    <?php
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    include("../scripts/conn.php");

    $products = $conn->query("SELECT * FROM burgers WHERE burger_id <= 5;");
    foreach ($products as $product) {
        // Adding data-badge attribute for new or featured products
        $badge = "";
        if (isset($product['is_new']) && $product['is_new'] == 1) {
            $badge = "data-badge='NOUVEAU'";
        } elseif (isset($product['is_featured']) && $product['is_featured'] == 1) {
            $badge = "data-badge='POPULAIRE'";
        }

        echo "
            <div class='product-card' $badge>
                <div class='product-image'>
                    <img src='../img/" . htmlspecialchars($product['picture']) . "' alt='" . htmlspecialchars($product['name']) . "'>
                </div>
                <a href='../pages/articles.php?id=" . $product['burger_id'] . "'>" . htmlspecialchars($product['name']) . "</a>
                <p class='price'>" . htmlspecialchars($product['prix']) . " €</p>
                <p class='description'>" . htmlspecialchars($product['description']) . "</p>
                <button class='add-to-cart'>Ajouter au panier</button>
            </div>
        ";
    }
    ?>
</div>

<!-- Featured Products Gallery Section -->
<div id="secondaire" class="secondContainer">
    <h2>Nos autres produits à découvrir</h2>

    <div class="gallery-container">
        <div class="gallery-item">
            <img src="../img/index3" alt="Produit spécial">
            <div class="overlay">
                <h3>Nos entrées</h3>
                <p>Découvrez notre sélection d'entrées délicieuses</p>
            </div>
        </div>
        <div class="gallery-item">
            <img src="../img/index2" alt="Menu populaire">
            <div class="overlay">
                <h3>Nos menus</h3>
                <p>Des menus complets pour tous les goûts</p>
            </div>
        </div>
        <div class="gallery-item">
            <img src="../img/iStock-1152247466 (1).jpg" alt="Plat signature">
            <div class="overlay">
                <h3>Nos desserts</h3>
                <p>Terminez votre repas en beauté</p>
            </div>
        </div>
    </div>

    <!-- Call-to-Action Section -->
    <section class="cta-section">
        <div class="cta-content">
            <h2>Repars avec du croustillant plein les poches</h2>
            <p>Cumulez des points pour vous offrir des produits croustillants et profitez de nos offres exclusives</p>
            <?php if (!isset($_SESSION['utilisateur_id_'])): ?>
                <a href="inscription.php" class="cta-button">Créer un compte</a>
            <?php endif; ?>
        </div>
    </section>

    <!-- Carousel Section -->
    <section class="carousel-container">
        <div class="carousel">
            <div class="carousel-item">
                <img src="../img/iStock-1152247466 (1).jpg" alt="Colonel Fish">
            </div>
            <div class="carousel-item">
                <img src="../img/Grilled-Hamburgers-Social.jpg" alt="Boxmaster Veggie">
            </div>
            <div class="carousel-item">
                <img src="../img/burger_spicy.jpg" alt="Plaisirs Gourmands">
            </div>
            <div class="carousel-item">
                <img src="../img/Grilled-Hamburgers-Social.jpg" alt="Boxmaster Veggie">
            </div>
            <div class="carousel-item">
                <img src="../img/iStock-1152247466 (1).jpg" alt="Colonel Fish">
            </div>
        </div>
        <div class="carousel-controls">
            <button class="prev-btn" aria-label="Previous slide">&#10094;</button>
            <button class="next-btn" aria-label="Next slide">&#10095;</button>
        </div>
    </section>

    <!-- Banner Image -->
    <div class="picture3">
        <img src="../img/INDEX" alt="Banner promotionnel">
    </div>

    <!-- JavaScript -->
    <script src="../src/scripts.js"></script>

    <!-- Add this new script for carousel functionality -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const carousel = document.querySelector('.carousel');
            const prevBtn = document.querySelector('.prev-btn');
            const nextBtn = document.querySelector('.next-btn');

            let scrollAmount = 0;
            const scrollStep = 320; // Adjust based on your carousel item width + gap

            prevBtn.addEventListener('click', function () {
                scrollAmount = Math.max(0, scrollAmount - scrollStep);
                carousel.scrollTo({
                    left: scrollAmount,
                    behavior: 'smooth'
                });
            });

            nextBtn.addEventListener('click', function () {
                scrollAmount = Math.min(carousel.scrollWidth - carousel.clientWidth, scrollAmount + scrollStep);
                carousel.scrollTo({
                    left: scrollAmount,
                    behavior: 'smooth'
                });
            });
        });
    </script>
</div>

<footer>
    <?php include("../footer/footer.php"); ?>
</footer>