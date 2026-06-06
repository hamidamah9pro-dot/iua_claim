<?php
session_start();
header("Content-Type: application/json");

// 1. On vérifie d'abord si c'est un Professeur
if (isset($_SESSION['prof_id'])) {
    echo json_encode([
        "success" => true,
        "role" => "prof",
        "nom" => $_SESSION['nom'],
        "prenom" => $_SESSION['prenom'] // Le prof a un prénom en session
    ]);
    exit;
} 
// 2. Sinon, on vérifie si c'est un Étudiant
else if (isset($_SESSION['etudiant_id'])) {
    echo json_encode([
        "success" => true,
        "role" => "etudiant",
        "nom" => $_SESSION['nom'],
        "prenom" => $_SESSION['prenom'] ?? "" // Évite une erreur si le prénom n'est pas défini pour l'étudiant
    ]);
    exit;
} 
// 3. Si aucun des deux n'est connecté
else {
    echo json_encode([
        "success" => false,
        "message" => "Aucun utilisateur connecté."
    ]);
}
?>