<?php
$host = $_ENV['DB_HOST'] ?? $_SERVER['DB_HOST'] ?? null;
$dbname = $_ENV['DB_NAME'] ?? $_SERVER['DB_NAME'] ?? null;
$username = $_ENV['DB_USER'] ?? $_SERVER['DB_USER'] ?? null;
$password = $_ENV['DB_PASSWORD'] ?? $_SERVER['DB_PASSWORD'] ?? null;
$port = $_ENV['DB_PORT'] ?? $_SERVER['DB_PORT'] ?? '3306';

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password);
    
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    header('Content-Type: application/json');
    http_response_code(500);
    die(json_encode(["error" => "Erreur de connexion à la base de données"]));
}
?>