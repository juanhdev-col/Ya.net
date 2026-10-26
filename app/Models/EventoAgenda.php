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

class EventoAgenda {

    private static function getDB() {
        if (class_exists('Config\Database')) {
            return Database::getConnection();
        }
        if (class_exists('App\Services\config\Database')) {
            return \App\Services\config\Database::getConnection();
        }
        throw new \Exception("No se encontró la clase Database.");
    }

    private static function prepararTabla($db) {
        $sql = "CREATE TABLE IF NOT EXISTS eventos_agenda (
            id INT AUTO_INCREMENT PRIMARY KEY,
            titulo VARCHAR(255) NOT NULL,
            descripcion TEXT NULL,
            tipo VARCHAR(50) DEFAULT 'evento',
            fecha_evento DATE NOT NULL,
            hora_evento TIME NULL,
            lugar VARCHAR(255) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        
        $db->exec($sql);
    }

    public static function obtenerProximos(): array {
        try {
            $db = self::getDB();
            self::prepararTabla($db);
            $stmt = $db->query("SELECT * FROM eventos_agenda WHERE fecha_evento >= CURDATE() ORDER BY fecha_evento ASC, hora_evento ASC LIMIT 10");
            return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        } catch (\Throwable $e) {
            error_log("Error en EventoAgenda::obtenerProximos: " . $e->getMessage());
            return [];
        }
    }

    public static function obtenerTodos(): array {
        try {
            $db = self::getDB();
            self::prepararTabla($db);
            $stmt = $db->query("SELECT * FROM eventos_agenda ORDER BY fecha_evento DESC");
            return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    public static function crear(array $datos): bool {
        try {
            $db = self::getDB();
            self::prepararTabla($db);

            $stmt = $db->prepare("INSERT INTO eventos_agenda (titulo, descripcion, tipo, fecha_evento, hora_evento, lugar) VALUES (:titulo, :descripcion, :tipo, :fecha_evento, :hora_evento, :lugar)");
            
            $hora = !empty($datos['hora_evento']) ? $datos['hora_evento'] : null;

            return $stmt->execute([
                ':titulo'       => trim($datos['titulo'] ?? ''),
                ':descripcion'  => trim($datos['descripcion'] ?? ''),
                ':tipo'         => trim($datos['tipo'] ?? 'evento'),
                ':fecha_evento' => $datos['fecha_evento'],
                ':hora_evento'  => $hora,
                ':lugar'        => trim($datos['lugar'] ?? '')
            ]);
        } catch (\Throwable $e) {
            // Muestra el error exacto en pantalla si falla en lugar de ocultarlo
            die("<div style='background:#fee2e2; border:1px solid #ef4444; padding:20px; font-family:sans-serif; border-radius:10px; margin:20px;'>
                    <h3 style='color:#991b1b; margin-top:0;'>Error de Inserción en la Base de Datos</h3>
                    <p style='color:#7f1d1d; font-size:14px;'><b>Detalle:</b> " . htmlspecialchars($e->getMessage()) . "</p>
                 </div>");
        }
    }

    public static function actualizar(int $id, array $datos): bool {
        $db = self::getDB();
        self::prepararTabla($db);
        $stmt = $db->prepare('UPDATE eventos_agenda SET titulo=:titulo, descripcion=:descripcion, tipo=:tipo, fecha_evento=:fecha, hora_evento=:hora, lugar=:lugar WHERE id=:id');
        return $stmt->execute([
            ':id'=>$id, ':titulo'=>trim($datos['titulo'] ?? ''), ':descripcion'=>trim($datos['descripcion'] ?? ''),
            ':tipo'=>trim($datos['tipo'] ?? 'evento'), ':fecha'=>$datos['fecha_evento'],
            ':hora'=>!empty($datos['hora_evento']) ? $datos['hora_evento'] : null, ':lugar'=>trim($datos['lugar'] ?? '')
        ]);
    }

    public static function eliminar(int $id): bool {
        try {
            $db = self::getDB();
            $stmt = $db->prepare("DELETE FROM eventos_agenda WHERE id = :id");
            return $stmt->execute([':id' => $id]);
        } catch (\Throwable $e) {
            return false;
        }
    }
}
