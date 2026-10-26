<?php
namespace App\Models;

use App\Config\Database;

class DirectoryItem {

    // Obtener todos los colaboradores
    public static function obtenerTodos() {
        try {
            $db = Database::getConnection();
            $stmt = $db->query("SELECT * FROM employees ORDER BY name ASC");
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            return [];
        }
    }

    // Registrar un nuevo colaborador
    public static function crear($datos) {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("INSERT INTO employees (name, position, department, email, extension, phone, avatar) VALUES (:name, :position, :department, :email, :extension, :phone, :avatar)");
            return $stmt->execute([
                ':name'       => $datos['name'],
                ':position'   => $datos['position'],
                ':department' => $datos['department'],
                ':email'      => $datos['email'],
                ':extension'  => $datos['extension'],
                ':phone'      => $datos['phone'],
                ':avatar'     => $datos['avatar']
            ]);
        } catch (\Throwable $e) {
            return false;
        }
    }

    // Eliminar colaborador
    public static function eliminar($id) {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("DELETE FROM employees WHERE id = :id");
            return $stmt->execute([':id' => $id]);
        } catch (\Throwable $e) {
            return false;
        }
    }
}