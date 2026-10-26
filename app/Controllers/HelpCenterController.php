<?php

namespace App\Controllers;

use App\Models\HelpCenter;
use Config\Database;

class HelpCenterController
{
    // Vista Pública para Colaboradores
    public function index(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['usuario'])) {
            header('Location: /yaconecta/public/login');
            exit;
        }

        $search = trim($_GET['q'] ?? '');

        // Cargar FAQs públicas y miembros de comités
        $faqsGrouped = HelpCenter::getFaqs($search);
        $copasstMembers = HelpCenter::getComiteMiembros('copasst');
        $convivenciaMembers = HelpCenter::getComiteMiembros('convivencia');

        // Cargar solicitudes privadas del usuario actual
        $userId = $_SESSION['usuario']['id'] ?? 0;
        $misSolicitudes = HelpCenter::getSolicitudesPorUsuario($userId);

        $ds = DIRECTORY_SEPARATOR;
        $vista = dirname(__DIR__, 2) . $ds . 'views' . $ds . 'centro-ayuda' . $ds . 'index.php';
        require_once $vista;
    }

    // Enviar Solicitud o Pregunta (Colaborador)
    public function enviarSolicitud(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['usuario'])) {
            header('Location: /yaconecta/public/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $comite        = trim($_POST['comite'] ?? 'general');
            $tipoSolicitud = trim($_POST['tipo_solicitud'] ?? 'Consulta');
            $asunto        = trim($_POST['asunto'] ?? '');
            $mensaje       = trim($_POST['mensaje'] ?? '');
            $esAnonimo     = isset($_POST['es_anonimo']) ? 1 : 0;

            if (empty($asunto) || empty($mensaje)) {
                header('Location: /yaconecta/public/centro-ayuda?error=campos_vacios');
                exit;
            }

            $userId = $_SESSION['usuario']['id'] ?? null;
            $nombre = $esAnonimo ? 'Anónimo' : ($_SESSION['usuario']['nombre'] ?? 'Colaborador');
            $correo = $esAnonimo ? 'anonimo@yajuego.com' : ($_SESSION['usuario']['email'] ?? '');

            // Manejo de archivo adjunto de evidencia (opcional)
            $nombreArchivo = null;
            if (!empty($_FILES['adjunto']['name'])) {
                $dirSubida = dirname(__DIR__, 2) . '/public/uploads/evidencias/';
                if (!file_exists($dirSubida)) {
                    mkdir($dirSubida, 0777, true);
                }
                $ext = pathinfo($_FILES['adjunto']['name'], PATHINFO_EXTENSION);
                $nombreArchivo = 'caso_' . time() . '_' . uniqid() . '.' . $ext;
                move_uploaded_file($_FILES['adjunto']['tmp_name'], $dirSubida . $nombreArchivo);
            }

            $db = Database::getConnection();
            $sql = "INSERT INTO comites_solicitudes 
                    (comite, user_id, nombre_remitente, correo_remitente, tipo_solicitud, asunto, mensaje, adjunto, es_anonimo) 
                    VALUES (:comite, :user_id, :nombre, :correo, :tipo, :asunto, :mensaje, :adjunto, :anonimo)";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':comite'   => $comite,
                ':user_id'  => $userId,
                ':nombre'   => $nombre,
                ':correo'   => $correo,
                ':tipo'     => $tipoSolicitud,
                ':asunto'   => $asunto,
                ':mensaje'  => $mensaje,
                ':adjunto'  => $nombreArchivo,
                ':anonimo'  => $esAnonimo
            ]);

            header('Location: /yaconecta/public/centro-ayuda?msg=solicitud_enviada');
            exit;
        }
    }
}