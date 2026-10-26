<?php

namespace App\Controllers;

use Config\Database;
use PDO;

class HelpCenterAdminController
{
    private function checkAdmin(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $roleId = (int)($_SESSION['usuario']['role_id'] ?? 3);
        if (empty($_SESSION['usuario']) || $roleId > 2) {
            header('Location: /yaconecta/public/centro-ayuda?error=no_autorizado');
            exit;
        }
    }

    // Panel Administrativo
    public function index(): void
    {
        $this->checkAdmin();

        $db = Database::getConnection();
        $faqs = $db->query("SELECT * FROM faq ORDER BY categoria ASC, orden ASC, id DESC")->fetchAll(PDO::FETCH_ASSOC) ?: [];
        $solicitudes = $db->query("SELECT * FROM comites_solicitudes ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $ds = DIRECTORY_SEPARATOR;
        // Carga la vista de administración en views/admin/centro-ayuda/index.php
        $vista = dirname(__DIR__, 2) . $ds . 'views' . $ds . 'admin' . $ds . 'centro-ayuda' . $ds . 'index.php';
        require_once $vista;
    }

    // Guardar/Editar FAQ
    public function saveFaq(): void
    {
        $this->checkAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id        = (int)($_POST['id'] ?? 0);
            $categoria = trim($_POST['categoria'] ?? 'General');
            $pregunta  = trim($_POST['pregunta'] ?? '');
            $respuesta = trim($_POST['respuesta'] ?? '');
            $estado    = $_POST['estado'] ?? 'activo';

            if (!empty($pregunta) && !empty($respuesta)) {
                $db = Database::getConnection();
                if ($id > 0) {
                    $sql = "UPDATE faq SET categoria = :c, pregunta = :p, respuesta = :r, estado = :e WHERE id = :id";
                    $stmt = $db->prepare($sql);
                    $stmt->execute([':c' => $categoria, ':p' => $pregunta, ':r' => $respuesta, ':e' => $estado, ':id' => $id]);
                } else {
                    $sql = "INSERT INTO faq (categoria, pregunta, respuesta, estado) VALUES (:c, :p, :r, :e)";
                    $stmt = $db->prepare($sql);
                    $stmt->execute([':c' => $categoria, ':p' => $pregunta, ':r' => $respuesta, ':e' => $estado]);
                }
            }
            header('Location: /yaconecta/public/admin/centro-ayuda?msg=faq_guardada');
            exit;
        }
    }

    // Responder Solicitud
    public function responderSolicitud(): void
    {
        $this->checkAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id          = (int)($_POST['id'] ?? 0);
            $respuesta   = trim($_POST['respuesta_admin'] ?? '');
            $estado      = $_POST['estado'] ?? 'resuelto';
            $publicarFaq = isset($_POST['publicar_faq']) ? 1 : 0;
            $categoria   = trim($_POST['categoria_faq'] ?? 'General');

            if ($id > 0 && !empty($respuesta)) {
                $db = Database::getConnection();
                $stmt = $db->prepare("UPDATE comites_solicitudes SET respuesta_admin = :r, estado = :e WHERE id = :id");
                $stmt->execute([':r' => $respuesta, ':e' => $estado, ':id' => $id]);

                if ($publicarFaq) {
                    $sol = $db->query("SELECT * FROM comites_solicitudes WHERE id = {$id}")->fetch(PDO::FETCH_ASSOC);
                    if ($sol) {
                        $stmtFaq = $db->prepare("INSERT INTO faq (categoria, pregunta, respuesta, estado) VALUES (:c, :p, :r, 'activo')");
                        $stmtFaq->execute([':c' => $categoria, ':p' => $sol['asunto'], ':r' => $respuesta]);
                    }
                }
            }
            header('Location: /yaconecta/public/admin/centro-ayuda?msg=respuesta_guardada');
            exit;
        }
    }

    // Eliminar FAQ
    public function deleteFaq(): void
    {
        $this->checkAdmin();

        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            $db = Database::getConnection();
            $stmt = $db->prepare("DELETE FROM faq WHERE id = :id");
            $stmt->execute([':id' => $id]);
        }
        header('Location: /yaconecta/public/admin/centro-ayuda?msg=faq_eliminada');
        exit;
    }
}