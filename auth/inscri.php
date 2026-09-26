<?php

require_once __DIR__ . '/../config/connection.php';

$nom = $_POST['nom'];
$email = $_POST['email'];
$password = $_POST['password'];
$role = $_POST['role'];

$hashPassword = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO users (nom,email,password,role)
VALUES (:nom,:email,:password,:role)";

$stmt = $pdo->prepare($sql);

if($stmt->execute(['nom' => $nom, 'email' => $email, 'password' => $hashPassword, 'role' => $role])){

header("Location: form_login.php");
exit;

}

else{

echo "Erreur inscription";

}

?>
