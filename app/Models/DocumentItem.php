<?php

namespace App\Models;

// Carga segura y robusta de la conexión a la base de datos
if (!class_exists('Config\Database')) {
    $databaseFile = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'Database.php';
    if (file_exists($databaseFile)) {
        require_once $databaseFile;
    }
}

use Config\Database;

class DocumentItem 
{
    public static function obtenerTodos(): array 
    {
        try {
            $db = Database::getConnection();
            $stmt = $db->query("SELECT * FROM documentos ORDER BY id DESC");
            return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    public static function buscarPorCategoria(string $categoria): array 
    {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("SELECT * FROM documentos WHERE categoria = :categoria ORDER BY id DESC");
            $stmt->execute([':categoria' => $categoria]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    public static function crear(array $datos): bool 
    {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("INSERT INTO documentos (nombre, categoria, url_archivo, descripcion) VALUES (:nombre, :categoria, :url_archivo, :descripcion)");
            return $stmt->execute([
                ':nombre'      => $datos['nombre'] ?? '',
                ':categoria'   => $datos['categoria'] ?? 'General',
                ':url_archivo' => $datos['url_archivo'] ?? '',
                ':descripcion' => $datos['descripcion'] ?? ''
            ]);
        } catch (\Throwable $e) {
            return false;
        }
    }

    public static function actualizar(int $id, array $datos): bool 
    {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("UPDATE documentos SET nombre = :nombre, categoria = :categoria, url_archivo = :url_archivo, descripcion = :descripcion WHERE id = :id");
            return $stmt->execute([
                ':id'          => $id,
                ':nombre'      => $datos['nombre'] ?? '',
                ':categoria'   => $datos['categoria'] ?? 'General',
                ':url_archivo' => $datos['url_archivo'] ?? '',
                ':descripcion' => $datos['descripcion'] ?? ''
            ]);
        } catch (\Throwable $e) {
            return false;
        }
    }

    public static function eliminar(int $id): bool 
    {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("DELETE FROM documentos WHERE id = :id");
            return $stmt->execute([':id' => $id]);
        } catch (\Throwable $e) {
            return false;
        }
    }
}