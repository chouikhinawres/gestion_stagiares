<?php
/**
 * connexion.php
 * ----------
 * Centralise la création d'une connexion PDO.
 * Utilisez-le partout avec `require_once 'connexion.php';`
 */

$host = 'localhost';
$db   = 'stagiaires';   // <-- le nom exact de votre base
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // exceptions en cas d’erreur
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // fetch assoc
    PDO::ATTR_EMULATE_PREPARES   => false,                  // vrai préparé
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {

    die('Erreur de connexion à la base de données.');
}
?>
