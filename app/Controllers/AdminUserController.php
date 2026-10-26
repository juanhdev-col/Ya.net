<?php

namespace App\Controllers;

// Carga segura de conexión a la BD
$baseDir = dirname(__DIR__, 2);
$posiblesRutas = [
    $baseDir . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'Database.php',
    $baseDir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Services' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'Database.php'
];

foreach ($posiblesRutas as $ruta) {
    if (file_exists($ruta)) {
        require_once $ruta;
        break;
    }
}

use Config\Database;

class AdminUserController
{
    private function getDB()
    {
        if (class_exists('Config\Database')) {
            return Database::getConnection();
        }
        return null;
    }

    public function index()
    {
        $db = $this->getDB();
        $usuarios = [];
        $rolesBD = [];

        if ($db) {
            try {
                // 1. Consultar Roles reales de MySQL
                $stmtRoles = $db->query("SELECT * FROM roles ORDER BY id ASC");
                $rolesBD = $stmtRoles->fetchAll(\PDO::FETCH_ASSOC) ?: [];

                // 2. Consultar Usuarios guardados en la BD con su Rol
                $sqlUsers = "SELECT u.*, r.name as nombre_rol 
                             FROM usuarios u 
                             LEFT JOIN roles r ON u.role_id = r.id 
                             ORDER BY u.id DESC";
                $stmtUsers = $db->query($sqlUsers);
                $usuarios = $stmtUsers->fetchAll(\PDO::FETCH_ASSOC) ?: [];
            } catch (\Throwable $e) {
                error_log("Error al consultar usuarios/roles: " . $e->getMessage());
            }
        }

        // Matriz de Roles por defecto (Fallback)
        $roles = [
            'Administrador General' => 'Acceso completo al sistema, creación de usuarios y configuración.',
            'Administrador de Contenido' => 'Creación y actualización de contenidos en módulos autorizados.',
            'Líder de Área' => 'Administración y publicación de contenidos específicos de su área.',
            'Colaborador' => 'Consulta de información autorizada según área y nivel.',
            'Consulta General' => 'Acceso básico únicamente a información institucional pública.'
        ];

        // Áreas de la Empresa
        $areas = [
            'Tecnología y Sistemas',
            'Control Interno y Calidad',
            'Gestión Humana',
            'Operaciones y Comercial',
            'Financiera y Contabilidad',
            'Presidencia / Dirección General'
        ];

        // Fallback si aún no hay usuarios en la tabla MySQL
        if (empty($usuarios)) {
            $usuarios = [
                [
                    'id' => 1,
                    'usuario_ad' => 'yeimy.johana',
                    'nombre' => 'Yeimy Johana',
                    'email' => 'yeimy.johana@yajuego.co',
                    'cargo' => 'Líder de Control Interno',
                    'area' => 'Control Interno y Calidad',
                    'rol' => 'Administrador General',
                    'role_id' => 1,
                    'estado' => 'Activo',
                    'ultimo_ingreso' => '06/10/2026 09:30 AM'
                ],
                [
                    'id' => 2,
                    'usuario_ad' => 'carlos.rodriguez',
                    'nombre' => 'Carlos Rodríguez',
                    'email' => 'carlos.rodriguez@yajuego.co',
                    'cargo' => 'Analista de Sistemas',
                    'area' => 'Tecnología y Sistemas',
                    'rol' => 'Administrador de Contenido',
                    'role_id' => 2,
                    'estado' => 'Activo',
                    'ultimo_ingreso' => '06/10/2026 08:15 AM'
                ]
            ];
        }

        $ds = DIRECTORY_SEPARATOR;
        $vista = dirname(__DIR__, 2) . $ds . 'views' . $ds . 'admin' . $ds . 'usuarios' . $ds . 'index.php';

        if (file_exists($vista)) {
            require_once $vista;
        } else {
            echo "Vista no encontrada en: " . $vista;
        }
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db = $this->getDB();

            if ($db) {
                try {
                    $usuario_ad = trim($_POST['usuario_ad'] ?? $_POST['username'] ?? '');
                    $nombre     = trim($_POST['nombre'] ?? '');
                    $email      = trim($_POST['email'] ?? '');
                    $cargo      = trim($_POST['cargo'] ?? '');
                    $area       = trim($_POST['area'] ?? '');
                    $role_id    = (int)($_POST['role_id'] ?? $_POST['rol'] ?? 4);

                    if (!empty($nombre)) {
                        $stmt = $db->prepare("INSERT INTO usuarios (usuario_ad, nombre, email, cargo, area, role_id, estado) 
                                              VALUES (:usuario_ad, :nombre, :email, :cargo, :area, :role_id, 'Activo')");
                        $stmt->execute([
                            ':usuario_ad' => $usuario_ad,
                            ':nombre'     => $nombre,
                            ':email'      => $email,
                            ':cargo'      => $cargo,
                            ':area'       => $area,
                            ':role_id'    => $role_id
                        ]);
                    }
                } catch (\Throwable $e) {
                    error_log("Error al crear usuario: " . $e->getMessage());
                }
            }
        }

        header('Location: /yaconecta/public/admin/usuarios?status=created');
        exit;
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db = $this->getDB();
            $id = $_POST['id'] ?? null;

            if ($db && $id) {
                try {
                    $role_id = (int)($_POST['role_id'] ?? $_POST['rol'] ?? 4);
                    $stmt = $db->prepare("UPDATE usuarios SET role_id = :role_id, cargo = :cargo, area = :area WHERE id = :id");
                    $stmt->execute([
                        ':role_id' => $role_id,
                        ':cargo'   => trim($_POST['cargo'] ?? ''),
                        ':area'    => trim($_POST['area'] ?? ''),
                        ':id'      => (int)$id
                    ]);
                } catch (\Throwable $e) {
                    error_log("Error al actualizar usuario: " . $e->getMessage());
                }
            }
        }

        header('Location: /yaconecta/public/admin/usuarios?status=updated');
        exit;
    }

    public function delete()
    {
        $id = $_GET['id'] ?? $_POST['id'] ?? null;
        $db = $this->getDB();

        if ($db && $id) {
            try {
                $stmt = $db->prepare("DELETE FROM usuarios WHERE id = :id");
                $stmt->execute([':id' => (int)$id]);
            } catch (\Throwable $e) {
                error_log("Error al eliminar usuario: " . $e->getMessage());
            }
        }

        header('Location: /yaconecta/public/admin/usuarios?status=deleted');
        exit;
    }
}