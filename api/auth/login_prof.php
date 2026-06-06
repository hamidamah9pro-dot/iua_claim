<?php
session_start();
require_once "../config/database.php";
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';

if (empty($email) || empty($password)) {
    echo json_encode(["success" => false, "message" => "Champs vides."]);
    exit;
}

try {
    // 1. On cherche d'abord dans la table PROF
    $stmt = $pdo->prepare("SELECT id, nom, prenom, email, password, matiere_id FROM prof WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $prof = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($prof && $password == $prof['password']) {
        $_SESSION['prof_id'] = $prof['id'];
        $_SESSION['nom'] = $prof['nom'];
        $_SESSION['prenom'] = $prof['prenom'];
        $_SESSION['matiere_id'] = $prof['matiere_id'];
        $_SESSION['role'] = 'prof';
        
        echo json_encode(["success" => true, "redirect" => "prof.html"]);
        exit;
    }

    // 2. Si non trouvé, on cherche dans la table UTILISATEURS (Étudiants)
    // On récupère aussi la filière et le niveau pour le formulaire de réclamation !
    $stmt = $pdo->prepare("SELECT id, nom, password, filiere_id, niveau FROM utilisateurs WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['etudiant_id'] = $user['id'];
        $_SESSION['nom'] = $user['nom'];
        $_SESSION['filiere_id'] = $user['filiere_id'];
        $_SESSION['niveau'] = $user['niveau'];
        $_SESSION['role'] = 'etudiant';
        
        echo json_encode(["success" => true, "redirect" => "recla.html"]);
        exit;
    }

    // 3. Si rien n'est trouvé
    echo json_encode(["success" => false, "message" => "Email ou mot de passe incorrect."]);

} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Erreur technique."]);
    echo json_encode(["success" => false, "message" => "Erreur SQL : " . $e->getMessage()]);
}