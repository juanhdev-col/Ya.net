<?php

namespace App\Controllers;

use Config\Database;

class PopupController
{
    // API pública para obtener el Pop-up activo dentro de su rango de vigencia
    public function getActivo(): void
    {
        header('Content-Type: application/json');
        
        try {
            $db = Database::getConnection();
            $now = date('Y-m-d H:i:s');

            $stmt = $db->prepare("SELECT * FROM popups 
                                  WHERE estado = 'activo' 
                                  AND (fecha_inicio IS NULL OR fecha_inicio <= :now1) 
                                  AND (fecha_fin IS NULL OR fecha_fin >= :now2) 
                                  ORDER BY id DESC LIMIT 1");
            $stmt->execute([':now1' => $now, ':now2' => $now]);
            $popup = $stmt->fetch(\PDO::FETCH_ASSOC);

            echo json_encode(['success' => true, 'popup' => $popup ?: null]);
        } catch (\Throwable $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }

    // Vista del Panel Administrador
    public function indexAdmin(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $usuario = $_SESSION['usuario'] ?? null;
        if (!$usuario || !in_array((int)($usuario['role_id'] ?? 0), [1, 2])) {
            header('Location: /yaconecta/public/');
            exit;
        }

        $db = Database::getConnection();
        $popups = $db->query("SELECT * FROM popups ORDER BY id DESC")->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        $ds = DIRECTORY_SEPARATOR;
        $vista = dirname(__DIR__, 2) . $ds . 'views' . $ds . 'admin' . $ds . 'popups.php';
        require_once $vista;
    }

    // Guardar nuevo Pop-up con vigencia por fechas
    public function guardar(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titulo = trim($_POST['titulo'] ?? '');
            $tipo = trim($_POST['tipo'] ?? 'Comunicaciones Importantes');
            $contenido = trim($_POST['contenido'] ?? '');
            $link = trim($_POST['link_destino'] ?? '');
            $textoBoton = trim($_POST['texto_boton'] ?? 'Entendido');
            $estado = $_POST['estado'] ?? 'activo';
            
            $fechaInicio = !empty($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : null;
            $fechaFin = !empty($_POST['fecha_fin']) ? $_POST['fecha_fin'] : null;

            $imagenPath = null;
            if (!empty($_FILES['imagen']['name'])) {
                $dirUpload = dirname(__DIR__, 2) . '/public/uploads/popups/';
                if (!is_dir($dirUpload)) {
                    mkdir($dirUpload, 0777, true);
                }
                $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
                $nombreFoto = 'popup_' . time() . '.' . $ext;
                if (move_uploaded_file($_FILES['imagen']['tmp_name'], $dirUpload . $nombreFoto)) {
                    $imagenPath = '/yaconecta/public/uploads/popups/' . $nombreFoto;
                }
            }

            $db = Database::getConnection();
            $stmt = $db->prepare("INSERT INTO popups (titulo, tipo, contenido, imagen_path, link_destino, texto_boton, estado, fecha_inicio, fecha_fin) 
                                  VALUES (:t, :tipo, :c, :img, :l, :b, :e, :fi, :ff)");
            $stmt->execute([
                ':t'          => $titulo,
                ':tipo'       => $tipo,
                ':c'          => $contenido,
                ':img'        => $imagenPath,
                ':l'          => $link,
                ':b'          => $textoBoton,
                ':e'          => $estado,
                ':fi'         => $fechaInicio,
                ':ff'         => $fechaFin
            ]);

            header('Location: /yaconecta/public/admin/popups?msg=guardado');
            exit;
        }
    }

    // Cambiar Estado o Eliminar
    public function cambiarEstado(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $accion = $_GET['accion'] ?? '';

        $db = Database::getConnection();
        if ($accion === 'eliminar') {
            $stmt = $db->prepare("DELETE FROM popups WHERE id = :id");
            $stmt->execute([':id' => $id]);
        } elseif ($accion === 'toggle') {
            $stmt = $db->prepare("UPDATE popups SET estado = IF(estado='activo', 'inactivo', 'activo') WHERE id = :id");
            $stmt->execute([':id' => $id]);
        }

        header('Location: /yaconecta/public/admin/popups');
        exit;
    }
}