<?php require_once 'header.php'; ?>
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales-all.min.js'></script>
<style>
    .fc a { color: inherit !important; text-decoration: none !important; }
    .fc-col-header-cell { background-color: #ffc107 !important; border: 1px solid #004400 !important; border-radius: 6px !important; }
    .fc-col-header-cell-cushion { color: #000000 !important; font-weight: bold !important; text-transform: uppercase !important; display: block !important; padding: 6px !important; }
    .fc-icon { color: #FFD700 !important; }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm border-success">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Calendário de Consultas</h5>
            </div>
            <div class="card-body bg-white">
                <div id='calendar'></div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',
        locale: 'pt-br',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'timeGridWeek,timeGridDay'
        },
        buttonText: {
            today: 'Hoje',
            week: 'Semana',
            day: 'Dia'
        },
        slotMinTime: '08:00:00',
        slotMaxTime: '19:00:00',
        slotDuration: '00:30:00',
        slotLabelInterval: '00:30',
        slotLabelFormat: {
            hour: 'numeric',
            minute: '2-digit',
            omitZeroMinute: false,
            meridiem: 'short'
        },
        contentHeight: 'auto',
        allDaySlot: false,
        hiddenDays: [0],
        dayHeaderContent: function(arg) {
            let labelSemana = arg.date.toLocaleDateString('pt-BR', { weekday: 'short' }).replace('.', '').toUpperCase();
            let labelData = arg.date.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit' });
            return labelSemana + ' - ' + labelData;
        },
        events: '../controller/consultaController.php?action=gerarJsonCalendario'
    });
    calendar.render();
});
</script>
<?php require_once 'footer.php'; ?>