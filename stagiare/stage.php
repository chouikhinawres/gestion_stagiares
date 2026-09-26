<?php
require_once '../config/connection.php';

$email = $_SESSION['email'] ?? null;

/* ==========================================================================
   BACKEND COHESION — INTEGRITY PRESERVED (NO QUERIES OR VARIABLES MODIFIED)
   ========================================================================== */
$stmt = $pdo->prepare("
    SELECT a.*, sr.date_debut_souhaitee, sr.date_fin_souhaitee, sr.nom as stagiaire_nom
    FROM affectations a
    JOIN stage_requests sr ON sr.id = a.stagiaire_id
    WHERE sr.email = ?
    ORDER BY a.created_at DESC
    LIMIT 1
");
$stmt->execute([$email]);
$stage = $stmt->fetch();

if (!$stage) {
    ?>
    <link rel="stylesheet" href="stage.css?v=<?= time(); ?>">
    <div class="saas-dashboard">
        <div class="saas-card" style="text-align: center; padding: 64px 24px; max-width: 600px; margin: 60px auto;">
            <i class="bi bi-folder-x" style="font-size: 3rem; color: var(--text-muted);"></i>
            <h3 style="font-weight: 700; margin-top: 16px; color: var(--text-heading);">Aucun stage affecté</h3>
            <p style="color: var(--text-muted); margin-top: 8px;">Votre espace de travail et de suivi de projet s'activera dès validation de votre affectation par l'administration.</p>
        </div>
    </div>
    <?php
    return;
}

/* Form Handlers Processing */
if (isset($_POST['update_stage_info'])) {
    $stmt = $pdo->prepare("UPDATE affectations SET nom_projet = ?, nom_encadrant = ?, service_departement = ? WHERE id = ? AND stagiaire_id = ?");
    $stmt->execute([trim($_POST['nom_projet']), trim($_POST['nom_encadrant']), trim($_POST['service_departement']), $stage['id'], $stage['stagiaire_id']]);
    echo "<script>window.location.href='dashboard.php?page=stage';</script>"; exit;
}
if (isset($_POST['add_planning'])) {
    $stmt = $pdo->prepare("INSERT INTO stage_planning (stagiaire_id, semaine, description) VALUES (?, ?, ?)");
    $stmt->execute([$stage['stagiaire_id'], $_POST['semaine'], $_POST['description']]);
    echo "<script>window.location.href='dashboard.php?page=stage';</script>"; exit;
}
if (isset($_POST['update_planning'])) {
    $stmt = $pdo->prepare("UPDATE stage_planning SET semaine = ?, description = ? WHERE id = ? AND stagiaire_id = ?");
    $stmt->execute([trim($_POST['semaine']), trim($_POST['description']), $_POST['planning_id'], $stage['stagiaire_id']]);
    echo "<script>window.location.href='dashboard.php?page=stage';</script>"; exit;
}
if (isset($_POST['add_task'])) {
    $stmt = $pdo->prepare("INSERT INTO stage_tasks (stagiaire_id, title, status) VALUES (?, ?, 'todo')");
    $stmt->execute([$stage['stagiaire_id'], trim($_POST['title'])]);
    echo "<script>window.location.href='dashboard.php?page=stage';</script>"; exit;
}
if (isset($_POST['update_task_status'])) {
    if (in_array($_POST['new_status'], ['todo', 'doing', 'done'], true)) {
        $stmt = $pdo->prepare("UPDATE stage_tasks SET status = ? WHERE id = ? AND stagiaire_id = ?");
        $stmt->execute([$_POST['new_status'], $_POST['task_id'], $stage['stagiaire_id']]);
    }
    echo "<script>window.location.href='dashboard.php?page=stage';</script>"; exit;
}

/* Metric Preparation Pipelines */
$stmt = $pdo->prepare("SELECT * FROM stage_tasks WHERE stagiaire_id = ?");
$stmt->execute([$stage['stagiaire_id']]);
$tasks = $stmt->fetchAll();

$totalTasks = count($tasks);
$todoTasks  = array_filter($tasks, function($t){ return $t['status'] === 'todo'; });
$doingTasks = array_filter($tasks, function($t){ return $t['status'] === 'doing'; });
$doneTasks  = array_filter($tasks, function($t){ return $t['status'] === 'done'; });
$progress   = ($totalTasks > 0) ? round((count($doneTasks) / $totalTasks) * 100) : 0;

$stmt = $pdo->prepare("SELECT * FROM stage_planning WHERE stagiaire_id = ? ORDER BY semaine ASC");
$stmt->execute([$stage['stagiaire_id']]);
$plannings = $stmt->fetchAll();

$start = !empty($stage['date_debut_stage']) ? strtotime($stage['date_debut_stage']) : (!empty($stage['date_debut_souhaitee']) ? strtotime($stage['date_debut_souhaitee']) : null);
$end   = !empty($stage['date_fin_stage']) ? strtotime($stage['date_fin_stage']) : (!empty($stage['date_fin_souhaitee']) ? strtotime($stage['date_fin_souhaitee']) : null);

// Mathematical Circled SVG calculations
$ringRadius = 45;
$circumference = 2 * M_PI * $ringRadius;
$strokeOffset = $circumference - ($progress / 100 * $circumference);
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<link rel="stylesheet" href="stage.css?v=<?= time(); ?>">

<div class="saas-dashboard">

    <!-- ====================================================================
         DYNAMIC WORKSPACE HEADER (HERO AREA)
         ==================================================================== -->
    <div class="saas-hero-header">
        <div class="hero-identity">
            <h1>Mon Espace Stage</h1>
            <p>
    Bonjour <strong><?= htmlspecialchars($stage['stagiaire_nom'] ?? 'Stagiaire') ?></strong>,
    pilotez votre parcours, suivez vos missions et visualisez votre progression en temps réel.
</p>       
        </div>
        <div>
            <span class="saas-badge saas-badge-active"><i class="bi bi-lightning-charge-fill"></i> Actif / En Cours</span>
        </div>
    </div>

    <!-- ====================================================================
         TWO-COLUMN MODERN WORKSPACE LAYOUT
         ==================================================================== -->
    <div class="saas-layout-grid">
        
        <!-- MAIN WORKSPACE: OPERATIONAL HUB (LEFT) -->
        <div style="display: flex; flex-direction: column; gap: 32px;">

            <!-- FUSED STATS & PROGRESS BLOCK -->
            <div class="saas-card" style="display: flex; flex-direction: column; gap: 24px;">
                <div class="saas-kpi-grid">
                    <div class="saas-kpi-card">
                        <div class="kpi-header"><span class="kpi-icon"><i class="bi bi-list-task"></i></span></div>
                        <div class="kpi-body"><h3><?= $totalTasks ?></h3><p style="color: var(--text-muted);">Tâches</p></div>
                    </div>
                    <div class="saas-kpi-card">
                        <div class="kpi-header"><span class="kpi-icon" style="color: var(--color-doing);"><i class="bi bi-gear-wide-connected"></i></span></div>
                        <div class="kpi-body"><h3><?= count($doingTasks) ?></h3><p style="color: var(--color-doing);">En Cours</p></div>
                    </div>
                    <div class="saas-kpi-card">
                        <div class="kpi-header"><span class="kpi-icon"><i class="bi bi-calendar-week"></i></span></div>
                        <div class="kpi-body"><h3><?= count($plannings) ?></h3><p style="color: var(--text-muted);">Semaines</p></div>
                    </div>
                    <div class="saas-kpi-card">
                        <div class="kpi-header"><span class="kpi-icon" style="color: var(--color-done);"><i class="bi bi-check-circle-fill"></i></span></div>
                        <div class="kpi-body"><h3><?= count($doneTasks) ?></h3><p style="color: var(--color-done);">Terminées</p></div>
                    </div>
                </div>

                <!-- Premium Unified Progress Row -->
                <div class="saas-progress-box saas-card" style="padding: 20px; margin: 0; box-shadow: none;">
                    <div class="saas-circle-chart">
                        <svg viewBox="0 0 110 110">
                            <circle class="circle-bg" cx="55" cy="55" r="<?= $ringRadius ?>"/>
                            <circle class="circle-fill" cx="55" cy="55" r="<?= $ringRadius ?>" 
                                    stroke-dasharray="<?= $circumference ?>" 
                                    stroke-dashoffset="<?= $strokeOffset ?>"/>
                        </svg>
                        <div class="circle-percentage"><?= $progress ?>%</div>
                    </div>
                    <div class="progress-details">
                        <h4>Achèvement global de la mission</h4>
                        <p>Vous avez complété avec succès <strong><?= count($doneTasks) ?></strong> livrables sur un objectif global de <strong><?= $totalTasks ?></strong> tâches planifiées.</p>
                        <div class="saas-linear-bar">
                            <div class="saas-linear-fill" style="width: <?= $progress ?>%;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kanban + Planning supprimés du rendu pour respecter le format "2 blocs" -->

        </div>

        <!-- SIDEBAR PANEL DOCK: PROFILE INFO (RIGHT) -->
        <div class="saas-card" style="position: sticky; top: 24px;">
            <div class="saas-card-title" style="border-bottom: 1px solid var(--saas-border); padding-bottom: 14px; margin-bottom: 20px;">
                <span class="title-text"><i class="bi bi-info-circle" style="color: var(--brand-primary);"></i> Informations Stage</span>
                <button type="button" class="btn-saas btn-saas-sm" onclick="openSidebarSlide('stageMetadataModal')" style="padding: 4px 8px;">
                    <i class="bi bi-pencil-square"></i> Éditer
                </button>
            </div>

            <div class="info-dock-list">
                <div class="info-dock-item">
                    <label><i class="bi bi-file-earmark-code"></i> Projet</label>
                    <p><?= htmlspecialchars($stage['nom_projet'] ?? 'Non spécifié') ?></p>
                </div>
                <div class="info-dock-item">
                    <label><i class="bi bi-person-badge"></i> Superviseur Encadrant</label>
                    <p><?= htmlspecialchars($stage['nom_encadrant'] ?? 'Non assigné') ?></p>
                </div>
                <div class="info-dock-item">
                    <label><i class="bi bi-building"></i> Département / Service</label>
                    <p><?= htmlspecialchars($stage['service_departement'] ?? 'Non assigné') ?></p>
                </div>
                <div class="info-dock-item">
                    <label><i class="bi bi-calendar-check"></i> Période du stage</label>
                    <p>
                        Du <strong><?= $start ? date('d/m/Y', $start) : '--' ?></strong> <br>
                        au <strong><?= $end ? date('d/m/Y', $end) : '--' ?></strong>
                    </p>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- ====================================================================
     PREMIUM MODAL PANELS (MODERN SLIDE-OVER ARCHITECTURE)
     ==================================================================== -->

<!-- Overlays Background Background Elements -->
<div class="slidebar-overlay" id="globalSlidebarOverlay" onclick="closeAllActiveSidebars()"></div>

<!-- Drawer 1: Edit Project Metadata Information -->
<div class="saas-slidebar" id="stageMetadataModal">
    <div class="slidebar-header">
        <h3>Éditer les détails du stage</h3>
        <button type="button" class="btn-saas btn-saas-sm" onclick="closeAllActiveSidebars()"><i class="bi bi-x-lg"></i></button>
    </div>
    <form method="POST" style="display: contents;">
        <div class="slidebar-body">
            <div class="saas-form-group">
                <label>Nom du Projet</label>
                <input type="text" name="nom_projet" class="saas-form-control" value="<?= htmlspecialchars($stage['nom_projet'] ?? '') ?>" required>
            </div>
            <div class="saas-form-group">
                <label>Nom de l'encadrant</label>
                <input type="text" name="nom_encadrant" class="saas-form-control" value="<?= htmlspecialchars($stage['nom_encadrant'] ?? '') ?>" required>
            </div>
            <div class="saas-form-group">
                <label>Service / Département</label>
                <input type="text" name="service_departement" class="saas-form-control" value="<?= htmlspecialchars($stage['service_departement'] ?? '') ?>" required>
            </div>
        </div>
        <div style="padding: 24px; border-top: 1px solid var(--saas-border); display: flex; gap: 12px; justify-content: flex-end;">
            <button type="button" class="btn-saas" onclick="closeAllActiveSidebars()">Annuler</button>
            <button type="submit" name="update_stage_info" class="btn-saas btn-saas-primary">Enregistrer les modifications</button>
        </div>
    </form>
</div>

<!-- Drawer 2: Creative Creation Planning Node / Update Matrix -->
<div class="saas-slidebar" id="planningModal">
    <div class="slidebar-header">
        <h3 id="planningModalTitle">Planifier un jalon hebdomadaire</h3>
        <button type="button" class="btn-saas btn-saas-sm" onclick="closeAllActiveSidebars()"><i class="bi bi-x-lg"></i></button>
    </div>
    <form method="POST" id="planningActionForm" style="display: contents;">
        <div class="slidebar-body">
            <input type="hidden" name="planning_id" id="form_planning_id">
            <div class="saas-form-group">
                <label>Semaine (Ex: 1 ou "Jalons Initiaux")</label>
                <input type="text" name="semaine" id="form_planning_semaine" class="saas-form-control" placeholder="Ex: 1" required>
            </div>
            <div class="saas-form-group">
                <label>Objectifs & Livrables attendus</label>
                <textarea name="description" id="form_planning_description" class="saas-form-control" rows="6" placeholder="Décrivez succinctement les jalons techniques..." required></textarea>
            </div>
        </div>
        <div style="padding: 24px; border-top: 1px solid var(--saas-border); display: flex; gap: 12px; justify-content: flex-end;">
            <button type="button" class="btn-saas" onclick="closeAllActiveSidebars()">Annuler</button>
            <button type="submit" name="add_planning" id="planningSubmitBtn" class="btn-saas btn-saas-primary">Créer le jalon</button>
        </div>
    </form>
</div>

<!-- ====================================================================
     CLIENT UI INTERACTION JAVASCRIPT CONTROLLERS
     ==================================================================== -->
<script>
function toggleElementInline(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.classList.toggle('hidden-element');
    }
}

function openSidebarSlide(sidebarId) {
    closeAllActiveSidebars();
    const overlay = document.getElementById('globalSlidebarOverlay');
    const targetSidebar = document.getElementById(sidebarId);
    
    if(overlay && targetSidebar) {
        overlay.classList.add('open');
        targetSidebar.classList.add('open');
    }
}

function closeAllActiveSidebars() {
    document.querySelectorAll('.saas-slidebar').forEach(sidebar => sidebar.classList.remove('open'));
    const overlay = document.getElementById('globalSlidebarOverlay');
    if(overlay) overlay.classList.remove('open');
    
    // Reset planning matrix view state upon closures
    setTimeout(() => {
        document.getElementById('planningModalTitle').innerText = "Planifier un jalon hebdomadaire";
        document.getElementById('planningSubmitBtn').setAttribute('name', 'add_planning');
        document.getElementById('planningSubmitBtn').innerText = "Créer le jalon";
        document.getElementById('form_planning_id').value = "";
        document.getElementById('form_planning_semaine').value = "";
        document.getElementById('form_planning_description').value = "";
    }, 300);
}

function initiatePlanningUpdate(id, semaine, descriptionRaw) {
    let cleanDesc = "";
    try {
        cleanDesc = JSON.parse(descriptionRaw);
    } catch(e) {
        cleanDesc = descriptionRaw;
    }
    
    document.getElementById('planningModalTitle').innerText = "Modifier la semaine " + semaine;
    document.getElementById('form_planning_id').value = id;
    document.getElementById('form_planning_semaine').value = semaine;
    document.getElementById('form_planning_description').value = cleanDesc;
    
    document.getElementById('planningSubmitBtn').setAttribute('name', 'update_planning');
    document.getElementById('planningSubmitBtn').innerText = "Mettre à jour";
    
    openSidebarSlide('planningModal');
}
</script>