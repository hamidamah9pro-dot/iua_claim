<?php
header('Content-Type: application/json');
require_once '../config/database.php'; // Ta connexion PDO

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['id']) && isset($data['message'])) {
    try {
        // On met à jour le commentaire et on peut aussi changer le statut
        $sql = "UPDATE reclamation SET retour_prof = :msg, date_traitement = NOW() WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $success = $stmt->execute([
            ':msg' => $data['message'],
            ':id'  => $data['id']
        ]);

        if ($success) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour SQL']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Données incomplètes']);
}