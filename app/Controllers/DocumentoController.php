<?php

namespace App\Controllers;

use App\Models\Documento;

class DocumentoController
{
    public function index(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $search = trim($_GET['search'] ?? $_GET['q'] ?? '');
        $categoria = trim($_GET['cat'] ?? '');
        $area = trim($_GET['area'] ?? '');
        $orden = ($_GET['orden'] ?? 'recentes') === 'alfabetico' ? 'alfabetico' : 'recentes';
        $documentos = Documento::obtenerTodos($search, $categoria, $area, $orden);
        $resumen = Documento::obtenerResumen();
        $departamentos = Documento::obtenerDepartamentos();
        $documentosDestacados = array_slice(array_values(array_filter($documentos, static fn($doc) => strtolower(pathinfo($doc['file_path'], PATHINFO_EXTENSION)) === 'pdf')), 0, 4);
        $usuarioAutenticado = !empty($_SESSION['usuario']);
        $pageTitle = 'Ya.Net | Gestión Documental SGC';
        require dirname(__DIR__, 2) . '/views/documentos/index.php';
    }

    public function archivo(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (empty($_SESSION['usuario'])) {
            header('Location: /yaconecta/public/login?error=sesion_requerida');
            exit;
        }
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $doc = $id ? Documento::obtenerPorId((int)$id) : null;
        if (!$doc || ($doc['estado'] === 'archived' && (int)($_SESSION['usuario']['role_id'] ?? 3) > 2)) {
            http_response_code(404);
            exit('Documento no encontrado.');
        }
        $filename = basename((string)$doc['file_path']);
        $path = realpath(dirname(__DIR__, 2) . '/storage/documentos/' . $filename);
        $base = realpath(dirname(__DIR__, 2) . '/storage/documentos');
        if (!$path || !$base || strpos($path, $base . DIRECTORY_SEPARATOR) !== 0 || !is_file($path)) {
            http_response_code(404);
            exit('Archivo no encontrado.');
        }
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($path) ?: 'application/octet-stream';
        $previewRequested = ($_GET['modo'] ?? '') === 'ver';
        if ($previewRequested && $mime !== 'application/pdf') {
            http_response_code(415);
            exit('Este formato no admite vista previa en el navegador.');
        }
        if (!$previewRequested && empty($doc['is_downloadable']) && $doc['estado'] !== 'archived') {
            http_response_code(403);
            exit('Este documento está disponible solo para consulta en línea.');
        }
        $disposition = $previewRequested ? 'inline' : 'attachment';
        header('Content-Type: ' . $mime);
        header('Content-Length: ' . filesize($path));
        header('Content-Disposition: ' . $disposition . '; filename="' . rawurlencode($filename) . '"');
        header('X-Content-Type-Options: nosniff');
        header('Cache-Control: private, no-store, max-age=0');
        readfile($path);
        exit;
    }
}
