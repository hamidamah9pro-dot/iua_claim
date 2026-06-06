<?php
session_start();
require_once "../config/database.php";
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';

try {
    $stmt = $pdo->prepare("SELECT * FROM admin WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    // Si tu utilises le texte clair pour l'instant :
    if ($admin && $password === $admin['password']) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['nom'] = $admin['nom'];
        $_SESSION['filiere_id'] = $admin['filiere_id']; // Crucial pour le filtrage
        $_SESSION['role'] = 'admin';

        echo json_encode([
            "success" => true,
            "redirect" => "admin.html"
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "Identifiants Admin incorrects"]);
    }
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Erreur : " . $e->getMessage()]);
}