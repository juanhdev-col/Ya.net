<?php
// Carga segura de sesión para validación de acceso al panel de administración
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$roleId = $_SESSION['usuario']['role_id'] ?? 0;
$canManage = in_array((int)$roleId, [1, 2], true);

// Obtención de eventos reales desde MySQL
$eventosHome = [];
try {
    if (class_exists('\App\Models\EventoAgenda')) {
        $eventosHome = \App\Models\EventoAgenda::obtenerProximos();
    }
} catch (\Throwable $e) {
    $eventosHome = [];
}

// Mapeo de días con eventos para el mini-calendario del mes actual
$mesActualNum = (int)date('n');
$anioActualNum = (int)date('Y');
$eventosPorDia = [];

foreach ($eventosHome as $ev) {
    $time = strtotime($ev['fecha_evento']);
    if ((int)date('n', $time) === $mesActualNum && (int)date('Y', $time) === $anioActualNum) {
        $dia = (int)date('j', $time);
        $eventosPorDia[$dia] = true;
    }
}

// Variables para renderizado del mini-calendario
$primerDiaMes = mktime(0, 0, 0, $mesActualNum, 1, $anioActualNum);
$diasEnMes = (int)date('t', $primerDiaMes);
$diaSemanaInicio = (int)date('w', $primerDiaMes); // 0 (Dom) a 6 (Sáb)
$diaHoy = (int)date('j');

$mesesEs = [
    1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
    5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
    9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
];
$nombreMesTexto = ($mesesEs[$mesActualNum] ?? date('F')) . ' ' . $anioActualNum;

// Clasificación de Eventos (Hoy vs Próximos)
$fechaHoyStr = date('Y-m-d');
$eventosHoy = [];
$proximosEventos = [];

foreach ($eventosHome as $ev) {
    if ($ev['fecha_evento'] === $fechaHoyStr) {
        $eventosHoy[] = $ev;
    } else {
        $proximosEventos[] = $ev;
    }
}
?>

<section class="home-card home-agenda">
    <div class="home-card-heading">
        <h2><span>Organización</span>Agenda Corporativa</h2>
        <?php if ($canManage): ?>
            <a href="/yaconecta/public/admin/agenda">Ver calendario <span aria-hidden="true">›</span></a>
        <?php else: ?>
            <span class="home-agenda-label">Próximos eventos</span>
        <?php endif; ?>
    </div>
    <div class="home-event-list">
        <?php foreach (array_slice($eventosHome, 0, 4) as $ev): ?>
            <?php
                $fechaEvento = strtotime($ev['fecha_evento'] ?? 'now');
                $tipo = strtolower($ev['tipo'] ?? 'evento');
                $colorEvento = str_contains($tipo, 'capacit') ? 'orange' : (str_contains($tipo, 'bienestar') ? 'green' : (str_contains($tipo, 'reuni') ? 'blue' : 'purple'));
                $nombreTipo = ucfirst($tipo);
            ?>
            <article class="home-event">
                <time class="home-event-date" datetime="<?= date('Y-m-d', $fechaEvento) ?>"><strong><?= date('d', $fechaEvento) ?></strong><span><?= strtoupper(date('M', $fechaEvento)) ?></span></time>
                <div class="home-event-details">
                    <span class="home-event-type type-<?= htmlspecialchars($colorEvento) ?>"><?= htmlspecialchars($nombreTipo) ?></span>
                    <h3><?= htmlspecialchars($ev['titulo'] ?? 'Evento') ?></h3>
                    <div class="home-event-meta">
                        <span>◷ &nbsp;<?= !empty($ev['hora_evento']) ? date('g:i a', strtotime($ev['hora_evento'])) : 'Todo el día' ?></span>
                        <span>⌖ &nbsp;<?= htmlspecialchars($ev['lugar'] ?? 'Por confirmar') ?></span>
                    </div>
                </div>
                <span class="home-event-arrow" aria-hidden="true">›</span>
            </article>
        <?php endforeach; ?>
        <?php if (empty($eventosHome)): ?><p class="home-empty">Aún no hay eventos próximos en la agenda.</p><?php endif; ?>
    </div>
</section>
