<?php
require_once "../config/connection.php";

// 🔴 DELETE
if(isset($_GET['delete'])){
    $id = (int) $_GET['delete'];
    if($id > 0){
        $pdo->prepare("DELETE FROM encadrants WHERE id=?")->execute([$id]);
    }
    header("Location: dashboard.php?page=encadrants&deleted=1");
    exit;
}

// 🔍 Recherche
$search = trim($_GET['search'] ?? '');
$where = '';
$params = [];

if($search){
    $where = "WHERE nom LIKE ? OR prenom LIKE ? OR email LIKE ?";
    $params = ["%$search%", "%$search%", "%$search%"];
}

// 📄 Pagination
$pageNum = isset($_GET['pageNum']) ? max(1,(int)$_GET['pageNum']) : 1;
$limit = 10;
$offset = ($pageNum - 1) * $limit;

// Count total records
$countSql = "SELECT COUNT(*) FROM encadrants $where";
$countStmt = $pdo->prepare($countSql);
$countStmt->execute($params);
$totalRecords = $countStmt->fetchColumn();
$totalPages = ceil($totalRecords / $limit);

// Récupérer les données
$dataSql = "SELECT * FROM encadrants $where ORDER BY id DESC LIMIT ? OFFSET ?";
$paramsWithLimit = array_merge($params, [$limit, $offset]);

$dataStmt = $pdo->prepare($dataSql);
$dataStmt->execute($paramsWithLimit);
$encadrants = $dataStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="dashboard-card">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="fas fa-chalkboard-teacher"></i> Liste des Encadrants</h2>
            <p class="text-muted mb-0"><?= $totalRecords ?> encadrant<?= $totalRecords > 1 ? 's' : '' ?> trouvé<?= $totalRecords > 1 ? 's' : '' ?> <?= $search ? '(recherche: "'.$search.'")' : '' ?></p>
        </div>
        <a href="dashboard.php?page=encadrants&action=add" class="btn btn-primary">
            <i class="fas fa-plus"></i> Ajouter Encadrant
        </a>
    </div>

    <!-- Alerts -->
    <!-- Alerts -->
    <?php if(isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> Opération réussie
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if(isset($_GET['deleted'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-trash-alt"></i> Encadrant supprimé avec succès
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Formulaire recherche -->
    <!-- Search Form -->
    <div class="search-section mb-4">
        <form method="GET" class="d-flex align-items-center gap-2">
            <input type="hidden" name="page" value="encadrants">
            <div class="flex-grow-1 position-relative">
                <input type="text" name="search" class="form-control ps-4" value="<?= htmlspecialchars($search) ?>" 
                       placeholder="Rechercher par nom, prénom ou email..." <?= $search ? 'autofocus' : '' ?>>
                <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Rechercher
            </button>
            <?php if($search): ?>
                <a href="dashboard.php?page=encadrants" class="btn btn-outline-secondary">
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
                    <th>ID</th>
                    <th>Nom complet</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($encadrants)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">
                            Aucun encadrant trouvé.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach($encadrants as $e): ?>
                        <tr>
                            <td><?= $e['id'] ?></td>
                            <td><?= htmlspecialchars($e['nom'].' '.$e['prenom']) ?></td>
                            <td><?= htmlspecialchars($e['email']) ?></td>
                            <td><?= htmlspecialchars($e['telephone']) ?></td>
                            <td>
                                <a href="dashboard.php?page=encadrants&action=edit&id=<?= $e['id'] ?>" class="btn btn-primary btn-sm">✏️</a>
                                <a href="dashboard.php?page=encadrants&delete=<?= $e['id'] ?>" 
                                   onclick="return confirm('Supprimer <?= htmlspecialchars($e['nom']) ?> ?')"
                                   class="btn btn-danger btn-sm">🗑️
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if($totalPages > 1): ?>
        <nav>
            <ul class="pagination justify-content-center">
                <?php for($i=1; $i<=$totalPages; $i++): ?>
                    <li class="page-item <?= $i==$pageNum?'active':'' ?>">
                        <a class="page-link" href="dashboard.php?page=encadrants&pageNum=<?= $i ?>&search=<?= urlencode($search) ?>">
                            <?= $i ?>
                        </a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>

</div>