<?php

namespace App\Controllers;

use App\Models\CompanyInfo;

class AdminCompanyController
{
    private bool $galleryPreview = false;

    private function checkAdmin(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $roleId = (int)($_SESSION['usuario']['role_id'] ?? 3);
        if (empty($_SESSION['usuario']) || $roleId > 2) {
            header('Location: /yaconecta/public/quienes-somos?error=no_autorizado');
            exit;
        }
    }

    public function index()
    {
        $this->checkAdmin();

        $sections = CompanyInfo::getSections();
        $media    = CompanyInfo::getMedia();
        $valores  = CompanyInfo::getValues();

        $ds = DIRECTORY_SEPARATOR;
        $vista = dirname(__DIR__, 2) . $ds . 'views' . $ds . 'admin' . $ds . 'quienes-somos' . $ds . 'index.php';

        if (file_exists($vista)) {
            require_once $vista;
        } else {
            echo "Vista de gestión no encontrada en: " . $vista;
        }
    }

    /** Panel de galería. En localhost permite vista previa de solo lectura. */
    public function gallery()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $roleId = (int)($_SESSION['usuario']['role_id'] ?? 0);
        if (!in_array($roleId, [1, 2], true)) {
            $host = strtolower((string)($_SERVER['HTTP_HOST'] ?? ''));
            $host = preg_replace('/:\\d+$/', '', $host);
            if (in_array($host, ['localhost', '127.0.0.1', '::1'], true)) {
                $this->galleryPreview = true;
            } else {
                http_response_code(403);
                echo 'Acceso restringido. Inicia sesión como administrador.';
                return;
            }
        }

        $media = CompanyInfo::getMedia();
        $search = trim($_GET['search'] ?? '');
        $typeFilter = trim($_GET['tipo'] ?? '');
        $media = array_values(array_filter($media, static function (array $item) use ($search, $typeFilter): bool {
            $type = strtolower((string)($item['type'] ?? 'image'));
            $text = implode(' ', [(string)($item['caption'] ?? ''), basename((string)($item['file_path'] ?? ''))]);
            return ($search === '' || stripos($text, $search) !== false)
                && ($typeFilter === '' || $type === strtolower($typeFilter));
        }));
        $allMedia = CompanyInfo::getMedia();
        $totalImages = count(array_filter($allMedia, static fn(array $item): bool => strtolower((string)($item['type'] ?? 'image')) !== 'video'));
        $totalVideos = count($allMedia) - $totalImages;
        $totalBytes = 0;
        foreach ($allMedia as $item) {
            $path = dirname(__DIR__, 2) . '/public' . (string)($item['file_path'] ?? '');
            if (is_file($path)) $totalBytes += (int)filesize($path);
        }
        $galleryPreview = $this->galleryPreview;
        $pageTitle = 'Ya.Net | Administración de Galería Corporativa';
        require dirname(__DIR__, 2) . '/views/admin/galeria/index.php';
    }

    // Guardar Cambios de Secciones (Historia, Misión, Visión, Cultura, Mapa, Organigrama)
    public function updateSection()
    {
        $this->checkAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $key      = trim($_POST['section_key'] ?? '');
            $title    = trim($_POST['title'] ?? '');
            $subtitle = trim($_POST['subtitle'] ?? '');
            $content  = trim($_POST['content'] ?? '');

            $mediaPath = null;

            if (isset($_FILES['media']) && $_FILES['media']['error'] === UPLOAD_ERR_OK) {
                $fileTmp   = $_FILES['media']['tmp_name'];
                $fileName  = time() . '_' . basename($_FILES['media']['name']);
                $uploadDir = dirname(__DIR__, 2) . '/public/uploads/quienes_somos/';

                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                if (move_uploaded_file($fileTmp, $uploadDir . $fileName)) {
                    $mediaPath = '/uploads/quienes_somos/' . $fileName;
                }
            }

            if (!empty($key)) {
                CompanyInfo::updateSection($key, $title, $subtitle, $content, $mediaPath);
            }

            header("Location: /yaconecta/public/admin/quienes-somos?msg=section_updated");
            exit;
        }
    }

    // Subir elemento multimedia a la historia (Imagen o Video)
    public function addMedia()
    {
        $this->checkAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $type    = trim($_POST['type'] ?? 'image');
            $caption = trim($_POST['caption'] ?? '');
            $filePath = '';

            // Si es archivo directo
            if (isset($_FILES['media_file']) && $_FILES['media_file']['error'] === UPLOAD_ERR_OK) {
                $fileTmp   = $_FILES['media_file']['tmp_name'];
                $fileName  = time() . '_' . basename($_FILES['media_file']['name']);
                $uploadDir = dirname(__DIR__, 2) . '/public/uploads/quienes_somos/galeria/';

                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                if (move_uploaded_file($fileTmp, $uploadDir . $fileName)) {
                    $filePath = '/uploads/quienes_somos/galeria/' . $fileName;
                }
            } elseif (!empty($_POST['video_url'])) { // URL de YouTube o MP4
                $filePath = trim($_POST['video_url']);
                $type = 'video';
            }

            if (!empty($filePath)) {
                CompanyInfo::addMedia($type, $filePath, $caption);
            }

            header("Location: /yaconecta/public/admin/quienes-somos?msg=media_added");
            exit;
        }
    }

    public function deleteMedia()
    {
        $this->checkAdmin();
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            CompanyInfo::deleteMedia($id);
        }
        header("Location: /yaconecta/public/admin/quienes-somos?msg=media_deleted");
        exit;
    }

    // Guardar / Editar Valor Corporativo
    public function saveValue()
    {
        $this->checkAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id          = $_POST['id'] ?? null;
            $icon        = trim($_POST['icon'] ?? '💎');
            $title       = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');

            if (!empty($title)) {
                CompanyInfo::saveValue([
                    'id'          => $id,
                    'icon'        => $icon,
                    'title'       => $title,
                    'description' => $description
                ]);
            }

            header("Location: /yaconecta/public/admin/quienes-somos?msg=value_saved");
            exit;
        }
    }

    public function deleteValue()
    {
        $this->checkAdmin();
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            CompanyInfo::deleteValue($id);
        }
        header("Location: /yaconecta/public/admin/quienes-somos?msg=value_deleted");
        exit;
    }
}
