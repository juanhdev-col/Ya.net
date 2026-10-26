<?php

namespace App\Models;

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

class Colaborador
{
    private static function getDB()
    {
        if (class_exists('Config\Database')) {
            return Database::getConnection();
        }
        return null;
    }

    public static function obtenerTodos(string $busqueda = '', string $area = '', string $cargo = '', string $estado = ''): array
    {
        try {
            $db = self::getDB();
            if (!$db) return [];
            $sql = 'SELECT id,nombre,apellido,cargo,area,email,telefono,extension,foto,estado FROM colaboradores WHERE 1=1';
            $params = [];
            if ($busqueda !== '') {
                $sql .= " AND (nombre LIKE :q1 OR apellido LIKE :q2 OR CONCAT(nombre,' ',apellido) LIKE :q3 OR cargo LIKE :q4 OR area LIKE :q5 OR email LIKE :q6 OR extension LIKE :q7)";
                for ($i=1;$i<=7;$i++) $params['q'.$i] = '%'.$busqueda.'%';
            }
            if ($area !== '') { $sql .= ' AND area=:area'; $params['area']=$area; }
            if ($cargo !== '') { $sql .= ' AND cargo=:cargo'; $params['cargo']=$cargo; }
            if ($estado !== '') { $sql .= ' AND estado=:estado'; $params['estado']=$estado; }
            $sql .= ' ORDER BY nombre, apellido';
            $stmt=$db->prepare($sql); $stmt->execute($params);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        } catch (\Throwable $e) { error_log('Error consultando colaboradores: '.$e->getMessage()); return []; }
    }

    public static function crear(array $data): bool
    {
        $stmt=self::getDB()->prepare('INSERT INTO colaboradores (nombre,apellido,cargo,area,email,telefono,extension,foto,estado) VALUES (:nombre,:apellido,:cargo,:area,:email,:telefono,:extension,:foto,:estado)');
        return $stmt->execute(['nombre'=>$data['nombre'],'apellido'=>$data['apellido'],'cargo'=>$data['cargo'],'area'=>$data['area'],'email'=>$data['email'] ?? '','telefono'=>$data['telefono'] ?? '','extension'=>$data['extension'] ?? '','foto'=>$data['foto'] ?? '','estado'=>$data['estado'] ?? 'activo']);
    }

    public static function actualizar(array $data): bool
    {
        $sql='UPDATE colaboradores SET nombre=:nombre,apellido=:apellido,cargo=:cargo,area=:area,email=:email,telefono=:telefono,extension=:extension,estado=:estado';
        $params=['id'=>$data['id'],'nombre'=>$data['nombre'],'apellido'=>$data['apellido'],'cargo'=>$data['cargo'],'area'=>$data['area'],'email'=>$data['email'] ?? '','telefono'=>$data['telefono'] ?? '','extension'=>$data['extension'] ?? '','estado'=>$data['estado'] ?? 'activo'];
        if (!empty($data['foto'])) { $sql.=',foto=:foto'; $params['foto']=$data['foto']; }
        $sql.=' WHERE id=:id';
        return self::getDB()->prepare($sql)->execute($params);
    }

    public static function eliminar(int $id): bool
    {
        return self::getDB()->prepare('DELETE FROM colaboradores WHERE id=:id')->execute(['id'=>$id]);
    }

    /**
     * Busca colaboradores por nombre, apellido, cargo, área o email (Para Directorio)
     */
    public static function buscar(string $q = '', string $area = '', string $cargo = ''): array
    {
        try {
            $db = self::getDB();
            if (!$db) return [];

            $q = trim($q);

            $sql = "SELECT id, nombre, apellido, cargo, area, email, telefono, extension, foto, estado
                    FROM colaboradores WHERE estado = 'activo'";
            $params = [];
            if ($q !== '') {
                $sql .= " AND (nombre LIKE :q1 OR apellido LIKE :q2 OR cargo LIKE :q3 OR area LIKE :q4 OR email LIKE :q5 OR CONCAT(nombre, ' ', apellido) LIKE :q6)";
                $term = '%' . $q . '%';
                for ($i = 1; $i <= 6; $i++) $params['q' . $i] = $term;
            }
            if ($area !== '') { $sql .= ' AND area = :area'; $params['area'] = $area; }
            if ($cargo !== '') { $sql .= ' AND cargo = :cargo'; $params['cargo'] = $cargo; }
            $sql .= ' ORDER BY nombre, apellido';
            $stmt = $db->prepare($sql);
            $stmt->execute($params);

            return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        } catch (\Throwable $e) {
            error_log("Error en Colaborador::buscar: " . $e->getMessage());
            return [];
        }
    }

    public static function obtenerAreasYcargos(): array
    {
        try {
            $db = self::getDB();
            if (!$db) return ['areas' => [], 'cargos' => []];
            $areas = $db->query("SELECT DISTINCT area FROM colaboradores WHERE estado='activo' AND area<>'' ORDER BY area")->fetchAll(\PDO::FETCH_COLUMN) ?: [];
            $cargos = $db->query("SELECT DISTINCT cargo FROM colaboradores WHERE estado='activo' AND cargo<>'' ORDER BY cargo")->fetchAll(\PDO::FETCH_COLUMN) ?: [];
            return ['areas' => $areas, 'cargos' => $cargos];
        } catch (\Throwable $e) {
            error_log('Error al consultar filtros del directorio: ' . $e->getMessage());
            return ['areas' => [], 'cargos' => []];
        }
    }

    public static function agregarSiNoExiste(array $colaborador): bool
    {
        try {
            $db = self::getDB();
            if (!$db) return false;
            $find = $db->prepare('SELECT id FROM colaboradores WHERE LOWER(nombre)=LOWER(:nombre) AND LOWER(apellido)=LOWER(:apellido) LIMIT 1');
            $find->execute(['nombre' => $colaborador['nombre'], 'apellido' => $colaborador['apellido']]);
            if ($find->fetchColumn()) return true;
            $stmt = $db->prepare("INSERT INTO colaboradores (nombre, apellido, cargo, area, email, telefono, extension, foto, estado)
                VALUES (:nombre, :apellido, :cargo, :area, '', '', '', '', 'activo')");
            return $stmt->execute($colaborador);
        } catch (\Throwable $e) {
            error_log('Error al agregar colaborador inicial: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Busca un colaborador por username, email o documento para el inicio de sesión (Login)
     */
   public static function buscarPorUsername(string $username): ?array
{
    try {
        $db = self::getDB();
        if (!$db) return null;

        $input = trim($username);

        // Buscar en users o colaboradores según corresponda
        try {
            $sql = "SELECT * FROM users WHERE username = :u1 OR email = :u2 LIMIT 1";
            $stmt = $db->prepare($sql);
            $stmt->execute([':u1' => $input, ':u2' => $input]);
            $res = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($res) return $res;
        } catch (\Throwable $e) {}

        try {
            $sql = "SELECT * FROM colaboradores WHERE email = :u1 OR username = :u2 LIMIT 1";
            $stmt = $db->prepare($sql);
            $stmt->execute([':u1' => $input, ':u2' => $input]);
            return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
        } catch (\Throwable $e) {
            return null;
        }

    } catch (\Throwable $e) {
        error_log("Error en Colaborador::buscarPorUsername: " . $e->getMessage());
        return null;
    }
}
}
