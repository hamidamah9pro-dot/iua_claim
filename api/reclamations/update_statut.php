<?php
session_set_cookie_params(['path' => '/', 'samesite' => 'Lax', 'secure' => false]);
session_start();

header("Access-Control-Allow-Origin: " . ($_SERVER['HTTP_ORIGIN'] ?? '*'));
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

require_once "../config/database.php";

// ... reste du code inchangé

$data = json_decode(file_get_contents("php://input"), true);
$id           = $data['id'] ?? null;
$nouveau_statut = $data['statut'] ?? null;
$message      = $data['message'] ?? null;
$note_finale  = $data['note_finale'] ?? null;

if (!$id || !$nouveau_statut) {
    echo json_encode(["success" => false, "message" => "Données manquantes"]);
    exit;
}

try {
    // Construction dynamique selon ce qui est fourni
    $fields = "statut = ?, date_traitement = NOW()";
    $params = [$nouveau_statut];

    if ($message !== null) {
        $fields .= ", retour_prof = ?";
        $params[] = $message;
    }

    if ($note_finale !== null) {
        $fields .= ", note_reclamee = ?";
        $params[] = $note_finale;
    }

    $params[] = $id;

    $sql = "UPDATE reclamation SET $fields WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $success = $stmt->execute($params);

    echo json_encode(["success" => $success]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}