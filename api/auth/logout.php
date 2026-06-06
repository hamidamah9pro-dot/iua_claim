<?php
session_start();

$role = $_SESSION['role'] ?? 'guest';
$_SESSION = array();

session_destroy();

if ($role === 'etudiant') {
    header("location: ../../index.html");
}
else{
    header("location: ../../index-adpr.html");
}

exit;
?>