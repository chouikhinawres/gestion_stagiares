<?php
session_start();
require_once '../config/connection.php';

$email = $_SESSION['email'];

/* récupérer affectation active */
$stmt = $pdo->prepare("
    SELECT a.*
    FROM affectations a
    JOIN stage_requests sr ON sr.id = a.stagiaire_id
    WHERE sr.email = ?
    ORDER BY a.created_at DESC
    LIMIT 1
");
$stmt->execute([$email]);
$stage = $stmt->fetch();

$events = [];

if ($stage) {

    $start = $stage['date_debut_stage'];
    $end   = $stage['date_fin_stage'];

    /* événements automatiques */
    $events[] = [
        "title" => "🚀 Début du stage",
        "start" => $start,
        "color" => "#22c55e"
    ];

    $events[] = [
        "title" => "📅 Fin du stage",
        "start" => $end,
        "color" => "#ef4444"
    ];

    /* planning automatique (semaines) */
    $events[] = [
        "title" => "Onboarding",
        "start" => $start,
        "end"   => date('Y-m-d', strtotime($start . ' +7 days')),
        "color" => "#3b82f6"
    ];

    $events[] = [
        "title" => "Formation",
        "start" => date('Y-m-d', strtotime($start . ' +7 days')),
        "end"   => date('Y-m-d', strtotime($start . ' +14 days')),
        "color" => "#f59e0b"
    ];

    $events[] = [
        "title" => "Développement",
        "start" => date('Y-m-d', strtotime($start . ' +14 days')),
        "end"   => date('Y-m-d', strtotime($start . ' +30 days')),
        "color" => "#8b5cf6"
    ];

    $events[] = [
        "title" => "Tests & Validation",
        "start" => date('Y-m-d', strtotime($start . ' +30 days')),
        "end"   => $end,
        "color" => "#06b6d4"
    ];
}

header('Content-Type: application/json');
echo json_encode($events);


<div class="card p-3">
    <h3>📅 Planning du stage</h3>
    <div id="calendar"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'fr',
        height: 650,
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listWeek'
        },

        events: 'calendar.php',

        eventClick: function(info) {
            alert("📌 " + info.event.title);
        }
    });

    calendar.render();
});
</script>