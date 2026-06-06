<?php
session_start();
require_once "../config/database.php";
header("Content-Type: application/json");

if (!isset($_SESSION['admin_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "Accès refusé."]);
    exit;
}

$filiere_id_admin = $_SESSION['filiere_id'];

try {
    $sql = "SELECT 
                r.id, 
                u.nom, 
                u.prenom,
                u.niveau,
                m.nom as matiere, 
                p.nom as prof, 
                r.note_actuelle, 
                r.note_reclamee, 
                r.motif,
                r.fichiers,
                r.retour_prof,
                r.statut,
                r.type,
                r.date_reclamation 
            FROM reclamation r
            JOIN utilisateurs u ON r.utilisateur_id = u.id
            JOIN matiere m ON r.matiere_id = m.id
            JOIN prof p ON r.prof_id = p.id
            WHERE m.filiere_id = ?
            ORDER BY r.date_reclamation DESC";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$filiere_id_admin]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}