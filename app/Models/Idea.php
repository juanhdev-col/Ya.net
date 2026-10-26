<?php

namespace App\Models;

use PDO;

class Idea
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

        public static function obtenerTodas($busqueda = '', $estado = '')
    {
        $db = self::getDB();
        $sql = "SELECT i.id, i.user_id, i.title AS titulo, i.description AS descripcion, 
                    i.category AS categoria, i.status AS estado, i.adjunto, i.created_at,
                    u.nombre AS colaborador_nombre, u.apellido AS colaborador_apellido, u.area AS colaborador_area
                FROM innovation_ideas i 
                LEFT JOIN colaboradores u ON i.user_id = u.id 
                WHERE 1=1";
        $params = [];

        if (!empty($busqueda)) {
            $sql .= " AND (i.title LIKE :search OR i.description LIKE :search OR i.category LIKE :search)";
            $params['search'] = "%{$busqueda}%";
        }

        if (!empty($estado)) {
            $sql .= " AND i.status = :estado";
            $params['estado'] = $estado;
        }

        $sql .= " ORDER BY i.id DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function crear($userId, $titulo, $descripcion, $categoria, $adjunto = null)
    {
        $db = self::getDB();
        $sql = "INSERT INTO innovation_ideas (user_id, title, description, category, adjunto, status) 
                VALUES (:user_id, :title, :description, :category, :adjunto, 'Pendiente')";
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            'user_id'     => $userId,
            'title'       => $titulo,
            'description' => $descripcion,
            'category'    => $categoria,
            'adjunto'     => $adjunto
        ]);
    }

    public static function cambiarEstado($id, $estado)
    {
        $db = self::getDB();
        $sql = "UPDATE innovation_ideas SET status = :estado WHERE id = :id";
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            'id'     => $id,
            'estado' => $estado
        ]);
    }

    public static function eliminar($id)
    {
        $db = self::getDB();
        $stmt = $db->prepare("DELETE FROM innovation_ideas WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }


   

}
