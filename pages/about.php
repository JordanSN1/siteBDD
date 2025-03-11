<header>
    <?php include("../header/header.php"); ?>
</header>
<style>
    <?php include("about.css"); ?>
</style>

<body>
    <section class="a-propos">
        <h1>À Propos de Nous</h1>
        <p class="intro">PhantomBurger est une plateforme leader...</p>
        <div class="info-a-propos">
            <div class="image-a-propos">
                <img src="../assets/phantomBurgerlogo.png" alt="GeeksforGeeks">
            </div>
            <div class="texte-a-propos">
                <p>

                    Nos Engagements pour Faire Craquer Votre Quotidien

                    Chez PhantomBurger, nous avons un secret bien gardé : le croustillant irrésistible de notre poulet.
                    Oui, celui qui fait saliver vos papilles et vous pousse à savourer chaque morceau de nos fameux
                    Buckets®, jusqu’à la dernière miette ! Mais ce goût unique ne se limite pas qu’à la recette de notre
                    poulet.

                    Derrière chaque bouchée croustillante se cache une véritable démarche d'engagement. Nous nous
                    engageons non seulement pour vous offrir des produits de qualité, mais aussi pour soutenir nos
                    équipes et préserver notre environnement. Chez nous, chaque ingrédient est sélectionné avec soin,
                    chaque employé est valorisé, et chaque action est pensée pour réduire notre empreinte écologique.

                    Jour après jour, comme nous suivons nos recettes avec précision, nous appliquons nos engagements
                    avec la même rigueur. Car chez PhantomBurger, chaque choix compte pour faire de votre expérience
                    gustative un moment unique. Découvrez dès maintenant notre engagement envers la qualité, l’humain et
                    la planète.
                </p>
            </div>
        </div>
    </section>
    <div class="projects-container">
        <div class="project">
            <h3 class="project-name">Clients</h3>
            <div class="project-number odometer websites-designed">0</div>
        </div>

        <div class="project">
            <h3 class="project-name">Projects</h3>
            <div class="project-number odometer apps-developed">0</div>
        </div>
    </div>


    <section class="team">
        <h1>Meet Our Team</h1>
        <div class="team-cards">


            <div class="card">
                <div class="card-img">
                    <img src="../img/afficherPhotoUtilisateur.jpg" alt="User 1">
                </div>
                <div class="card-info">
                    <h2 class="card-name">Nabil Saied</h2>
                    <p class="card-role">CEO and Founder</p>
                    <p class="card-email">nabil.saied@ecoles-epsi.net</p>
                    <p><button class="button">Contact</button></p>
                </div>
            </div>

            <div class="card">
                <div class="card-img">
                    <img src="../img/manu.jpg" alt="User 1">
                </div>
                <div class="card-info">
                    <h2 class="card-name">Pedro ataide </h2>
                    <p class="card-role">CEO and Founder</p>
                    <p class="card-email">m.dossantosataide@ecoles-epsi.net</p>
                    <p><button class="button">Contact</button></p>
                </div>
            </div>
            <div class="card">
                <div class="card-img">
                    <img src="../img/jordan.jpg" alt="User 2">
                </div>
                <div class="card-info">
                    <h2 class="card-name">Jordan Turnaco</h2>
                    <p class="card-role">CEO and Founder</p>
                    <p class="card-email">jordan.turnaco@ecoles-epsi.net</p>
                    <p><button class="button">Contact</button></p>
                </div>
            </div>
            <div class="card">
                <div class="card-img">
                    <img src="../img/ilyas.png" alt="User 3">
                </div>
                <div class="card-info">
                    <h2 class="card-name">Ilyas Lachgar</h2>
                    <p class="card-role">CEO and Founder</p>
                    <p class="card-email">ilyas.lachgar@ecoles-epsi.net</p>
                    <p><button class="button">Contact</button></p>
                </div>
            </div>
        </div>
    </section>


</body>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const websitesDesigned = document.querySelector(".websites-designed");
        const appsDeveloped = document.querySelector(".apps-developed");

        // Vérifie si les éléments existent avant de les modifier
        if (websitesDesigned && appsDeveloped) {
            setTimeout(() => {
                websitesDesigned.textContent = "40";
                appsDeveloped.textContent = "50";
            }, 400);
        } else {
            console.warn("Les éléments .websites-designed ou .apps-developed n'ont pas été trouvés dans le DOM.");
        }
    });
</script>
<footer>
    <?php include("../footer/footer.php"); ?>
</footer>

</html>