<?php

namespace App\Models;

use PDO;

class CompanyInfo
{
    private static function getDB()
    {
        if (class_exists('App\Core\Database')) {
            return \App\Core\Database::getInstance()->getConnection();
        }
        
        if (class_exists('Config\Database')) {
            if (method_exists('Config\Database', 'getConnection')) {
                return \Config\Database::getConnection();
            }
            if (method_exists('Config\Database', 'getInstance')) {
                return \Config\Database::getInstance()->getConnection();
            }
        }

        static $pdo = null;
        if ($pdo === null) {
            $host     = '127.0.0.1';
            $port     = '3306';
            $dbname   = 'yaconecta_db';
            $user     = 'root';
            $password = '';

            try {
                $pdo = new PDO("mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4", $user, $password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]);
            } catch (\PDOException $e) {
                die("Error de conexión a la base de datos: " . $e->getMessage());
            }
        }

        return $pdo;
    }

    // Obtener todas las secciones de información
    public static function getSections(): array
    {
        try {
            $db = self::getDB();
            $stmt = $db->query("SELECT * FROM company_info");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (\PDOException $e) {
            return [];
        }
        
        $sections = [];
        foreach ($rows as $row) {
            $sections[$row['section_key']] = $row;
        }
        return $sections;
    }

    // Guardar o actualizar una sección
    public static function updateSection(string $key, string $title, ?string $subtitle, ?string $content, ?string $mediaPath = null): bool
    {
        $db = self::getDB();
        $sql = "INSERT INTO company_info (section_key, title, subtitle, content, media_path) 
                VALUES (:key, :title, :subtitle, :content, :media)
                ON DUPLICATE KEY UPDATE 
                    title = VALUES(title), 
                    subtitle = VALUES(subtitle), 
                    content = VALUES(content)";
        
        $params = [
            'key'      => $key,
            'title'    => $title,
            'subtitle' => $subtitle,
            'content'  => $content,
            'media'    => $mediaPath
        ];

        if ($mediaPath !== null) {
            $sql = "INSERT INTO company_info (section_key, title, subtitle, content, media_path) 
                    VALUES (:key, :title, :subtitle, :content, :media)
                    ON DUPLICATE KEY UPDATE 
                        title = VALUES(title), 
                        subtitle = VALUES(subtitle), 
                        content = VALUES(content),
                        media_path = VALUES(media_path)";
        }

        $stmt = $db->prepare($sql);
        return $stmt->execute($params);
    }

    // Galería Multimedia de la Historia
    public static function getMedia(): array
    {
        try {
            $db = self::getDB();
            return $db->query("SELECT * FROM company_media ORDER BY order_index ASC, id DESC")->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (\PDOException $e) {
            return [];
        }
    }

    public static function addMedia(string $type, string $filePath, ?string $caption = ''): bool
    {
        $db = self::getDB();
        $stmt = $db->prepare("INSERT INTO company_media (type, file_path, caption) VALUES (:type, :path, :caption)");
        return $stmt->execute([
            'type'    => $type,
            'path'    => $filePath,
            'caption' => $caption
        ]);
    }

    public static function deleteMedia(int $id): bool
    {
        $db = self::getDB();
        $stmt = $db->prepare("DELETE FROM company_media WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    // Valores Corporativos
    public static function getValues(): array
    {
        try {
            $db = self::getDB();
            return $db->query("SELECT * FROM company_values ORDER BY order_index ASC, id ASC")->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (\PDOException $e) {
            return [];
        }
    }

    public static function saveValue(array $data): bool
    {
        $db = self::getDB();
        if (!empty($data['id'])) {
            $stmt = $db->prepare("UPDATE company_values SET icon = :icon, title = :title, description = :description WHERE id = :id");
            return $stmt->execute([
                'icon'        => $data['icon'],
                'title'       => $data['title'],
                'description' => $data['description'],
                'id'          => $data['id']
            ]);
        } else {
            $stmt = $db->prepare("INSERT INTO company_values (icon, title, description) VALUES (:icon, :title, :description)");
            return $stmt->execute([
                'icon'        => $data['icon'],
                'title'       => $data['title'],
                'description' => $data['description']
            ]);
        }
    }

    public static function deleteValue(int $id): bool
    {
        $db = self::getDB();
        $stmt = $db->prepare("DELETE FROM company_values WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
