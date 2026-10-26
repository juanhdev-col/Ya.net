<?php

namespace App\Controllers;

use App\Models\EventoAgenda;

class AgendaController
{
    private bool $previewMode = false;
    /**
     * Verifica que el usuario logueado tenga rol de Admin General (1) o Admin Contenido (2)
     */
    private function verificarPermiso(bool $allowLocalPreview = false): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Obtener el role_id desde la sesión $_SESSION['usuario']['role_id']
        $roleId = isset($_SESSION['usuario']['role_id']) ? (int) $_SESSION['usuario']['role_id'] : 0;

        // Permitir acceso ÚNICAMENTE a Admin General (1) y Admin Contenido (2)
        if (!in_array($roleId, [1, 2], true)) {
            $host = strtolower((string)($_SERVER['HTTP_HOST'] ?? ''));
            $host = preg_replace('/:\\d+$/', '', $host);
            if ($allowLocalPreview && in_array($host, ['localhost', '127.0.0.1', '::1'], true)) {
                $this->previewMode = true;
                return;
            }
            header('HTTP/1.1 403 Forbidden');
            echo "<div style='font-family:sans-serif; text-align:center; padding:50px;'>
                    <h2 style='color:#0A2540;'>Acceso Restringido</h2>
                    <p style='color:#666;'>Solo los usuarios con rol de <b>Administrador General</b> o <b>Administrador de Contenido</b> pueden gestionar el Calendario Corporativo.</p>
                    <a href='/yaconecta/public/' style='color:#FF6B00; font-weight:bold;'>← Volver al Inicio</a>
                  </div>";
            exit;
        }
    }

    // Vista Panel Admin de la Agenda
    public function admin(): void
    {
        $this->verificarPermiso(true);

        $todosLosEventos = EventoAgenda::obtenerTodos();
        $busqueda = trim($_GET['search'] ?? $_GET['q'] ?? '');
        $tipoFiltro = trim($_GET['tipo'] ?? '');
        $estadoFiltro = trim($_GET['estado'] ?? '');
        $eventos = array_values(array_filter($todosLosEventos, static function (array $evento) use ($busqueda, $tipoFiltro, $estadoFiltro): bool {
            $status = ($evento['fecha_evento'] ?? '') < date('Y-m-d') ? 'realizado' : 'programado';
            $text = implode(' ', [$evento['titulo'] ?? '', $evento['descripcion'] ?? '', $evento['lugar'] ?? '', $evento['tipo'] ?? '']);
            return ($busqueda === '' || stripos($text, $busqueda) !== false)
                && ($tipoFiltro === '' || ($evento['tipo'] ?? '') === $tipoFiltro)
                && ($estadoFiltro === '' || $status === $estadoFiltro);
        }));
        $tiposEvento = array_values(array_unique(array_filter(array_column($todosLosEventos, 'tipo'))));
        sort($tiposEvento, SORT_NATURAL | SORT_FLAG_CASE);
        $hoy = date('Y-m-d');
        $limiteProximos = date('Y-m-d', strtotime('+30 days'));
        $totalEventos = count($todosLosEventos);
        $totalProximos = count(array_filter($todosLosEventos, static fn(array $e): bool => ($e['fecha_evento'] ?? '') >= $hoy && ($e['fecha_evento'] ?? '') <= $limiteProximos));
        $totalRealizados = count(array_filter($todosLosEventos, static fn(array $e): bool => ($e['fecha_evento'] ?? '') < $hoy));
        $proximosEventos = array_values(array_filter($todosLosEventos, static fn(array $e): bool => ($e['fecha_evento'] ?? '') >= $hoy));
        usort($proximosEventos, static fn(array $a, array $b): int => [$a['fecha_evento'] ?? '', $a['hora_evento'] ?? ''] <=> [$b['fecha_evento'] ?? '', $b['hora_evento'] ?? '']);
        $proximosEventos = array_slice($proximosEventos, 0, 3);
        $pageTitle = 'Ya.Net | Administración de Eventos';
        $adminPreview = $this->previewMode;

        $ds = DIRECTORY_SEPARATOR;
        $vista = dirname(__DIR__, 2) . $ds . 'views' . $ds . 'admin' . $ds . 'agenda' . $ds . 'index.php';

        if (file_exists($vista)) {
            require_once $vista;
        } else {
            echo "Vista no encontrada en: " . $vista;
        }
    }

    // Crear evento (Cumpleaños, Capacitación, Reunión, etc.)
    public function store(): void
    {
        $this->verificarPermiso();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && class_exists('App\Models\EventoAgenda')) {
            try {
                $data = [
                    'titulo'       => $_POST['titulo'] ?? '',
                    'descripcion'  => $_POST['descripcion'] ?? '',
                    'tipo'         => $_POST['tipo'] ?? 'evento',
                    'fecha_evento' => $_POST['fecha_evento'] ?? date('Y-m-d'),
                    'hora_evento'  => $_POST['hora_evento'] ?? null,
                    'lugar'        => $_POST['lugar'] ?? ''
                ];

                if (!empty($data['titulo']) && !empty($data['fecha_evento'])) {
                    EventoAgenda::crear($data);
                }
            } catch (\Throwable $e) {
                error_log("Error al crear evento en agenda: " . $e->getMessage());
            }
        }

        header('Location: /yaconecta/public/admin/agenda?status=created');
        exit;
    }

    public function update(): void
    {
        $this->verificarPermiso();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit; }
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $data = [
            'titulo' => trim($_POST['titulo'] ?? ''),
            'descripcion' => trim($_POST['descripcion'] ?? ''),
            'tipo' => trim($_POST['tipo'] ?? 'evento'),
            'fecha_evento' => trim($_POST['fecha_evento'] ?? ''),
            'hora_evento' => trim($_POST['hora_evento'] ?? ''),
            'lugar' => trim($_POST['lugar'] ?? '')
        ];
        if (!$id || $data['titulo'] === '' || $data['fecha_evento'] === '') {
            header('Location: /yaconecta/public/admin/agenda?status=error'); exit;
        }
        EventoAgenda::actualizar((int)$id, $data);
        header('Location: /yaconecta/public/admin/agenda?status=updated'); exit;
    }

    // Eliminar evento
    public function delete(): void
    {
        $this->verificarPermiso();

        $id = $_GET['id'] ?? null;
        if ($id && class_exists('App\Models\EventoAgenda')) {
            EventoAgenda::eliminar((int)$id);
        }

        header('Location: /yaconecta/public/admin/agenda?status=deleted');
        exit;
    }
}
