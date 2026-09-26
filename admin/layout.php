<?php
// page dynamique
$page = $_GET['page'] ?? 'dashboard';

// sécurité (pages autorisées seulement)
$allowedPages = ['dashboard', 'encadrants', 'demandes'];
if(!in_array($page, $allowedPages)){
    $page = 'dashboard';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Panel</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
body {
    margin:0;
    font-family:Arial;
    display:flex;
    background:#f3f4f6;
}

/* SIDEBAR */
.sidebar {
    width:230px;
    height:100vh;
    background:#1f2937;
    color:white;
    position:fixed;
    top:0;
    left:0;
    padding:20px;
}

.sidebar h3 {
    text-align:center;
    margin-bottom:30px;
}

.sidebar a {
    display:flex;
    align-items:center;
    gap:10px;
    color:#cbd5e1;
    padding:10px;
    margin:6px 0;
    text-decoration:none;
    border-radius:6px;
    transition:0.3s;
}

.sidebar a:hover,
.sidebar a.active {
    background:#6366f1;
    color:white;
}

/* MAIN */
.main-content {
    margin-left:230px;
    padding:25px;
    width:100%;
}

/* CARD */
.card {
    background:white;
    padding:20px;
    border-radius:10px;
    box-shadow:0 5px 20px rgba(0,0,0,0.05);
}

/* TABLE */
table {
    width:100%;
    border-collapse:collapse;
    margin-top:15px;
}

table th {
    background:#e5e7eb;
    padding:10px;
    text-align:left;
}

table td {
    padding:10px;
    border-bottom:1px solid #eee;
}

table tr:hover {
    background:#f9fafb;
}

/* BUTTONS */
.btn {
    padding:6px 10px;
    border:none;
    border-radius:5px;
    cursor:pointer;
    text-decoration:none;
}

.btn-primary { background:#6366f1; color:white; }
.btn-danger { background:#ef4444; color:white; }
.btn-success { background:#10b981; color:white; }

</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h3>Admin</h3>

    <a href="dashboard.php">Dashboard</a>
    <a href="encadrants.php">Encadrants</a>
    <a href="demandes.php">Demandes</a>
</div>

<!-- CONTENT -->
<div class="main-content">

<?php
// affichage dynamique des pages
if($page == 'dashboard'){
    echo "<div class='card'>
            <h2>Dashboard</h2>
            <p>Bienvenue dans l'admin 👋</p>
          </div>";
}

elseif($page == 'encadrants'){
    echo "<div class='card'>
            <h2>Liste des encadrants</h2>
            <table>
                <tr><th>ID</th><th>Nom</th></tr>
                <tr><td>1</td><td>Ahmed</td></tr>
                <tr><td>2</td><td>Sami</td></tr>
            </table>
          </div>";
}

elseif($page == 'demandes'){
    echo "<div class='card'>
            <h2>Demandes de stage</h2>
            <p>Liste des demandes ici...</p>
          </div>";
}
?>

</div>

</body>
</html>