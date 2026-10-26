<?php

namespace App\Controllers;

use App\Models\Idea;

class AdminIdeasController
{
    public function index()
    {
        $busqueda = trim($_GET['search'] ?? $_GET['q'] ?? '');
        $estado   = trim($_GET['estado'] ?? '');

        $ideas = Idea::obtenerTodas($busqueda, $estado);

        $ds = DIRECTORY_SEPARATOR;
        $vista = dirname(__DIR__, 2) . $ds . 'views' . $ds . 'admin' . $ds . 'ideas' . $ds . 'index.php';

        if (file_exists($vista)) {
            require_once $vista;
        } else {
            echo "Vista no encontrada en: " . $vista;
        }
    }

    public function updateStatus()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id            = $_POST['id'] ?? null;
            $estado        = trim($_POST['estado'] ?? 'Pendiente');
            $observaciones = trim($_POST['observaciones'] ?? '');

            if ($id) {
                Idea::cambiarEstado($id, $estado, $observaciones);
            }

            header("Location: /yaconecta/public/admin/ideas?msg=updated");
            exit;
        }
    }

    public function delete()
    {
        $id = $_GET['id'] ?? $_POST['id'] ?? null;
        if ($id) {
            Idea::eliminar($id);
        }
        header("Location: /yaconecta/public/admin/ideas?msg=deleted");
        exit;
    }
}