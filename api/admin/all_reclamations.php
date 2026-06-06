<?php
require_once "../config/database.php";

$sql = "
SELECT r.*, 
       u.nom AS etudiant_nom, u.prenom AS etudiant_prenom,
       p.nom AS prof_nom, p.prenom AS prof_prenom
FROM reclamation r
JOIN utilisateurs u ON r.utilisateur_id = u.id
JOIN prof p ON r.prof_id = p.id
ORDER BY r.date_reclamation DESC
";

$stmt = $pdo->query($sql);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
