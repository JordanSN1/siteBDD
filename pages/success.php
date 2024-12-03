
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction</title>
    <link rel="stylesheet" href="../styles/style.css">
    <link rel="stylesheet" href="../styles/style-success.css">
</head>
<body>
<header>
        <?php include("../header/header.php"); ?>
    </header>
    <h1 style="margin-bottom: 250px;"></h1>
    <div class="loading-screen" id="loadingScreen">
    <div class="burger">
      🍔
    </div>
    <div class="progress-container">
      <div class="progress-bar" id="progressBar"></div>
    </div>
    <p>Préparation de votre commande...</p>
  </div>

  <div class="success-screen hidden" id="successScreen">
    <h1>Commande réussie !</h1>
    <p>Merci pour votre commande ! Votre burger arrive bientôt 🍔</p>
    <button onclick="window.location.reload()">Recommencer</button>
  </div>
    <h1 style="margin-bottom: 250px;"></h1>
    <footer>
        <?php include("../footer/footer.php"); ?>
    </footer>
</body>
</html>


 
<script>
     const loadingScreen = document.getElementById('loadingScreen');
const successScreen = document.getElementById('successScreen');
const progressBar = document.getElementById('progressBar');

let progress = 0;

// Simulation du chargement
const loadingInterval = setInterval(() => {
  progress += 10;
  progressBar.style.width = `${progress}%`;

  if (progress >= 100) {
    clearInterval(loadingInterval);
    setTimeout(() => {
      loadingScreen.classList.add('hidden');
      successScreen.classList.remove('hidden');
    }, 500); // Petite pause pour la transition
  }
}, 500);

    </script>

<style>
    <?php include("../styles/style-transaction.css"); ?>
</style>
