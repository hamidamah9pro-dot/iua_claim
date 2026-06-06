<?php
session_start();
require_once "../config/database.php";
header("Content-Type: application/json");

// 1. Vérification de la session
if (!isset($_SESSION['etudiant_id'])) {
    echo json_encode(["success" => false, "message" => "Vous devez être connecté."]);
    exit;
}

// 2. Récupération des données via $_POST (FormData côté JS)
$matiere_id    = $_POST['matiere_id'] ?? null;
$note_actuelle = $_POST['note_actuelle'] ?? null;
$note_reclamee = $_POST['note_reclamee'] ?? null;
$motif         = $_POST['motif'] ?? null;
$type          = $_POST['type'] ?? 'NOTE'; // 'NOTE' ou 'EXAMEN'

$utilisateur_id = $_SESSION['etudiant_id'];
$niveau         = $_SESSION['niveau'] ?? 'NON_DEFINI';

// 3. Gestion de l'image (changement du lien et stockage)
$nom_fichier_final = null; 

if (isset($_FILES['fichiers']) && $_FILES['fichiers']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = "iua-claim.rf.gd/api/uploads/";
    
    if (!is_dir($uploadDir)) { 
        mkdir($uploadDir, 0777, true); 
    }

    $pathInfo = pathinfo($_FILES['fichiers']['name']);
    $extension = isset($pathInfo['extension']) ? strtolower($pathInfo['extension']) : 'jpg';
    
    $nom_fichier_final = "rec_" . uniqid() . "_" . time() . "." . $extension;
    $destination = $uploadDir . $nom_fichier_final;

    if (!move_uploaded_file($_FILES['fichiers']['tmp_name'], $destination)) {
        echo json_encode(["success" => false, "message" => "Erreur lors du déplacement du fichier."]);
        exit;
    }
}

// Validation des champs obligatoires
if (!$matiere_id || $note_actuelle === null || $note_reclamee === null || !$motif) {
    echo json_encode(["success" => false, "message" => "Tous les champs sont obligatoires."]);
    exit;
}

try {
    // 4. Recherche du professeur affecté
    $sqlProf = "SELECT id FROM prof WHERE matiere_id = ? AND TRIM(niveau) = ? LIMIT 1";
    $stmtProf = $pdo->prepare($sqlProf);
    $stmtProf->execute([(int)$matiere_id, trim($niveau)]);
    $prof = $stmtProf->fetch(PDO::FETCH_ASSOC);

    if (!$prof) {
        echo json_encode([
            "success" => false, 
            "message" => "Aucun professeur n'est affecté à cette matière pour votre niveau ($niveau)."
        ]);
        exit;
    }

    $prof_id = $prof['id'];

    // 5. Insertion dans la table reclamation
    $sqlInsert = "INSERT INTO reclamation (
        utilisateur_id, prof_id, matiere_id, note_actuelle, 
        note_reclamee, motif, fichiers, statut, type, date_reclamation
    ) VALUES (?, ?, ?, ?, ?, ?, ?, 'EN_ATTENTE', ?, NOW())";

    $stmtInsert = $pdo->prepare($sqlInsert);
    $success = $stmtInsert->execute([
        $utilisateur_id, 
        $prof_id, 
        $matiere_id, 
        $note_actuelle, 
        $note_reclamee, 
        $motif,
        $nom_fichier_final,
        $type
    ]);

    echo json_encode([
        "success" => $success,
        "message" => $success ? "Réclamation envoyée avec succès !" : "Erreur technique lors de l'enregistrement."
    ]);

} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Erreur SQL : " . $e->getMessage()]);
}