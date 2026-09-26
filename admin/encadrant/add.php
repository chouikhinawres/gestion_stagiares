<?php
require_once "../config/connection.php";

// 🔐 Vérification admin
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'){
    header("Location: ../../auth/form_login.php");
    exit;
}

// 🧠 Traitement
$errors = [];

if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');

    // Validation
    if(!$nom) $errors[] = "Le nom est requis.";
    if(!$prenom) $errors[] = "Le prénom est requis.";
    if(!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email invalide.";
    if(!$telephone) $errors[] = "Téléphone requis.";

    // Vérifier email existe
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM encadrants WHERE email=?");
    $stmt->execute([$email]);

    if($stmt->fetchColumn() > 0){
        $errors[] = "Cet email est déjà utilisé.";
    }

    // INSERT
    if(empty($errors)){
        $stmt = $pdo->prepare("
            INSERT INTO encadrants (nom, prenom, email, telephone)
            VALUES (?, ?, ?, ?)
        ");

        $stmt->execute([$nom, $prenom, $email, $telephone]);

        // ✅ REDIRECT IMPORTANT
        header("Location: dashboard.php?page=encadrants&success=1");
        exit;
    }
}
?>

<div class="card">
    <h2><i class="fas fa-user-plus"></i> Ajouter Encadrant</h2>

    <!-- ERRORS -->
    <?php if(!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- FORM -->
    <form method="POST" action="">

        <div class="mb-3">
            <label>Nom</label>
            <input type="text" name="nom" class="form-control"
                   value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>">
        </div>

        <div class="mb-3">
            <label>Prénom</label>
            <input type="text" name="prenom" class="form-control"
                   value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>">
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control"
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>

        <div class="mb-3">
            <label>Téléphone</label>
            <input type="text" name="telephone" class="form-control"
                   value="<?= htmlspecialchars($_POST['telephone'] ?? '') ?>">
        </div>

        <button type="submit" class="btn btn-success">
            <i class="fas fa-plus"></i> Ajouter
        </button>

        <!-- ✅ RETOUR CORRECT -->
        <a href="dashboard.php?page=encadrants" class="btn btn-secondary">
            Retour
        </a>

    </form>
</div>