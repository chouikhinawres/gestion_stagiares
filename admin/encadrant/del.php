<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'){
    header("Location: ../../auth/form_login.php");
    exit;
}

require_once "../../config/connection.php";

$id = $_GET['id'] ?? 0;

if($id > 0){
    $stmt = $pdo->prepare("DELETE FROM encadrants WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location: list.php");
exit;
?>
