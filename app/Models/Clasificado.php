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

class Clasificado
{
    private static function getDB()
    {
        if (class_exists('Config\Database')) {
            return Database::getConnection();
        }
        return null;
    }

    // Obtener clasificados aprobados para la vista pública
    public static function obtenerAprobados(): array
    {
        try {
            $db = self::getDB();
            if (!$db) return [];
            $sql = "SELECT c.*, u.name as usuario_nombre 
                    FROM classifieds c 
                    LEFT JOIN users u ON c.user_id = u.id 
                    WHERE c.status = 'approved' AND (c.expires_at IS NULL OR c.expires_at >= CURDATE())
                    ORDER BY c.id DESC";
            $stmt = $db->query($sql);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        } catch (\Throwable $e) {
            error_log("Error en Clasificado::obtenerAprobados: " . $e->getMessage());
            return [];
        }
    }

    // Obtener todos los clasificados para la vista de administración
    public static function obtenerTodos(): array
    {
        try {
            $db = self::getDB();
            if (!$db) return [];
            $sql = "SELECT c.*, u.name as usuario_nombre 
                    FROM classifieds c 
                    LEFT JOIN users u ON c.user_id = u.id 
                    ORDER BY c.id DESC";
            $stmt = $db->query($sql);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    // Crear un anuncio (queda con status 'pending' por defecto)
    public static function crear(array $datos): bool
    {
        try {
            $db = self::getDB();
            if (!$db) return false;

            // Expiración por defecto a 30 días si no se especifica
            $expiresAt = date('Y-m-d', strtotime('+30 days'));

            $stmt = $db->prepare("INSERT INTO classifieds (user_id, title, description, category, price, contact_info, image_path, status, expires_at) 
                                  VALUES (:user_id, :title, :description, :category, :price, :contact_info, :image_path, 'pending', :expires_at)");

            return $stmt->execute([
                ':user_id'      => $datos['user_id'],
                ':title'        => trim($datos['title']),
                ':description'  => trim($datos['description']),
                ':category'     => trim($datos['category'] ?? 'otro'),
                ':price'        => !empty($datos['price']) ? $datos['price'] : null,
                ':contact_info' => trim($datos['contact_info']),
                ':image_path'   => $datos['image_path'] ?? null,
                ':expires_at'   => $expiresAt
            ]);
        } catch (\Throwable $e) {
            error_log("Error al crear clasificado: " . $e->getMessage());
            return false;
        }
    }

    // Cambiar el estado ('approved', 'rejected', 'pending')
    public static function cambiarEstado(int $id, string $status): bool
    {
        try {
            $db = self::getDB();
            if (!$db) return false;
            $stmt = $db->prepare("UPDATE classifieds SET status = :status WHERE id = :id");
            return $stmt->execute([':status' => $status, ':id' => $id]);
        } catch (\Throwable $e) {
            return false;
        }
    }

    // Eliminar anuncio
    public static function eliminar(int $id): bool
    {
        try {
            $db = self::getDB();
            if (!$db) return false;
            $stmt = $db->prepare("DELETE FROM classifieds WHERE id = :id");
            return $stmt->execute([':id' => $id]);
        } catch (\Throwable $e) {
            return false;
        }
    }
}