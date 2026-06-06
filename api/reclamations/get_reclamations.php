<?php
session_start();
require_once "../config/database.php";
header("Content-Type: application/json");

if (!isset($_SESSION['prof_id'])) {
    echo json_encode(["success" => false, "message" => "Accès refusé"]);
    exit;
}

$prof_id = $_SESSION['prof_id'];

try {
    $sql = "SELECT 
                r.id, 
                u.nom as nom, 
                u.prenom, 
                u.niveau, 
                f.nom as nom_filiere, 
                m.nom as matiere, 
                r.note_actuelle, 
                r.note_reclamee, 
                r.motif, 
                r.fichiers, 
                r.statut,
                r.retour_prof,
                r.type,
                r.date_reclamation
            FROM reclamation r
            JOIN utilisateurs u ON r.utilisateur_id = u.id
            JOIN filiere f ON u.filiere_id = f.id
            JOIN matiere m ON r.matiere_id = m.id
            WHERE r.prof_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$prof_id]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}