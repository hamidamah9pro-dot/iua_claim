<?php
session_start();
require_once "../config/database.php";
header("Content-Type: application/json");

if (!isset($_SESSION['etudiant_id'])) {
    echo json_encode(["success" => false, "message" => "Non connecté"]);
    exit;
}

$etudiant_id = $_SESSION['etudiant_id'];

try {
    $sql = "SELECT r.id, m.nom as matiere, p.nom as prof, r.note_actuelle, r.note_reclamee, r.statut, r.date_reclamation, r.retour_prof
            FROM reclamation r
            JOIN matiere m ON r.matiere_id = m.id
            JOIN prof p ON r.prof_id = p.id
            WHERE r.utilisateur_id = ?
            ORDER BY r.date_reclamation DESC";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$etudiant_id]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}