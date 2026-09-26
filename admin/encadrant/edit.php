<?php
require_once "../config/connection.php";

// 🔐 Vérification admin
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'){
    header("Location: ../../auth/form_login.php");
    exit;
}

// 📌 ID encadrant
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if($id <= 0){
    header("Location: dashboard.php?page=encadrants&success=1");
    exit;
}

// 📥 Récupération données
$stmt = $pdo->prepare("SELECT * FROM encadrants WHERE id = ?");
$stmt->execute([$id]);
$encadrant = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$encadrant){
    header("Location: dashboard.php?page=encadrants&success=1");
    exit;
}

// 🧠 Traitement formulaire
$errors = [];

if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);
    $telephone = trim($_POST['telephone']);

    // Validation
    if(!$nom) $errors[] = "Le nom est obligatoire";
    if(!$prenom) $errors[] = "Le prénom est obligatoire";
    if(!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email invalide";
    if(!$telephone) $errors[] = "Le téléphone est obligatoire";

    // Update
    if(empty($errors)){
        $update = $pdo->prepare("
            UPDATE encadrants 
            SET nom=?, prenom=?, email=?, telephone=? 
            WHERE id=?
        ");

        $update->execute([$nom, $prenom, $email, $telephone, $id]);

        // ✅ REDIRECT CORRECT
        header("Location: dashboard.php?page=encadrants&success=1");
        exit;
    }
}
?>

<div class="card">
    <h2><i class="fas fa-edit"></i> Modifier Encadrant</h2>

    <!-- ❌ ERRORS -->
    <?php if(!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach($errors as $e): ?>
                <div><?= htmlspecialchars($e) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- ✅ FORM -->
<form method="POST" action="">
        <div class="mb-3">
            <label class="form-label">Nom</label>
            <input type="text" name="nom" class="form-control"
                   value="<?= htmlspecialchars($encadrant['nom']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Prénom</label>
            <input type="text" name="prenom" class="form-control"
                   value="<?= htmlspecialchars($encadrant['prenom']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control"
                   value="<?= htmlspecialchars($encadrant['email']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Téléphone</label>
            <input type="text" name="telephone" class="form-control"
                   value="<?= htmlspecialchars($encadrant['telephone']) ?>">
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Enregistrer
        </button>

        <a href="dashboard.php?page=encadrants" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>

    </form>
</div>