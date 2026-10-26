<?php

namespace App\Controllers;

use App\Models\Colaborador;

class AdminDirectoryController
{
    public function index()
    {
        $this->requireAdmin();
        $busqueda = trim($_GET['search'] ?? $_GET['q'] ?? '');
        $area     = trim($_GET['area'] ?? '');
        $cargo = trim($_GET['cargo'] ?? '');
        $estado = trim($_GET['estado'] ?? '');
        $colaboradores = Colaborador::obtenerTodos($busqueda, $area, $cargo, $estado);
        $filtrosDirectorio = Colaborador::obtenerAreasYcargos();
        $totalColaboradores = count(Colaborador::obtenerTodos());
        $departamentos = count($filtrosDirectorio['areas']);
        $cargosTotal = count($filtrosDirectorio['cargos']);
        $usuariosSistema = 0;
        try { $usuariosSistema = (int)\Config\Database::getConnection()->query('SELECT COUNT(*) FROM users')->fetchColumn(); } catch (\Throwable $e) {}
        if (!$colaboradores) {
            $colaboradores = [
                ['id'=>-1,'nombre'=>'Juan Andrés','apellido'=>'Henao','cargo'=>'Analista de control interno y calidad','area'=>'Control Interno y Calidad','email'=>'','telefono'=>'','extension'=>'','foto'=>'','estado'=>'activo'],
                ['id'=>-2,'nombre'=>'Andrea','apellido'=>'Cortes','cargo'=>'Aprendiz SENA','area'=>'Control Interno y Calidad','email'=>'','telefono'=>'','extension'=>'','foto'=>'','estado'=>'activo'],
                ['id'=>-3,'nombre'=>'Yeimy','apellido'=>'Cortés','cargo'=>'Jefe de Control Interno y Calidad','area'=>'Control Interno y Calidad','email'=>'','telefono'=>'','extension'=>'','foto'=>'','estado'=>'activo'],
                ['id'=>-4,'nombre'=>'Diego','apellido'=>'Chacón','cargo'=>'Jefe de Operaciones','area'=>'Operaciones','email'=>'','telefono'=>'','extension'=>'','foto'=>'','estado'=>'activo'],
            ];
            if ($busqueda !== '') $colaboradores = array_values(array_filter($colaboradores, static fn(array $c): bool => stripos(implode(' ', $c), $busqueda) !== false));
            if ($area !== '') $colaboradores = array_values(array_filter($colaboradores, static fn(array $c): bool => $c['area'] === $area));
            if ($cargo !== '') $colaboradores = array_values(array_filter($colaboradores, static fn(array $c): bool => $c['cargo'] === $cargo));
            if ($estado !== '') $colaboradores = array_values(array_filter($colaboradores, static fn(array $c): bool => $c['estado'] === $estado));
            $totalColaboradores = 4;
            $departamentos = 2;
            $cargosTotal = 4;
            $filtrosDirectorio = ['areas'=>['Control Interno y Calidad','Operaciones'],'cargos'=>['Analista de control interno y calidad','Aprendiz SENA','Jefe de Control Interno y Calidad','Jefe de Operaciones']];
        }

        $ds = DIRECTORY_SEPARATOR;
        $vista = dirname(__DIR__, 2) . $ds . 'views' . $ds . 'admin' . $ds . 'directorio' . $ds . 'index.php';

        if (file_exists($vista)) {
            require_once $vista;
        } else {
            echo "Vista no encontrada en: " . $vista;
        }
    }

    public function store()
    {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre    = trim($_POST['nombre'] ?? '');
            $apellido  = trim($_POST['apellido'] ?? '');
            $cargo     = trim($_POST['cargo'] ?? '');
            $area      = trim($_POST['area'] ?? 'General');
            $email     = trim($_POST['email'] ?? '');
            $telefono  = trim($_POST['telefono'] ?? '');
            $extension = trim($_POST['extension'] ?? '');
            $estado    = trim($_POST['estado'] ?? 'activo');

            $fotoPath = 'default_avatar.png';

            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $fileTmp   = $_FILES['foto']['tmp_name'];
                $fileName  = time() . '_' . basename($_FILES['foto']['name']);
                $uploadDir = dirname(__DIR__, 2) . '/public/uploads/directorio/';

                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $destPath = $uploadDir . $fileName;
                if (move_uploaded_file($fileTmp, $destPath)) {
                    $fotoPath = '/uploads/directorio/' . $fileName;
                }
            }

            if (!empty($nombre)) {
                Colaborador::crear([
                    'nombre'    => $nombre,
                    'apellido'  => $apellido,
                    'cargo'     => $cargo,
                    'area'      => $area,
                    'email'     => $email,
                    'telefono'  => $telefono,
                    'extension' => $extension,
                    'foto'      => $fotoPath,
                    'estado'    => $estado
                ]);
            }

            header("Location: /yaconecta/public/admin/directorio?msg=created");
            exit;
        }
    }

    public function update()
    {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id        = $_POST['id'] ?? null;
            $nombre    = trim($_POST['nombre'] ?? '');
            $apellido  = trim($_POST['apellido'] ?? '');
            $cargo     = trim($_POST['cargo'] ?? '');
            $area      = trim($_POST['area'] ?? 'General');
            $email     = trim($_POST['email'] ?? '');
            $telefono  = trim($_POST['telefono'] ?? '');
            $extension = trim($_POST['extension'] ?? '');
            $estado    = trim($_POST['estado'] ?? 'activo');

            $fotoPath = '';

            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $fileTmp   = $_FILES['foto']['tmp_name'];
                $fileName  = time() . '_' . basename($_FILES['foto']['name']);
                $uploadDir = dirname(__DIR__, 2) . '/public/uploads/directorio/';

                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                if (move_uploaded_file($fileTmp, $uploadDir . $fileName)) {
                    $fotoPath = '/uploads/directorio/' . $fileName;
                }
            }

            if ($id && !empty($nombre)) {
                Colaborador::actualizar([
                    'id'        => $id,
                    'nombre'    => $nombre,
                    'apellido'  => $apellido,
                    'cargo'     => $cargo,
                    'area'      => $area,
                    'email'     => $email,
                    'telefono'  => $telefono,
                    'extension' => $extension,
                    'foto'      => $fotoPath,
                    'estado'    => $estado
                ]);
            }

            header("Location: /yaconecta/public/admin/directorio?msg=updated");
            exit;
        }
    }

    public function delete()
    {
        $this->requireAdmin();
        $id = $_GET['id'] ?? $_POST['id'] ?? null;
        if ($id) {
            Colaborador::eliminar($id);
        }
        header("Location: /yaconecta/public/admin/directorio?msg=deleted");
        exit;
    }

    private function requireAdmin(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (empty($_SESSION['usuario']) || !in_array((int)($_SESSION['usuario']['role_id'] ?? 0), [1, 2], true)) {
            http_response_code(403);
            exit('Acceso restringido. Inicia sesión con una cuenta administradora.');
        }
    }
}
