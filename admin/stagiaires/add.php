<?php
require_once "../config/connection.php";

// 🧠 Traitement
$errors = [];

if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');

    // Validation
    if(!$nom) $errors[] = "Le nom est requis.";
    if(!$prenom) $errors[] = "Le prénom est requis.";
    if(!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email invalide.";

    // Vérifier email existe
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email=?");
    $stmt->execute([$email]);

    if($stmt->fetchColumn() > 0){
        $errors[] = "Cet email est déjà utilisé.";
    }

    // INSERT
    if(empty($errors)){
        $stmt = $pdo->prepare("
            INSERT INTO users (nom, prenom, email, role)
            VALUES (?, ?, ?, 'stagiaire')
        ");

        $stmt->execute([$nom, $prenom, $email]);

        // ✅ REDIRECT IMPORTANT
        header("Location: dashboard.php?page=stagiaires&success=1");
        exit;
    }
}
?>

<div class="dashboard-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-user-plus"></i> Ajouter Stagiaire</h2>
        <a href="dashboard.php?page=stagiaires" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>

    <!-- ERRORS -->
    <?php if(!empty($errors)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <ul class="mb-0">
                <?php foreach($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- FORM -->
    <form method="POST" action="">
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Nom <span class="text-danger">*</span></label>
                <input type="text" name="nom" class="form-control" required
                       value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>"
                       placeholder="Entrez le nom">
            </div>

            <div class="form-group">
                <label class="form-label">Prénom <span class="text-danger">*</span></label>
                <input type="text" name="prenom" class="form-control" required
                       value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>"
                       placeholder="Entrez le prénom">
            </div>

            <div class="form-group">
                <label class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control" required
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                       placeholder="exemple@domaine.com">
            </div>

            <div class="form-group">
                <label class="form-label">Rôle</label>
                <input type="text" class="form-control" value="Stagiaire" readonly>
            </div>
        </div>

        <div class="form-actions mt-4 d-flex gap-2 justify-content-end">
            <a href="dashboard.php?page=stagiaires" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Annuler
            </a>
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Créer Stagiaire
            </button>
        </div>
    </form>
</div>
