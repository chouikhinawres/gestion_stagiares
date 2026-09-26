// admin/demandes.php
<?php
require_once "../config/connection.php";

/* ACTION */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id = $_POST["id"];
    $action = $_POST["action"];

    if($action=="accepter"){
        $pdo->prepare("UPDATE stage_requests SET statut='accepté' WHERE id=?")->execute([$id]);
    }
    elseif($action=="rejeter"){
        $pdo->prepare("UPDATE stage_requests SET statut='rejeté' WHERE id=?")->execute([$id]);
    }
    elseif($action=="affecter"){
        $pdo->prepare("
            INSERT INTO affectations (stagiaire_id, nom_projet, nom_encadrant, service_departement)
            VALUES (?,?,?,?)
        ")->execute([
            $id,
            $_POST["nom_projet"],
            $_POST["nom_encadrant"],
            $_POST["service_departement"]
        ]);

        $pdo->prepare("UPDATE stage_requests SET statut='affecté' WHERE id=?")->execute([$id]);
    }
    elseif($action=="update_affectation"){
        $pdo->prepare("
            UPDATE affectations 
            SET nom_encadrant=?, service_departement=? 
            WHERE stagiaire_id=?
        ")->execute([
            $_POST["nom_encadrant"],
            $_POST["service_departement"],
            $id
        ]);
    }

    echo json_encode(['success'=>true]);
    exit;
}

/* 🔥 JOIN encadrant */
$demandes = $pdo->query("
    SELECT sr.*, a.nom_encadrant 
    FROM stage_requests sr
    LEFT JOIN affectations a ON sr.id = a.stagiaire_id
")->fetchAll(PDO::FETCH_ASSOC);

$encadrants = $pdo->query("SELECT nom,prenom FROM encadrants")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card">
    <div class="table-wrapper">
        <table class="table">
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Statut / Action</th>
            </tr>
            <?php foreach($demandes as $d): ?>
                <tr>
                    <td><?= $d['id'] ?></td>
                    <td><?= htmlspecialchars($d['nom']." ".$d['prenom']) ?></td>
                    <td><?= htmlspecialchars($d['email']) ?></td>
                    <td>
                        <?php if($d['statut']=="en attente"): ?>
                            <span class="status status-pending">En attente</span>
                            <button class="btn-success" onclick="updateStatus(<?= $d['id'] ?>,'accepter')">✔</button>
                            <button class="btn-danger" onclick="updateStatus(<?= $d['id'] ?>,'rejeter')">✖</button>
                            <?php elseif($d['statut']=="accepté"): ?>
                                <span class="status status-accepted">Accepté</span>
                                <button class="btn-primary" onclick="openModal(<?= $d['id'] ?>,'<?= addslashes($d['message']) ?>')">Affecter</button>
                                <?php elseif($d['statut']=="affecté"): ?>
                                    <span class="status status-assigned">Affecté</span>
                                    <a href="#" class="badge-encadrant" onclick="editAffectation(<?= $d['id'] ?>,'<?= addslashes($d['nom_encadrant']) ?>','<?= addslashes($d['message']) ?>')">👤 <?= htmlspecialchars($d['nom_encadrant']) ?></a>
                                    <?php elseif($d['statut']=="rejeté"): ?>
                                        <span class="status status-rejected">Rejeté</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </table>
                        </div>
                    </div>
                    <!-- MODAL -->
                     <div id="modal" class="modal">
                        <div class="modal-content">
                            <h3>Affectation / Changement</h3>
                            <form id="form">
                                <input type="hidden" name="id" id="id">
                                <input type="hidden" name="action" id="action" value="affecter">
                                <input type="hidden" name="nom_projet" id="projet">
                                <label>Encadrant</label>
                                <select name="nom_encadrant" id="encadrant">
                                    <?php foreach($encadrants as $e): ?>
                                        <option value="<?= $e['nom']." ".$e['prenom'] ?>"><?= $e['nom']." ".$e['prenom'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label>Service / Département</label>
                                    <input name="service_departement" id="service" placeholder="Service">
                                    <div class="modal-buttons">
                                        <button type="button" class="btn-save" onclick="save()">Save</button>
                                        <button type="button" class="btn-cancel" onclick="closeModal()">Cancel</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- CSS -->
<style>
/* Card & Table */
.card{padding:20px;background:#fff;border-radius:10px;box-shadow:0 5px 20px rgba(0,0,0,0.05);margin-bottom:20px;}
.table-wrapper{overflow-x:auto;}
.table{width:100%;border-collapse:collapse;min-width:600px;}
.table th, .table td{padding:12px;text-align:left;border-bottom:1px solid #eee;}
.status{padding:4px 10px;border-radius:8px;color:white;font-size:13px;font-weight:500;}
.status-pending{background:#f59e0b;}
.status-accepted{background:#3b82f6;}
.status-assigned{background:#10b981;}
.status-rejected{background:#ef4444;}

/* Buttons */
button{border:none;border-radius:6px;padding:5px 10px;margin:2px;cursor:pointer;font-weight:500;}
.btn-success{background:#10b981;color:white;}
.btn-danger{background:#ef4444;color:white;}
.btn-primary{background:#3b82f6;color:white;}
.badge-encadrant{background:#10b981;color:white;padding:5px 12px;border-radius:20px;text-decoration:none;font-weight:500;cursor:pointer;display:inline-block;}

/* Modal */
.modal{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);align-items:center;justify-content:center;padding:10px;z-index:999;}
.modal-content{background:white;padding:20px;border-radius:10px;width:100%;max-width:400px;animation:fadeIn 0.3s ease;}
.modal-buttons{margin-top:15px;display:flex;justify-content:flex-end;gap:10px;}
.btn-save{background:#10b981;color:white;padding:6px 12px;border-radius:6px;}
.btn-cancel{background:#ef4444;color:white;padding:6px 12px;border-radius:6px;}

/* Responsive */
@media (max-width:600px){
    .table th, .table td{padding:8px;font-size:14px;}
    .badge-encadrant{padding:4px 8px;font-size:12px;}
}

/* Animation */
@keyframes fadeIn{from{opacity:0;transform:translateY(-20px);}to{opacity:1;transform:translateY(0);}}
</style>

<!-- JS -->
<script>
function updateStatus(id,action){
    fetch('demandes.php',{
        method:'POST',
        body:new URLSearchParams({id,action})
    }).then(()=>location.reload());
}

function openModal(id,projet){
    document.getElementById('modal').style.display='flex';
    document.getElementById('id').value=id;
    document.getElementById('projet').value=projet;
    document.getElementById('action').value="affecter";
}

function editAffectation(id, encadrant, projet){
    document.getElementById('modal').style.display='flex';
    document.getElementById('id').value=id;
    document.getElementById('projet').value=projet;
    document.getElementById('encadrant').value = encadrant;
    document.getElementById('action').value="update_affectation";
}

function save(){
    fetch('demandes.php',{
        method:'POST',
        body:new FormData(document.getElementById('form'))
    }).then(()=>location.reload());
}

function closeModal(){
    document.getElementById('modal').style.display='none';
}
</script>