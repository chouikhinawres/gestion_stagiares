<?php
session_start();
require_once "../../config/connection.php";

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'){
    header("Location: ../../auth/form_login.php");
    exit;
}

$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);
    $telephone = trim($_POST['telephone']);
    $date_debut_souhaitee = $_POST['date_debut_souhaitee'];
    $date_fin_souhaitee = $_POST['date_fin_souhaitee'];

    $sql = "INSERT INTO stage_requests (nom, prenom, email, telephone, date_debut_souhaitee, date_fin_souhaitee, statut, created_at) VALUES (?, ?, ?, ?, ?, ?, 'en attente', NOW())";
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute([$nom, $prenom, $email, $telephone, $date_debut_souhaitee, $date_fin_souhaitee])) {
        header("Location: list.php?success=1");
        exit;
    } else {
        $message = 'Erreur lors de l\'ajout.';
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter Stagiaire - Admin</title>
    <link rel="stylesheet" href="../dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <!-- Top Navbar -->
    <nav class="top-navbar">
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
                <button class="notifications">
                    <i class="fas fa-bell"></i>
                </button>
                <div class="user-btn">
                    <i class="fas fa-user"></i>
                    <span>Admin</span>
                </div>
                <div class="user-dropdown">
                    <a href="../dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                    <hr>
                    <a href="../../../auth/logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-user-graduate"></i>
            <span>Ajouter Stagiaire</span>
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
            <a href="create.php" class="active">
                <i class="fas fa-plus"></i>
                <span>Ajouter</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="page-header">
            <div>
                <h1><i class="fas fa-user-plus"></i> Ajouter un Stagiaire</h1>
                <p>Remplissez le formulaire pour ajouter une nouvelle demande de stage</p>
            </div>
            <a href="list.php" class="btn-reject" style="padding: 0.75rem 1.5rem;">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
        </div>

        <?php if ($message): ?>
        <div class="dashboard-card" style="background: rgba(239,68,68,0.2); border-color: var(--danger);">
            <div style="padding: 1rem;">
                <i class="fas fa-exclamation-triangle" style="color: var(--danger);"></i> <?= $message ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="dashboard-grid">
            <div class="dashboard-card full-width">
                <div class="card-header">
                    <h3><i class="fas fa-edit"></i> Formulaire d'ajout</h3>
                </div>
                <div class="card-body">
                    <form method="POST" class="form-affectation">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                            <div>
                                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: white;">Nom *</label>
                                <input type="text" name="nom" required style="form-style">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: white;">Prénom *</label>
                                <input type="text" name="prenom" required style="form-style">
                            </div>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                            <div>
                                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: white;">Email *</label>
                                <input type="email" name="email" required style="form-style">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: white;">Téléphone *</label>
                                <input type="tel" name="telephone" required style="form-style">
                            </div>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                            <div>
                                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: white;">Date début souhaitée *</label>
                                <input type="date" name="date_debut_souhaitee" required style="form-style">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: white;">Date fin souhaitée *</label>
                                <input type="date" name="date_fin_souhaitee" required style="form-style">
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <button type="submit" class="btn-accept" style="padding: 1rem 2rem; font-size: 1rem;">
                                <i class="fas fa-save"></i> Ajouter le stagiaire
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
            document.querySelector('.sidebar-overlay').classList.toggle('active');
        }

        // Form style helper
        const inputs = document.querySelectorAll('input');
        inputs.forEach(input => {
            input.style.width = '100%';
            input.style.padding = '0.75rem';
            input.style.border = '1px solid var(--border)';
            input.style.borderRadius = '6px';
            input.style.background = 'rgba(255,255,255,0.9)';
            input.style.fontSize = '1rem';
        });
    </script>

    <style>
        .form-style {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border);
            border-radius: 6px;
            background: rgba(255,255,255,0.9);
            font-size: 1rem;
        }
        .form-style:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99,102,241,0.1);
        }
    </style>
</body>
</html>
