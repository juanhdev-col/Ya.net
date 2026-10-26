<?php

namespace App\Models;

// Carga segura de la conexión a la Base de Datos
$baseDir = dirname(__DIR__, 2);
$rutasDB = [
    $baseDir . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'Database.php',
    $baseDir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Services' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'Database.php'
];

foreach ($rutasDB as $ruta) {
    if (file_exists($ruta)) {
        require_once $ruta;
        break;
    }
}

use Config\Database;

class Banner {

    private static function getDB() {
        if (class_exists('Config\Database')) {
            return Database::getConnection();
        }
        throw new \Exception("No se encontró la clase Config\Database.");
    }

    private static function prepararTabla($db) {
        $db->exec("CREATE TABLE IF NOT EXISTS banners (
            id INT AUTO_INCREMENT PRIMARY KEY,
            titulo VARCHAR(255) NOT NULL,
            subtitulo VARCHAR(255) NULL,
            etiqueta VARCHAR(100) DEFAULT 'CULTURA YAJUEGO',
            tipo_media ENUM('imagen', 'video') DEFAULT 'imagen',
            url_media TEXT NOT NULL,
            enlace_boton VARCHAR(255) NULL,
            texto_boton VARCHAR(100) DEFAULT 'Ver más',
            orden INT DEFAULT 1,
            is_active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    }

    public static function obtenerActivos(): array {
        try {
            $db = self::getDB();
            self::prepararTabla($db);
            $stmt = $db->query("SELECT * FROM banners WHERE is_active = 1 ORDER BY orden ASC, id DESC");
            return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        } catch (\Throwable $e) {
            error_log("Error en Banner::obtenerActivos: " . $e->getMessage());
            return [];
        }
    }

    public static function obtenerTodos(): array {
        try {
            $db = self::getDB();
            self::prepararTabla($db);
            $stmt = $db->query("SELECT * FROM banners ORDER BY orden ASC, id DESC");
            return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    public static function crear(array $datos): bool {
        $db = self::getDB();
        self::prepararTabla($db);

        $stmt = $db->prepare("INSERT INTO banners (titulo, subtitulo, etiqueta, tipo_media, url_media, enlace_boton, texto_boton, orden) VALUES (:titulo, :subtitulo, :etiqueta, :tipo_media, :url_media, :enlace_boton, :texto_boton, :orden)");
        return $stmt->execute([
            ':titulo'       => trim($datos['titulo'] ?? ''),
            ':subtitulo'    => trim($datos['subtitulo'] ?? ''),
            ':etiqueta'     => trim($datos['etiqueta'] ?? 'CULTURA YAJUEGO'),
            ':tipo_media'   => $datos['tipo_media'] ?? 'imagen',
            ':url_media'    => trim($datos['url_media'] ?? ''),
            ':enlace_boton' => trim($datos['enlace_boton'] ?? ''),
            ':texto_boton'  => trim($datos['texto_boton'] ?? 'Ver más'),
            ':orden'        => (int)($datos['orden'] ?? 1)
        ]);
    }

    public static function eliminar(int $id): bool {
        try {
            $db = self::getDB();
            $stmt = $db->prepare("DELETE FROM banners WHERE id = :id");
            return $stmt->execute([':id' => $id]);
        } catch (\Throwable $e) {
            return false;
        }
    }
}
