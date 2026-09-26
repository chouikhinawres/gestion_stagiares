<?php
require_once "../config/connection.php";

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'){
    header("Location: ../../auth/form_login.php");
    exit;
}
   
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if($id <= 0){
    header("Location: dashboard.php?page=stagiaires&success=1");
    exit;
}


// 📥 Récupération données

    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $stagiaire = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$stagiaire) {
    header("Location: dashboard.php?page=stagiaires&success=1");
    exit;
}

$errors = [];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);

     
    if(!$nom) $errors[] = "Le nom est obligatoire";
    if(!$prenom) $errors[] = "Le prénom est obligatoire";
    if(!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email invalide";


    if(empty($errors)){
        $update = $pdo->prepare("
        UPDATE users SET nom = ?, prenom = ?, email = ? WHERE id = ?");
   
        $update->execute([$nom, $prenom, $email, $id]);
        header("Location: dashboard.php?page=stagiaires&success=1");
        exit;
        }
}
?>

<div class="dashboard-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-edit"></i> Modifier Stagiaire #<?= $stagiaire['id'] ?></h2>
        <a href="dashboard.php?page=stagiaires" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>

    <!-- ERRORS -->
    <?php if(!empty($errors)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <div class="mb-0">
                <?php foreach($errors as $e): ?>
                    <div><?= htmlspecialchars($e) ?></div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- ✅ FORM -->
<form method="POST" action="">
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Nom <span class="text-danger">*</span></label>
                <input type="text" name="nom" class="form-control" required
                       value="<?= htmlspecialchars($stagiaire['nom']) ?>"
                       placeholder="Nom complet">
            </div>

            <div class="form-group">
                <label class="form-label">Prénom <span class="text-danger">*</span></label>
                <input type="text" name="prenom" class="form-control" required
                       value="<?= htmlspecialchars($stagiaire['prenom']) ?>"
                       placeholder="Prénom">
            </div>

            <div class="form-group">
                <label class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control" required
                       value="<?= htmlspecialchars($stagiaire['email']) ?>"
                       placeholder="email@exemple.com">
            </div>

            <div class="form-group">
                <label class="form-label">Rôle</label>
                <input type="text" class="form-control bg-light" value="Stagiaire" readonly>
            </div>
        </div>

        <div class="form-actions mt-4 d-flex gap-2 justify-content-end">
            <a href="dashboard.php?page=stagiaires" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Annuler
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Enregistrer les modifications
            </button>
        </div>
    </form>
</div>
