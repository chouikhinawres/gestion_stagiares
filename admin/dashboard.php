// admin/dashboard.php
<?php
session_start();
require_once '../config/connection.php';

// Vérification admin
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'){
    header("Location: ../auth/form_login.php");
    exit;
}

// Page/action
$page = $_GET['page'] ?? 'home';
$action = $_GET['action'] ?? '';

// STATS pour dashboard home
$stats = [
    'demandes' => $pdo->query("SELECT COUNT(*) FROM stage_requests")->fetchColumn(),
    'stagiaires' => $pdo->query("SELECT COUNT(*) FROM users WHERE role='stagiaire'")->fetchColumn(),
    'encadrants' => $pdo->query("SELECT COUNT(*) FROM encadrants")->fetchColumn()
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel </title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
    <aside class="sidebar">
        <div class="logo">
            <div class="logo-icon">
                <svg viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
            </div>
            <span class="logo-text">Admin Panel</span>
           <span class="logo-badge">Dashboard</span>
        </div>
        <div class="nav-section">
            <div class="nav-label">Main Menu
            </div>
            <a href="dashboard.php?page=home" class="nav-item active" class="<?= $page=='home'?'active':'' ?>">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/></svg>
                Dashboard
            </a>
            <a class="nav-item" href="dashboard.php?page=demandes" class="<?= $page=='demandes'?'active':'' ?>">
                <i class="fas fa-clipboard-list"></i>
                <span>Demandes</span>
            </a>
            <a class="nav-item" href="dashboard.php?page=encadrants" class="<?= $page=='encadrants'?'active':'' ?>">
                <i class="fas fa-chalkboard-teacher"></i>
                <span>Encadrants</span>
            </a>
            <a class="nav-item" href="dashboard.php?page=stagiaires" class="<?= $page=='stagiaires'?'active':'' ?>">
                <i class="fas fa-user-graduate"></i>
                <span>Stagiaires</span>
            </a>
        </div>
        <div class="nav-section" style="margin-top:auto;">
            <a class="nav-item" href="#">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 010 14.14M4.93 4.93a10 10 0 000 14.14"/></svg>
                Account Settings
            </a>
        </div>
    </aside>
    <div class="main">
        <header class="topbar">
            <div class="topbar-greeting">Welcome Admin! 👋</div>
            <div class="topbar-actions">
                <button class="theme-toggle" id="themeToggle" title="Toggle dark mode">
                    <svg class="sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
                   <svg class="moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg>
                </button>
                <button class="icon-btn" title="Settings">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 010 14.14M4.93 4.93a10 10 0 000 14.14"/></svg>
                </button>
                <button class="icon-btn" title="Refresh">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 11-2.12-9.36L23 10"/></svg>
                </button>
                <button class="icon-btn" title="Notifications">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>
                </button>
                <div class="avatar">DA</div>
                <span class="user-info">Admin</span>
                <span class="chevron"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg></span>
            </div>
        </header>
        <!-- PAGE -->
        <div class="page">
            <main class="main-content fade-in-up">
                <div class="page-header mb-4">
                    <h1 class="page-title">
                <i class="fas <?= $page=='home' ? 'fa-tachometer-alt' : 'fa-list' ?>"></i>
                <?= [
                    'home' => 'Dashboard',
                    'demandes' => 'Demandes de Stage',
                    'stagiaires' => 'Stagiaires',
                    'encadrants' => 'Encadrants'
                ][$page] ?? 'Admin' ?>
            </h1>
        </div>

        <?php if($page == "home"): ?>
            <!-- STATS CARDS -->
            <div class="stats-grid">
                <div class="stat-card">
<i class="fas fa-clipboard-list fa-2x mb-3"></i>
                    <div class="stat-value"><?= $stats['demandes'] ?></div>
                    <div class="stat-label"">Demandes</div>
                </div>
                <div class="stat-card scale-in" style="animation-delay: 0.1s;">
<i class="fas fa-user-graduate fa-2x mb-3"></i>
                    <div class="stat-value"><?= $stats['stagiaires'] ?></div>
                    <div class="stat-label">Stagiaires</div>
                </div>
                <div class="stat-card scale-in" style="animation-delay: 0.2s;">
<i class="fas fa-chalkboard-teacher fa-2x mb-3"></i>
                    <div class="stat-value"><?= $stats['encadrants'] ?></div>
                    <div class="stat-label">Encadrants</div>
                </div>   
            </div>

            
        <?php endif; ?>

        <!-- CONTENT PAGES -->
        <?php
        if($page == "demandes"){
            include "demandes.php";
        } elseif($page == 'encadrants') {
            if($action == 'add'){
                include "encadrant/add.php";
            } elseif($action == 'edit' && isset($_GET['id'])) {
                include "encadrant/edit.php";
            } else {
                include "encadrant/list.php";
            }
        } elseif($page == 'stagiaires') {
            if($action == 'add'){
                include "stagiaires/add.php";
            } elseif($action == 'edit' && isset($_GET['id'])) {
                include "stagiaires/edit.php";
            } else {
                include "stagiaires/list.php";
            }
        } else {
            echo "";
        }
        ?>
    </div>
</div>

<!-- JS -->
<script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script><script>


  // Dark mode toggle
  const toggle = document.getElementById('themeToggle');
  const saved = localStorage.getItem('theme');
  if (saved === 'dark') document.body.classList.add('dark');

  toggle.addEventListener('click', () => {
    document.body.classList.toggle('dark');
    localStorage.setItem('theme', document.body.classList.contains('dark') ? 'dark' : 'light');
  });
      </script>
</body>
</html>
