<?php
$host = "sql203.infinityfree.com"; 
$dbname = "if0_41828289_iua"; // nom de ta base de données
$username = "if0_41828289";    // ton utilisateur MySQL (par défaut root)
$password = "q7mJEHI0Aml";        // ton mot de passe MySQL (souvent vide en local)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // Activer les erreurs PDO
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    

} catch (PDOException $e) {
    die("Erreur de connexion à la base de données: " . $e->getMessage());
}
?>
