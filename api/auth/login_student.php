<?php
session_start(); // démarrer la session

header("Content-Type: application/json");
require_once "../config/database.php";

$data = json_decode(file_get_contents("php://input"), true);

$matricule = $data['matricule'] ?? null;
$email = $data['email'] ?? null;

if (!$matricule || !$email) {
    echo json_encode([
        "success" => false,
        "message" => "Champs manquants"
    ]);
    exit;
}

$sql = "SELECT * FROM utilisateurs WHERE matricule = ? AND email = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$matricule, $email]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    // stocker les infos dans la session
    $_SESSION['etudiant_id'] = $user['id'];
    $_SESSION['nom'] = $user['nom'];
    $_SESSION['prenom'] = $user['prenom'];
    $_SESSION['niveau'] = $user['niveau'];
    $_SESSION['filiere_id'] = $user['filiere_id'];
    $_SESSION['role'] = 'etudiant';

    echo json_encode([
        "success" => true,
        "message" => "Connexion réussie",
        "redirect" => "recla.html" // indiquer au JS la page de redirection
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Identifiants incorrects"
    ]);
}
