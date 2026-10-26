<?php

namespace App\Models;

// Escaneo dinámico de la ubicación de Database.php
$baseDir = dirname(__DIR__, 2); // Raíz del proyecto
$posiblesRutas = [
    $baseDir . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'Database.php', // Ubicación estándar en la raíz
    $baseDir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Services' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'Database.php', // Dentro de Services
    dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Services' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'Database.php'
];

foreach ($posiblesRutas as $ruta) {
    if (file_exists($ruta)) {
        require_once $ruta;
        break;
    }
}

use Config\Database;

class Noticia {

    private static function getDB() {
        if (class_exists('Config\Database')) {
            return Database::getConnection();
        }
        
        throw new \Exception("No se pudo instanciar Config\Database. Revisa la ubicación de la carpeta config.");
    }

    public static function obtenerTodas(): array {
        try {
            $db = self::getDB();
            $stmt = $db->query("SELECT * FROM noticias ORDER BY id DESC");
            return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        } catch (\Throwable $e) {
            error_log("Error en Noticia::obtenerTodas: " . $e->getMessage());
            return [];
        }
    }

    public static function crear(array $datos): bool {
        try {
            $db = self::getDB();
            
            $titulo    = trim($datos['titulo'] ?? '');
            $resumen   = trim($datos['resumen'] ?? '');
            $contenido = trim($datos['contenido'] ?? '');
            $imagen    = trim($datos['imagen'] ?? '');
            $categoria = trim($datos['categoria'] ?? 'Noticias');

            $stmt = $db->prepare("INSERT INTO noticias (titulo, resumen, contenido, imagen, categoria, estado) VALUES (:titulo, :resumen, :contenido, :imagen, :categoria, 'publicado')");
            return $stmt->execute([
                ':titulo'    => $titulo,
                ':resumen'   => $resumen,
                ':contenido' => $contenido,
                ':imagen'    => $imagen,
                ':categoria' => $categoria
            ]);
        } catch (\Throwable $e) {
            error_log("Error en Noticia::crear: " . $e->getMessage());
            return false;
        }
    }

    public static function eliminar(int $id): bool {
        try {
            $db = self::getDB();
            $stmt = $db->prepare("DELETE FROM noticias WHERE id = :id");
            return $stmt->execute([':id' => $id]);
        } catch (\Throwable $e) {
            return false;
        }
    }
}