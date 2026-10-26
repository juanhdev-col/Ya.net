<?php

namespace App\Controllers;

use App\Models\Documento;

class AdminDocumentController
{
    private bool $previewMode = false;

    private function isLocalRequest(): bool
    {
        $host = strtolower((string)($_SERVER['HTTP_HOST'] ?? ''));
        $host = preg_replace('/:\\d+$/', '', $host);
        return in_array($host, ['localhost', '127.0.0.1', '::1'], true);
    }

    private function checkAdmin(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $roleId = (int)($_SESSION['usuario']['role_id'] ?? 3);
        if (empty($_SESSION['usuario']) || $roleId > 2) {
            if ($this->isLocalRequest()) {
                $this->previewMode = true;
                return;
            }
            header('Location: /yaconecta/public/documentos?error=no_autorizado');
            exit;
        }
    }

    private function checkCsrf(): void
    {
        $token = (string)($_POST['csrf_token'] ?? '');
        if (empty($_SESSION['csrf_documentos']) || !hash_equals($_SESSION['csrf_documentos'], $token)) {
            http_response_code(419);
            exit('La sesión del formulario venció. Recarga la página e inténtalo de nuevo.');
        }
    }

    private function guardarArchivo(): ?string
    {
        if (empty($_FILES['archivo']) || $_FILES['archivo']['error'] === UPLOAD_ERR_NO_FILE) return null;
        $file = $_FILES['archivo'];
        if ($file['error'] !== UPLOAD_ERR_OK || $file['size'] > 20 * 1024 * 1024) return null;
        $allowed = [
            'pdf' => ['application/pdf'],
            'doc' => ['application/msword', 'application/x-ole-storage'],
            'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip'],
            'xls' => ['application/vnd.ms-excel', 'application/x-ole-storage'],
            'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/zip']
        ];
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        if (!isset($allowed[$extension]) || !in_array($mime, $allowed[$extension], true)) return null;
        $uploadDir = dirname(__DIR__, 2) . '/storage/documentos/';
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0750, true) && !is_dir($uploadDir)) return null;
        $fileName = bin2hex(random_bytes(18)) . '.' . $extension;
        if (!move_uploaded_file($file['tmp_name'], $uploadDir . $fileName)) return null;
        return $fileName;
    }

    public function index(): void
    {
        $this->checkAdmin();
        if (!$this->previewMode) $_SESSION['csrf_documentos'] = bin2hex(random_bytes(32));
        $busqueda = trim($_GET['search'] ?? $_GET['q'] ?? '');
        $categoria = trim($_GET['cat'] ?? '');
        $area = trim($_GET['area'] ?? '');
        $tipo = trim($_GET['type'] ?? '');
        $documentos = Documento::obtenerTodos($busqueda, $categoria, $area);
        if ($tipo !== '') {
            $documentos = array_values(array_filter($documentos, static fn(array $doc): bool => $doc['doc_type'] === $tipo));
        }
        $documentosObsoletos = Documento::obtenerObsoletos();
        $departamentos = Documento::obtenerDepartamentos();
        $resumenCategorias = Documento::obtenerResumen();
        $totalActivos = count(Documento::obtenerTodos());
        $totalArchivados = count($documentosObsoletos);
        $tiposDocumento = array_values(array_unique(array_column(Documento::obtenerTodos(), 'doc_type')));
        $csrfToken = $_SESSION['csrf_documentos'] ?? '';
        $adminPreview = $this->previewMode;
        $pageTitle = 'Ya.Net | Administración Documental';
        require dirname(__DIR__, 2) . '/views/admin/documentos/index.php';
    }

    public function store(): void
    {
        $this->checkAdmin();
        if ($this->previewMode) { http_response_code(403); exit('La vista previa no permite guardar documentos. Inicia sesión como administrador.'); }
        $this->checkCsrf();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit; }
        $filePath = $this->guardarArchivo();
        $title = trim($_POST['title'] ?? '');
        $docType = $_POST['doc_type'] ?? '';
        $validTypes = ['politica','manual','procedimiento','instructivo','protocolo','guia','matriz','informe','formato'];
        if ($filePath === null || $title === '' || !in_array($docType, $validTypes, true)) {
            header('Location: /yaconecta/public/admin/documentos?error=validacion'); exit;
        }
        Documento::crear([
            'code' => trim($_POST['code'] ?? ''), 'title' => $title, 'doc_type' => $docType,
            'department_id' => (int)($_POST['department_id'] ?? 0), 'file_path' => $filePath,
            'version' => $_POST['version'] ?? 1, 'is_downloadable' => isset($_POST['is_downloadable']),
            'valid_from' => $_POST['valid_from'] ?? null, 'created_by' => (int)($_SESSION['usuario']['id'] ?? 0)
        ]);
        header('Location: /yaconecta/public/admin/documentos?msg=created'); exit;
    }

    public function update(): void
    {
        $this->checkAdmin();
        if ($this->previewMode) { http_response_code(403); exit('La vista previa no permite editar documentos. Inicia sesión como administrador.'); }
        $this->checkCsrf();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit; }
        $filePath = $this->guardarArchivo();
        if (!empty($_FILES['archivo']['name']) && $filePath === null) {
            header('Location: /yaconecta/public/admin/documentos?error=archivo'); exit;
        }
        $docType = $_POST['doc_type'] ?? '';
        $validTypes = ['politica','manual','procedimiento','instructivo','protocolo','guia','matriz','informe','formato'];
        if (empty($_POST['id']) || trim($_POST['title'] ?? '') === '' || !in_array($docType, $validTypes, true)) {
            header('Location: /yaconecta/public/admin/documentos?error=validacion'); exit;
        }
        Documento::actualizar([
            'id' => (int)$_POST['id'], 'code' => trim($_POST['code'] ?? ''), 'title' => trim($_POST['title']),
            'doc_type' => $docType, 'department_id' => (int)($_POST['department_id'] ?? 0),
            'version' => $_POST['version'] ?? 1, 'is_downloadable' => isset($_POST['is_downloadable']),
            'valid_from' => $_POST['valid_from'] ?? null, 'file_path' => $filePath
        ]);
        header('Location: /yaconecta/public/admin/documentos?msg=updated'); exit;
    }

    public function archive(): void
    {
        $this->checkAdmin();
        if ($this->previewMode) { http_response_code(403); exit('La vista previa no permite archivar documentos. Inicia sesión como administrador.'); }
        $this->checkCsrf();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit; }
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $reason = trim($_POST['archive_reason'] ?? 'Retirado por la administración');
        if ($id) Documento::archivar((int)$id, (int)($_SESSION['usuario']['id'] ?? 0), $reason);
        header('Location: /yaconecta/public/admin/documentos?msg=archived'); exit;
    }
}
