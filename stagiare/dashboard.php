<?php
session_start();
require_once '../config/connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'stagiaire') {
    header("Location: ../auth/form_login.php");
    exit;
}

$email = $_SESSION['email'];

// FIX : traitement du formulaire de demande ici, AVANT tout HTML
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['soumettre'])) {
    $nom    = trim($_POST['nom']      ?? '');
    $prenom = trim($_POST['prenom']   ?? '');
    $tel    = trim($_POST['tel']      ?? '');
    $debut  = $_POST['date_debut']    ?? '';
    $fin    = $_POST['date_fin']      ?? '';
    $lettre = trim($_POST['lettre']   ?? '');
    $cv_path = null;

    if (isset($_FILES['cv_demande']) && $_FILES['cv_demande']['error'] === 0) {
        $ext = strtolower(pathinfo($_FILES['cv_demande']['name'], PATHINFO_EXTENSION));
        if ($ext === 'pdf' && $_FILES['cv_demande']['size'] <= 5 * 1024 * 1024) {
            $cv_path = time() . '_cv_' . basename($_FILES['cv_demande']['name']);
            move_uploaded_file($_FILES['cv_demande']['tmp_name'], "../uploads/" . $cv_path);
        }
    }

    $stmt = $pdo->prepare("INSERT INTO stage_requests (email, nom, prenom, telephone, date_debut_souhaitee, date_fin_souhaitee, lettre_motivation, cv_path, statut, created_at)
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'en attente', NOW())");
    $stmt->execute([$email, $nom, $prenom, $tel, $debut, $fin, $lettre, $cv_path]);

    header("Location: dashboard.php?page=demandes&success=1");
    exit;
}

if (isset($_POST['upload_photo']) && isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
    $fileName = time() . "_" . basename($_FILES['photo']['name']);
    move_uploaded_file($_FILES['photo']['tmp_name'], "../uploads/" . $fileName);
    $pdo->prepare("UPDATE users SET photo=? WHERE email=?")->execute([$fileName, $email]);
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
}

if (isset($_POST['upload_cv']) && isset($_FILES['cv']) && $_FILES['cv']['error'] === 0) {
    $fileName = time() . "_" . basename($_FILES['cv']['name']);
    move_uploaded_file($_FILES['cv']['tmp_name'], "../uploads/" . $fileName);
    $pdo->prepare("UPDATE users SET cv=? WHERE email=?")->execute([$fileName, $email]);
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE email=?");
$stmt->execute([$email]);
$user = $stmt->fetch();

$photo    = $user['photo'] ?? null;
$cv       = $user['cv']    ?? null;

$stmt = $pdo->prepare("SELECT * FROM stage_requests WHERE email=? ORDER BY created_at DESC");
$stmt->execute([$email]);
$demandes = $stmt->fetchAll();

$total   = count($demandes);
$attente = count(array_filter($demandes, function($d){
    return strtolower($d['statut']) === 'en attente';
}));

$ok = count(array_filter($demandes, function($d){
    return in_array(strtolower($d['statut']), ['accepté','approuvé','approved']);
}));
$page     = $_GET['page'] ?? 'home';
$initials = strtoupper(substr($email, 0, 2));

$pageTitles = [
    'home'     => 'Tableau de bord',
    'demandes' => 'Mes demandes de stage',
    'ajouter'  => 'Nouvelle demande',
];
$pageIcons = [
    'home'     => 'fa-tachometer-alt',
    'demandes' => 'fa-list',
    'ajouter'  => 'fa-plus-circle',
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= htmlspecialchars($pageTitles[$page] ?? 'Dashboard') ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<link rel="stylesheet" href="dashboard.css?v=<?= filemtime('dashboard.css') ?>">

<!-- FIX DARK MODE : appliqué AVANT le rendu, évite le flash -->
<script>
    (function(){
        if(localStorage.getItem('theme')==='dark'){
            document.documentElement.classList.add('dark-init');
        }
    })();
</script>
<style>
    /* Appliqué immédiatement avant que le CSS charge */
    html.dark-init body { background:#0d1117 !important; }
</style>
</head>
<body>

<script>
    /* Transfert de la classe sur body dès que possible */
    if(localStorage.getItem('theme')==='dark') document.body.classList.add('dark');
</script>

<!-- SIDEBAR -->
<div class="sidebar">
    <div class="logo">
        <span class="logo-text">Stagiaire Panel</span>
        <span class="logo-badge">v2</span>
    </div>

    <div class="nav-section">
        <div class="nav-label">Menu principal</div>

        <a href="dashboard.php?page=home" class="nav-item <?= $page==='home' ? 'active' : '' ?>">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="7" rx="1"/>
                <rect x="14" y="3" width="7" height="7" rx="1"/>
                <rect x="14" y="14" width="7" height="7" rx="1"/>
                <rect x="3" y="14" width="7" height="7" rx="1"/>
            </svg>
            Dashboard
        </a>

        <a href="dashboard.php?page=demandes" class="nav-item <?= $page==='demandes' ? 'active' : '' ?>">
            <i class="fas fa-clipboard-list"></i>
            Demandes
        </a>

        <a href="dashboard.php?page=ajouter" class="nav-item <?= $page==='ajouter' ? 'active' : '' ?>">
            <i class="fas fa-plus-circle"></i>
            Ajouter
        </a>
        <a href="dashboard.php?page=stage" class="nav-item <?= $page==='stage'?'active':'' ?>">
            <i class="fas fa-briefcase"></i>
            Mon Stage
        </a>
        <a href="dashboard.php?page=stage_calendar" class="nav-item <?= $page==='stage_calendar'?'active':'' ?>">
            <i class="fas fa-calendar-alt"></i>
            Planning
        </a>
    </div>
    

    <div class="nav-section" style="margin-top:auto;">
        <a class="nav-item" href="#">
            <i class="fas fa-cog"></i>
            Paramètres
        </a>
        <a class="nav-item" href="../auth/logout.php">
            <i class="fas fa-sign-out-alt"></i>
            Déconnexion
        </a>
    </div>
</div>

<!-- MAIN -->
<div class="main">
    <header class="topbar">
        <div class="topbar-greeting">Bienvenue ! 👋</div>
        <div class="topbar-actions">
            <button class="theme-toggle" id="themeToggle" title="Mode sombre">
                <svg class="sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="5"/>
                    <line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/>
                    <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
                    <line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/>
                    <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
                </svg>
                <svg class="moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
                </svg>
            </button>
            <button class="icon-btn" title="Notifications">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                    <path d="M13.73 21a2 2 0 01-3.46 0"/>
                </svg>
            </button>
            <div class="topbar-avatar"><?= $initials ?></div>
            <span class="user-info" title="<?= htmlspecialchars($email) ?>"><?= htmlspecialchars($email) ?></span>
            <span class="chevron">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="6 9 12 15 18 9"/>
                </svg>
            </span>
        </div>
    </header>

    <div class="page fade-in-up">
        
        <?php if ($page === 'home'): ?>


            <!-- PROFIL -->
            <div class="card profile-card">
                <div class="profile-avatar-wrap">
                    <form method="POST" enctype="multipart/form-data" id="photoForm">
                        <label for="photoInput">
                            <?php if ($photo): ?>
                                <img src="../uploads/<?= htmlspecialchars($photo) ?>" alt="Photo">
                            <?php else: ?>
                                <?= $initials ?>
                            <?php endif; ?>
                            <input type="file" id="photoInput" name="photo" accept="image/*" hidden onchange="document.getElementById('photoForm').submit()">
                            <input type="hidden" name="upload_photo">
                        </label>
                    </form>
                    <div class="profile-avatar-edit"><i class="fas fa-camera"></i></div>
                </div>
                <div class="profile-name"><?= htmlspecialchars($user['nom'] ?? explode('@',$email)[0]) ?></div>
                <div class="profile-role">Stagiaire</div>
                <div class="profile-email"><?= htmlspecialchars($email) ?></div>
                <div class="profile-stats">
                    <div class="profile-stat-item">
                        <div class="profile-stat-val" style="color:var(--accent)"><?= $total ?></div>
                        <div class="profile-stat-label">Total</div>
                    </div>
                    <div class="profile-stat-item">
                        <div class="profile-stat-val" style="color:#f59e0b"><?= $attente ?></div>
                        <div class="profile-stat-label">Attente</div>
                    </div>
                    <div class="profile-stat-item">
                        <div class="profile-stat-val" style="color:#22c55e"><?= $ok ?></div>
                        <div class="profile-stat-label">Accepté</div>
                    </div>
                </div>
            </div>

            <!-- DEMANDES RÉCENTES -->
            <div class="card table-card">
                <h4>Demandes récentes</h4>
                <?php if ($demandes): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($demandes, 0, 5) as $d): ?>
                        <tr>
                            <td><?= htmlspecialchars($d['created_at']) ?></td>
                            <td>
                                <?php
                                $s = strtolower($d['statut']);
                                $cls = 'badge-attente';
                                if (in_array($s, ['accepté','approved'])) $cls = 'badge-ok';
                                elseif (in_array($s, ['refusé','rejected'])) $cls = 'badge-refuse';
                                ?>
                                <span class="badge-statut <?= $cls ?>">
                                    <?= htmlspecialchars($d['statut']) ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <p style="color:var(--text-muted);font-size:14px;text-align:center;padding:24px 0;">
                    Aucune demande pour le moment.<br>
                    <a href="?page=ajouter" style="color:var(--accent);font-weight:600;">Créer une demande →</a>
                </p>
                <?php endif; ?>
            </div>

            <!-- CV -->
            <div class="card cv-card">
                <h4><i class="fas fa-file-pdf" style="color:var(--accent)"></i> Mon CV</h4>
                <form method="POST" enctype="multipart/form-data">
                    <div class="upload-zone">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p>Glisser ou cliquer</p>
                        <input type="file" name="cv" accept=".pdf" onchange="this.form.submit()">
                        <input type="hidden" name="upload_cv">
                    </div>
                </form>
                <?php if ($cv): ?>
                <a href="../uploads/<?= htmlspecialchars($cv) ?>" class="btn-cv-download" target="_blank">
                    <i class="fas fa-download"></i> Télécharger CV
                </a>
                <?php endif; ?>
            </div>

        

        <?php elseif ($page === 'demandes'): ?>

        <div class="card table-card">
            <h4>Toutes mes demandes</h4>
            <?php if ($demandes): ?>
            <table>
                <thead>
                    <tr><th>Date</th><th>Statut</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($demandes as $d): ?>
                    <tr>
                        <td><?= htmlspecialchars($d['created_at']) ?></td>
                        <td>
                            <?php
                            $s = strtolower($d['statut']);
                            $cls = 'badge-attente';
                            if (in_array($s, ['accepté','approved'])) $cls = 'badge-ok';
                            elseif (in_array($s, ['refusé','rejected'])) $cls = 'badge-refuse';
                            ?>
                            <span class="badge-statut <?= $cls ?>"><?= htmlspecialchars($d['statut']) ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p style="color:var(--text-muted);text-align:center;padding:24px 0;">Aucune demande.</p>
            <?php endif; ?>
        </div>

        <?php elseif ($page === 'ajouter'): ?>
    <?php include "demande/demande_stage.php"; ?>

<?php elseif ($page === 'stage'): ?>
    <?php include "stage.php"; ?>

<?php elseif ($page === 'stage_calendar'): ?>
    <?php include "calendar.php"; ?>

<?php endif; ?>

    </div>
</div>

<script>
    const toggle = document.getElementById('themeToggle');
    toggle.addEventListener('click', () => {
        const isDark = document.body.classList.toggle('dark');
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
    });
</script>
</body>
</html>