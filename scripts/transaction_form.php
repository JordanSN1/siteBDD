<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("../scripts/conn.php");

session_start();

$utilisateur_id = $_SESSION['utilisateur_id_'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulaire_carte = $_POST['name'] ?? null;
    $numero_carte = $_POST['card_number'] ?? null;
    $type_carte = $_POST['card_type'] ?? null;
    $date_expiration = $_POST['exp_date'] ?? null;
    $cvv = $_POST['cvv'] ?? null;

    if (empty($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        die("Le panier est vide. Veuillez ajouter des articles à votre panier.");
    }

    $commande_id = null;
    $date_commande = date('Y-m-d H:i:s');

    try {
        $conn->beginTransaction();

        $commande_id = $conn->lastInsertId();

        $stmtDetails = $conn->prepare("
            INSERT INTO commandes_details (commande_id, menu_id, boisson_id, burger_id, quantite, prix_unitaire, utilisateur_id_)
            VALUES (:commande_id, :menu_id, :boisson_id, :burger_id, :quantite, :prix_unitaire, :utilisateur_id_)
        ");

        foreach ($_SESSION['cart'] as $item) {
            $stmtDetails->execute([
                ':commande_id' => $commande_id,
                ':menu_id' => $item['menu_id'] ?? null,
                ':boisson_id' => $item['boisson_id'] ?? null,
                ':burger_id' => $item['burger_id'] ?? null,
                ':quantite' => $item['quantity'],
                ':prix_unitaire' => $item['price'],
                ':utilisateur_id_' => $utilisateur_id
            ]);
        }

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

        $conn->commit();

        header("Location: success.php");
        unset($_SESSION['cart']);
        exit;
    } catch (Exception $e) {
        $conn->rollBack();
        die("Erreur lors de la transaction : " . $e->getMessage());
    }
}
?>