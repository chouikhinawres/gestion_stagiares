<?php
require_once "../config/connection.php";



/* 🔴 DELETE */
if(isset($_GET['delete'])){
    $id = (int) $_GET['delete'];
    if($id > 0){
        $pdo->prepare("DELETE FROM users WHERE id=?")->execute([$id]);
    }
    header("Location: dashboard.php?page=stagiaires&deleted=1");
    exit;
}

/* 🔍 SEARCH */
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$where = "WHERE role = 'stagiaire'";
$params = [];

if($search){
    $where .= " AND (nom LIKE ? OR prenom LIKE ? OR email LIKE ?)";
    $params = ["%$search%", "%$search%", "%$search%"];
}
/* 📄 PAGINATION */
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

/*  COUNT */
$countSql = "SELECT COUNT(*) FROM users $where";
$countStmt = $pdo->prepare($countSql);
$countStmt->execute($params);
$totalRecords = $countStmt->fetchColumn();
$totalPages = ceil($totalRecords / $limit);

// Récupérer les données
$sql = "SELECT * FROM users $where ORDER BY id DESC LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$stagiaires = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="dashboard-card">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="fas fa-user-graduate"></i> Liste des Stagiaires</h2>
            <p class="text-muted mb-0"><?= $totalRecords ?> stagiaire<?= $totalRecords > 1 ? 's' : '' ?> trouvé<?= $totalRecords > 1 ? 's' : '' ?> <?= $search ? '(recherche: "'.$search.'")' : '' ?></p>
        </div>
        <a href="dashboard.php?page=stagiaires&action=add" class="btn btn-primary">
            <i class="fas fa-plus"></i> Ajouter Stagiaire
        </a>
    </div>
<!-- Alerts -->
    <?php if(isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> Opération réussie
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if(isset($_GET['deleted'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-trash-alt"></i> Stagiaire supprimé avec succès
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

<!-- Formulaire recherche -->
    <!-- Search Form -->
    <div class="search-section mb-4">
        <form method="GET" class="d-flex align-items-center gap-2">
            <input type="hidden" name="page" value="stagiaires">
            <div class="flex-grow-1 position-relative">
                <input type="text" name="search" class="form-control ps-4" value="<?= htmlspecialchars($search) ?>" 
                       placeholder="Rechercher par nom, prénom ou email..." <?= $search ? 'autofocus' : '' ?>>
                <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Rechercher
            </button>
            <?php if($search): ?>
                <a href="dashboard.php?page=stagiaires" class="btn btn-outline-secondary">
                    <i class="fas fa-times"></i> Réinitialiser
                </a>
            <?php endif; ?>
        </form>
    </div>


    <!-- Tableau -->

  <div class="table-responsive">
    <table class="table table-hover">
        <thead class="table-light">
        <tr>
            <th>Cin</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if(empty($stagiaires)): ?>
            <tr>
                <td colspan="5" align="center">Aucun stagiaire trouvé</td>
            </tr>
        <?php else: ?>
            <?php foreach($stagiaires as $s): ?>
                <tr>
                    <td><?= $s['id'] ?></td>

                    <td><?= htmlspecialchars($s['nom']) ?></td>
                    <td><?= htmlspecialchars($s['prenom']) ?></td>
                    <td><?= htmlspecialchars($s['email']) ?></td>
                    <td>
                        <a href="dashboard.php?page=stagiaires&action=edit&id=<?= $s['id'] ?>" class="btn btn-sm btn-outline-primary">✏️
                        </a>
                        <a href="dashboard.php?page=stagiaires&delete=<?= $s['id'] ?>" 
                        onclick="return confirm('Supprimer <?= htmlspecialchars($s['nom']) ?> ?')"
                        class="btn btn-danger btn-sm">🗑️
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
    </div>
<!-- PAGINATION -->
<?php if($totalPages > 1): ?>
        <nav>
            <ul class="pagination justify-content-center">
                <?php for($i=1; $i<=$totalPages; $i++): ?>
                    <li class="page-item <?= $i==$pageNum?'active':'' ?>">
                        <a class="page-link" href="dashboard.php?page=stagiaires&pageNum=<?= $i ?>&search=<?= urlencode($search) ?>">
                            <?= $i ?>
                        </a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>

</div>

