<?php
/* -------------------------------------------------
   2️⃣  Pré‑requis : session + connexion PDO
   ------------------------------------------------- */
session_start();                     // démarre (ou reprend) la session
require_once __DIR__ . '/../config/connection.php';   // $pdo est disponible

/* -------------------------------------------------
   3️⃣  Vérifier que le formulaire a bien été soumis
   ------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Accès direct → on renvoie vers le formulaire
    header('Location: form_login.php');
    exit;
}

/* -------------------------------------------------
   4️⃣  Récupérer et nettoyer les valeurs
   ------------------------------------------------- */
$emailRaw    = $_POST['email']    ?? '';
$passwordRaw = $_POST['password'] ?? '';

$email    = filter_var(trim($emailRaw), FILTER_VALIDATE_EMAIL);
$password = $passwordRaw;               // le mot de passe ne doit pas être modifié

/* -------------------------------------------------
   5️⃣  Gestion des erreurs de saisie (format e‑mail)
   ------------------------------------------------- */
if ($email === false) {
    $_SESSION['login_error'] = 'Le format de l’adresse e‑mail n’est pas valide.';
    header('Location: form_login.php');
    exit;
}

/* -------------------------------------------------
   6️⃣  Recherche du compte en base (requête préparée)
   ------------------------------------------------- */
$sql  = "SELECT id, email, password, role FROM users WHERE email = :email LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute(['email' => $email]);
$user = $stmt->fetch();      // false si aucun enregistrement

/* -------------------------------------------------
   7️⃣  Vérification du mot de passe
   ------------------------------------------------- */
if ($user && password_verify($password, $user['password'])) {

    /* -------------------------------------------------
       8️⃣  Authentification réussie → stockage en session
       ------------------------------------------------- */
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role']    = $user['role'];
    $_SESSION['email']   = $user['email'];   // utile pour le message d’accueil

    // Redirection selon le rôle
    switch ($user['role']) {
        case 'admin':
            $dest = '../admin/dashboard.php';
            break;
        case 'stagiaire':
            $dest = '../stagiare/dashboard.php';
            break;
        case 'encadrant':
            $dest = '../encadrant/dashboard.php';
            break;
        default:
            // Cas improbable : on protège tout de même
            $_SESSION['login_error'] = 'Rôle inconnu. Contactez l’administrateur.';
            $dest = 'form_login.php';
    }

    header('Location: ' . $dest);
    exit;                               // arrête l’exécution

} else {
    /* -------------------------------------------------
       9️⃣  Échec d’authentification → message générique
       ------------------------------------------------- */
    $_SESSION['login_error'] = 'E‑mail ou mot de passe incorrect.';
    header('Location: form_login.php');
    exit;
}
?>