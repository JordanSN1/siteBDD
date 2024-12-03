<?php
// Activer l'affichage des erreurs pour le débogage
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Inclure la connexion à la base de données
include("../scripts/conn.php");

// Démarrer la session pour accéder aux variables $_SESSION
session_start();

$utilisateur_id = $_SESSION['utilisateur_id_']; // Récupérer l'ID utilisateur depuis la session

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupérer les données du formulaire
    $titulaire_carte = $_POST['name'] ?? null;
    $numero_carte = $_POST['card_number'] ?? null;
    $type_carte = $_POST['card_type'] ?? null;
    $date_expiration = $_POST['exp_date'] ?? null;
    $cvv = $_POST['cvv'] ?? null;

    // Vérifier que le panier n'est pas vide
    if (empty($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        die("Le panier est vide. Veuillez ajouter des articles à votre panier.");
    }

    // Insérer une nouvelle commande dans la table commandes
    $commande_id = null; // On capture l'ID auto-incrémenté
    $date_commande = date('Y-m-d H:i:s'); // Date actuelle

    try {
        $conn->beginTransaction(); // Démarrer la transaction

        // Insérer la commande
        $stmtCommande = $conn->prepare("
            INSERT INTO commandes (utilisateur_id_, date_commande) 
            VALUES (:utilisateur_id_, :date_commande)
        ");
        $stmtCommande->execute([ 
            ':utilisateur_id_' => $utilisateur_id,
            ':date_commande' => $date_commande
        ]);
        $commande_id = $conn->lastInsertId(); // Capturer l'ID de la commande

        // Insérer les détails de la commande
        $stmtDetails = $conn->prepare("
            INSERT INTO commandes_details (commande_id, menu_id, boisson_id, burger_id, quantite, prix_unitaire, utilisateur_id_)
            VALUES (:commande_id, :menu_id, :boisson_id, :burger_id, :quantite, :prix_unitaire, :utilisateur_id_)
        ");
        
        foreach ($_SESSION['cart'] as $item) {
            $stmtDetails->execute([
                ':commande_id' => $commande_id,
                ':menu_id' => $item['menu_id'] ?? null, // Assurez-vous d'utiliser l'ID du menu
                ':boisson_id' => $item['boisson_id'] ?? null, // Si la boisson existe, insérez son ID
                ':burger_id' => $item['burger_id'] ?? null, // Si le burger existe, insérez son ID
                ':quantite' => $item['quantity'],
                ':prix_unitaire' => $item['price'],
                ':utilisateur_id_' => $utilisateur_id // Assurez-vous de lier l'utilisateur
            ]);
        }

        // Insérer les informations de transaction
        $stmtTransaction = $conn->prepare("
            INSERT INTO transactions (titulaire_carte, numero_carte, type_carte, date_expiration, cvv, commande_id, utilisateur_id_)
            VALUES (:titulaire_carte, :numero_carte, :type_carte, :date_expiration, :cvv, :commande_id, :utilisateur_id_)
        ");
        $stmtTransaction->execute([
            ':titulaire_carte' => $titulaire_carte,
            ':numero_carte' => $numero_carte,
            ':type_carte' => $type_carte,
            ':date_expiration' => $date_expiration,
            ':cvv' => $cvv,
            ':commande_id' => $commande_id,
            ':utilisateur_id_' => $utilisateur_id
        ]);

        $conn->commit(); // Confirmer la transaction

        // Rediriger l'utilisateur vers la page success.php après la transaction réussie
        header("Location: success.php");
        unset($_SESSION['cart']); // Cette ligne vide le panier dans la session
        exit; // Arrêter l'exécution du script après la redirection
    } catch (Exception $e) {
        $conn->rollBack(); // Annuler la transaction en cas d'erreur
        die("Erreur lors de la transaction : " . $e->getMessage());
    }
}
?>
