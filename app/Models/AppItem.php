<?php

namespace App\Models;

// Carga directa de la base de datos desde app/Services/config/Database.php
$databasePath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Services' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'Database.php';

if (file_exists($databasePath)) {
    require_once $databasePath;
}

use Config\Database;

class AppItem {

    private static function getDB() {
        if (class_exists('Config\Database')) {
            return Database::getConnection();
        }
        
        throw new \Exception("No se pudo cargar la clase Config\Database.");
    }

    private static function prepararTabla($db) {
        // 1. Crear la tabla 'apps'
        $db->exec("CREATE TABLE IF NOT EXISTS apps (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            url TEXT NOT NULL,
            category VARCHAR(100) DEFAULT 'General',
            description TEXT NULL,
            is_active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        // 2. Modificar la columna 'url' a TEXT si existía como VARCHAR
        try {
            $db->exec("ALTER TABLE apps MODIFY url TEXT NOT NULL;");
        } catch (\Throwable $e) {
            // Continuar si ya es TEXT
        }
    }

    public static function obtenerTodos(string $busqueda = '', string $categoria = '', string $estado = ''): array {
        try {
            $db = self::getDB();
            self::prepararTabla($db);

            $sql = "SELECT id, 
                           name, name AS nombre, 
                           url, url AS url_acceso, 
                           category, category AS categoria, 
                           description, description AS descripcion, is_active,
                           created_at 
                    FROM apps 
                    WHERE 1=1";
            $params = [];

            if (!empty($busqueda)) {
                $sql .= " AND (name LIKE :search_name OR category LIKE :search_category OR description LIKE :search_description)";
                $term = "%{$busqueda}%";
                $params['search_name'] = $term;
                $params['search_category'] = $term;
                $params['search_description'] = $term;
            }

            if (!empty($categoria)) {
                $sql .= " AND category = :cat";
                $params['cat'] = $categoria;
            }

            if ($estado !== '') {
                $sql .= ' AND is_active = :active';
                $params['active'] = $estado === 'activa' ? 1 : 0;
            }

            $sql .= " ORDER BY id DESC";

            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $apps = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if (!empty($apps)) {
                return $apps;
            }

            // Use the legacy table only when `apps` itself has not been populated.
            if ((int)$db->query('SELECT COUNT(*) FROM apps')->fetchColumn() > 0) return [];

            // Fallback a tabla legacy 'aplicativos' si 'apps' está vacía
            try {
                $stmtLegacy = $db->query("SELECT id, nombre AS name, nombre, url_acceso AS url, url_acceso, 'General' AS category, 'General' AS categoria, 'Herramienta corporativa' AS description, 'Herramienta corporativa' AS descripcion, 1 AS is_active FROM aplicativos ORDER BY orden ASC, id DESC");
                $legacy = $stmtLegacy->fetchAll(\PDO::FETCH_ASSOC) ?: [];
                return array_values(array_filter($legacy, static fn(array $app): bool => ($categoria === '' || $app['category'] === $categoria) && ($estado === '' || $estado === 'activa') && ($busqueda === '' || stripos(implode(' ', [$app['name'], $app['category'], $app['description']]), $busqueda) !== false)));
            } catch (\Throwable $th) {
                return [];
            }

        } catch (\Throwable $e) {
            error_log("Error en AppItem::obtenerTodos: " . $e->getMessage());
            return [];
        }
    }

    public static function obtenerResumen(): array {
        try {
            $db = self::getDB();
            self::prepararTabla($db);
            $row = $db->query('SELECT COUNT(*) AS total, SUM(is_active=1) AS activas, SUM(is_active=0) AS inactivas, COUNT(DISTINCT category) AS categorias FROM apps')->fetch(\PDO::FETCH_ASSOC) ?: [];
            if ((int)($row['total'] ?? 0) === 0) {
                try {
                    $legacyTotal = (int)$db->query('SELECT COUNT(*) FROM aplicativos')->fetchColumn();
                    if ($legacyTotal > 0) return ['total'=>$legacyTotal,'activas'=>$legacyTotal,'inactivas'=>0,'categorias'=>1];
                } catch (\Throwable $ignored) {}
            }
            return ['total'=>(int)($row['total']??0),'activas'=>(int)($row['activas']??0),'inactivas'=>(int)($row['inactivas']??0),'categorias'=>(int)($row['categorias']??0)];
        } catch (\Throwable $e) { return ['total'=>0,'activas'=>0,'inactivas'=>0,'categorias'=>0]; }
    }

    public static function crear(array $datos): bool {
        $db = self::getDB();
        self::prepararTabla($db);

        $name        = $datos['name'] ?? $datos['nombre'] ?? '';
        $url         = $datos['url'] ?? $datos['url_acceso'] ?? '';
        $category    = $datos['category'] ?? $datos['categoria'] ?? 'General';
        $description = $datos['description'] ?? $datos['descripcion'] ?? '';

        if (empty(trim($name)) || empty(trim($url))) {
            throw new \Exception("El nombre y la URL son campos obligatorios.");
        }

        $stmt = $db->prepare("INSERT INTO apps (name, description, category, url, is_active) VALUES (:name, :description, :category, :url, :active)");
        return $stmt->execute([
            ':name'        => trim($name),
            ':description' => trim($description),
            ':category'    => trim($category),
            ':url'         => trim($url),
            ':active'      => isset($datos['is_active']) ? (int)(bool)$datos['is_active'] : 1
        ]);
    }

    public static function actualizar(int $id, array $datos): bool {
        $db = self::getDB();
        self::prepararTabla($db);

        $name        = $datos['name'] ?? $datos['nombre'] ?? '';
        $url         = $datos['url'] ?? $datos['url_acceso'] ?? '';
        $category    = $datos['category'] ?? $datos['categoria'] ?? 'General';
        $description = $datos['description'] ?? $datos['descripcion'] ?? '';

        $stmt = $db->prepare("UPDATE apps SET name = :name, url = :url, category = :category, description = :description, is_active = :active WHERE id = :id");
        $stmt->execute([
            ':id'          => $id,
            ':name'        => trim($name),
            ':url'         => trim($url),
            ':category'    => trim($category),
            ':description' => trim($description),
            ':active'      => isset($datos['is_active']) ? (int)(bool)$datos['is_active'] : 1
        ]);
        if ($stmt->rowCount() > 0) return true;
        $exists = $db->prepare('SELECT id FROM apps WHERE id = :id');
        $exists->execute([':id'=>$id]);
        if ($exists->fetchColumn()) return true;
        try {
            $legacy = $db->prepare('SELECT id FROM aplicativos WHERE id = :id');
            $legacy->execute([':id'=>$id]);
            if (!$legacy->fetchColumn()) return false;
            $db->beginTransaction();
            $insert = $db->prepare('INSERT INTO apps (name,url,category,description,is_active) VALUES (:name,:url,:category,:description,:active)');
            $insert->execute([':name'=>trim($name),':url'=>trim($url),':category'=>trim($category),':description'=>trim($description),':active'=>isset($datos['is_active'])?(int)(bool)$datos['is_active']:1]);
            $db->prepare('DELETE FROM aplicativos WHERE id = :id')->execute([':id'=>$id]);
            $db->commit();
            return true;
        } catch (\Throwable $e) {
            if ($db->inTransaction()) $db->rollBack();
            return false;
        }
    }

    public static function eliminar(int $id): bool {
        try {
            $db = self::getDB();
            $stmt = $db->prepare("DELETE FROM apps WHERE id = :id");
            $stmt->execute([':id' => $id]);
            if ($stmt->rowCount() > 0) return true;
            try {
                return $db->prepare('DELETE FROM aplicativos WHERE id = :id')->execute([':id'=>$id]);
            } catch (\Throwable $ignored) { return false; }
        } catch (\Throwable $e) {
            return false;
        }
    }
}
