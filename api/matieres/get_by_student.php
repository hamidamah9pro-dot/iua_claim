<?php
session_start();
require_once "../config/database.php";
header("Content-Type: application/json");

// On vérifie que l'étudiant est bien connecté
if (!isset($_SESSION['etudiant_id']) || !isset($_SESSION['filiere_id'])) {
    echo json_encode([]);
    exit;
}

$filiere_id = $_SESSION['filiere_id'];

try {
    // On récupère uniquement les matières de la filière de l'étudiant
    $query = "SELECT id, nom FROM matiere WHERE filiere_id = ? ORDER BY nom ASC";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$filiere_id]);
    $matieres = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($matieres);
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}