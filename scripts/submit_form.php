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
$nom = isset($_POST['nom']) ? trim($_POST['nom']) : '';
$prenom = isset($_POST['prenom']) ? trim($_POST['prenom']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$telephone = isset($_POST['telephone']) ? trim($_POST['telephone']) : '';
$message = isset($_POST['message']) ? trim($_POST['message']) : '';
$datemsg = date('Y-m-d H:i:s'); // Current date and time

// Vérifier que toutes les données sont présentes
if (empty($nom) || empty($prenom) || empty($email) || empty($telephone) || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Tous les champs doivent être remplis.']);
    exit();
}

// Valider l'email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'L\'adresse email est invalide.']);
    exit();
}

// Préparer et exécuter la requête d'insertion
$sql = "INSERT INTO contact (nom, prenom, email, telephone, message, datemsg) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

// Vérifier si la préparation a échoué
if ($stmt === false) {
    echo json_encode(['success' => false, 'message' => 'Erreur dans la préparation de la requête SQL.']);
    exit();
}

// Lier les paramètres et exécuter la requête
$stmt->bind_param("ssssss", $nom, $prenom, $email, $telephone, $message, $datemsg);

// Exécuter la requête et vérifier si l'exécution a réussi
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Message envoyé avec succès ! Nos équipes vous contacteront dès que possible.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'envoi du message.']);
}

// Fermer la connexion
$stmt->close();
$conn->close();
?>