<?php
header('Content-Type: application/json');

// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "phantomBurger";

$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Erreur de connexion à la base de données.']);
    exit();
}

// Récupérer les données du formulaire
$nom = isset($_POST['nom']) ? $_POST['nom'] : '';
$prenom = isset($_POST['prenom']) ? $_POST['prenom'] : '';
$email = isset($_POST['email']) ? $_POST['email'] : '';
$telephone = isset($_POST['telephone']) ? $_POST['telephone'] : '';
$message = isset($_POST['message']) ? $_POST['message'] : '';

// Préparer et exécuter la requête d'insertion
$sql = "INSERT INTO contact (nom, prenom, email, telephone, message) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssss", $nom, $prenom, $email, $telephone, $message);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Message envoyé avec succès ! Nos équipes vous contacteront dès que possible.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'envoi du message.']);
}

// Fermer la connexion
$stmt->close();
$conn->close();