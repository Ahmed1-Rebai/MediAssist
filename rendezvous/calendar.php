<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_login();

$page_title = "Calendrier des Rendez-vous";

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container">
    <div class="page-header">
        <h1>Calendrier des Rendez-vous</h1>
        <a href="add.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Ajouter
        </a>
    </div>
    
    <div id="calendar"></div>
</div>

<!-- FullCalendar CSS -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.css' rel='stylesheet' />

<!-- FullCalendar JS -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/locales/fr.min.js'></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'fr',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: {
            url: '../api/get_appointments.php',
            method: 'GET',
            extraParams: {
                user_id: <?php echo $_SESSION['user_id']; ?>
            },
            failure: function() {
                alert('Erreur lors du chargement des rendez-vous');
            }
        },
        eventClick: function(info) {
            window.location.href = 'edit.php?id=' + info.event.id;
        },
        dateClick: function(info) {
            window.location.href = 'add.php?date=' + info.dateStr;
        }
    });
    calendar.render();
});
</script>

<?php include '../includes/footer.php'; ?>