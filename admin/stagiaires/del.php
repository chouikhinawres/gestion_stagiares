<?php
session_start();
require_once "../../config/connection.php";

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'){
    header("Location: ../../auth/form_login.php");
    exit;
}

$id = $_GET['id'] ?? 0;

$stagiaire = null;
if ($id > 0) {
    $stmt = $pdo->prepare("SELECT nom, prenom FROM stage_requests WHERE id = ?");
    $stmt->execute([$id]);
    $stagiaire = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (!$stagiaire) {
    header("Location: list.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $deleteStmt = $pdo->prepare("DELETE FROM stage_requests WHERE id = ?");
    $deleteStmt->execute([$id]);
    header("Location: list.php?deleted=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supprimer Stagiaire - Admin</title>
    <link rel="stylesheet" href="../dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <!-- Navbar and Sidebar (same) -->
    <nav class="top-navbar">
        <!-- same as before -->
        <div class="navbar-container">
            <div class="navbar-left">
                <button class="menu-toggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="logo">
                    <i class="fas fa-graduation-cap"></i>
                    <span>Gestion Stages</span>
                </div>
            </div>
            <div class="navbar-center">
                <div class="search-bar">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Rechercher...">
                </div>
            </div>
            <div class="navbar-right">
                <div class="user-btn">
                    <i class="fas fa-user"></i> Admin
                </div>
            </div>
        </div>
    </nav>

    <div class="sidebar-overlay" onclick="toggleSidebar()"></div>

    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-trash"></i>
            <span>Supprimer</span>
        </div>
        <nav class="sidebar-menu">
            <a href="../dashboard.php">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            <a href="list.php">
                <i class="fas fa-list"></i>
                <span>Liste</span>
            </a>
        </nav>
    </aside>

    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-trash"></i> Confirmer suppression</h1>
        </div>

        <div class="dashboard-grid">
            <div class="dashboard-card full-width">
                <div class="card-header">
                    <h3><i class="fas fa-exclamation-triangle"></i> Êtes-vous sûr ?</h3>
                </div>
                <div class="card-body">
                    <div style="text-align: center; padding: 2rem;">
                        <i class="fas fa-user-times" style="font-size: 4rem; color: var(--danger); margin-bottom: 1rem;"></i>
                        <h2>Supprimer <?= htmlspecialchars($stagiaire['nom'].' '.$stagiaire['prenom']) ?></h2>
                        <p style="color: var(--secondary); max-width: 400px; margin: 0 auto 2rem;">
                            Cette action est irréversible. Le stagiaire sera définitivement supprimé de la base de données.
                        </p>
                        <div style="display: flex; gap: 1rem; justify-content: center;">
                            <form method="POST" style="display: inline;">
                                <button type="submit" class="btn-reject" style="padding: 1rem 2rem; font-size: 1rem;">
                                    <i class="fas fa-trash"></i> Oui, supprimer
                                </button>
                            </form>
                            <a href="list.php" class="btn-accept" style="padding: 1rem 2rem; font-size: 1rem; text-decoration: none;">
                                <i class="fas fa-times"></i> Annuler
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
            document.querySelector('.sidebar-overlay').classList.toggle('active');
        }
    </script>
</body>
</html>

