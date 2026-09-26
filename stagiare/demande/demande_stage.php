<?php
require_once "../config/connection.php";

// 🔐 Vérification admin
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'stagiaire'){
    header("Location: ../../auth/form_login.php");
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['soumettre'])) {
    $nom      = trim($_POST['nom']      ?? '');
    $prenom   = trim($_POST['prenom']   ?? '');
    $tel      = trim($_POST['tel']      ?? '');
    $debut    = $_POST['date_debut']    ?? '';
    $fin      = $_POST['date_fin']      ?? '';
    $lettre   = trim($_POST['lettre']   ?? '');
    $email    = $_SESSION['email'];
    $cv_path  = null;

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
?>

<div class="form-card">
    <div class="form-header">
        <div class="form-header-title">
            <i class="fas fa-paper-plane"></i>
            Nouvelle demande de stage
        </div>
        <p class="form-header-sub">Remplissez ce formulaire pour postuler à un stage</p>
    </div>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-body">

            <div class="form-row cols-2">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-user"></i> Nom <span class="required">*</span>
                    </label>
                    <input type="text" name="nom" class="form-input" placeholder="Votre nom" required>
                </div>
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-user"></i> Prénom <span class="required">*</span>
                    </label>
                    <input type="text" name="prenom" class="form-input" placeholder="Votre prénom" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-phone"></i> Téléphone
                </label>
                <input type="tel" name="tel" class="form-input" placeholder="+216 XX XXX XXX">
            </div>

            <div class="form-row cols-2">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-calendar-alt"></i> Date de début <span class="required">*</span>
                    </label>
                    <input type="date" name="date_debut" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-calendar-check"></i> Date de fin <span class="required">*</span>
                    </label>
                    <input type="date" name="date_fin" class="form-input" required>
                </div>
            </div>
<div class="form-row cols-2">
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-file-pdf"></i> lettre de motivation (PDF)
                </label>
                <div class="upload-field" id="uploadZone">
                    <div class="upload-icon">
                        <i class="fas fa-cloud-upload-alt"></i>
                    </div>
                    <p>Glisser-déposer votre lettre de motivation ici</p>
                    <small>Format PDF uniquement · Max 5 Mo</small>
                    <input type="file" name="cv_demande" accept=".pdf" id="cvInput"
                           onchange="showFileName(this)">
                </div>
                <div id="fileInfo" style="
                    display:none;
                    align-items:center; gap:10px; margin-top:8px;
                    padding:10px 14px;
                    background:var(--accent-light); border:1px solid var(--accent);
                    border-radius:8px; font-size:13px; color:var(--accent);
                ">
                    <i class="fas fa-file-pdf"></i>
                    <span id="fileName"></span>
                    <button type="button" onclick="clearFile()" style="
                        margin-left:auto; background:none; border:none;
                        color:var(--accent); cursor:pointer; padding:0; font-size:14px;
                    "><i class="fas fa-times"></i></button>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-file-pdf"></i> CV (PDF)
                </label>
                <div class="upload-field" id="uploadZone">
                    <div class="upload-icon">
                        <i class="fas fa-cloud-upload-alt"></i>
                    </div>
                    <p>Glisser-déposer votre CV ici</p>
                    <small>Format PDF uniquement · Max 5 Mo</small>
                    <input type="file" name="cv_demande" accept=".pdf" id="cvInput"
                           onchange="showFileName(this)">
                </div>
                <div id="fileInfo" style="
                    display:none;
                    align-items:center; gap:10px; margin-top:8px;
                    padding:10px 14px;
                    background:var(--accent-light); border:1px solid var(--accent);
                    border-radius:8px; font-size:13px; color:var(--accent);
                ">
                    <i class="fas fa-file-pdf"></i>
                    <span id="fileName"></span>
                    <button type="button" onclick="clearFile()" style="
                        margin-left:auto; background:none; border:none;
                        color:var(--accent); cursor:pointer; padding:0; font-size:14px;
                    "><i class="fas fa-times"></i></button>
                </div>
            </div>
</div>

        </div>

        <div class="form-footer">
            <button type="submit" name="soumettre" class="btn-submit">
                <i class="fas fa-paper-plane"></i>
                Soumettre ma demande
            </button>
        </div>
    </form>
</div>

<script>
function showFileName(input) {
    if (input.files && input.files[0]) {
        document.getElementById('fileName').textContent = input.files[0].name;
        document.getElementById('fileInfo').style.display = 'flex';
        document.getElementById('uploadZone').style.borderColor = 'var(--accent)';
    }
}
function clearFile() {
    document.getElementById('cvInput').value = '';
    document.getElementById('fileInfo').style.display = 'none';
    document.getElementById('uploadZone').style.borderColor = '';
}
</script>