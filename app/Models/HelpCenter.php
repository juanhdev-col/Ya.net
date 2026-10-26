<?php

namespace App\Models;

use Config\Database;
use PDO;

class HelpCenter
{
    /**
     * Obtiene todas las preguntas frecuentes activas
     */
    public static function getFaqs(string $search = ''): array
    {
        try {
            $db = Database::getConnection();
            $search = trim($search);

            if (!empty($search)) {
                $sql = "SELECT * FROM faq 
                        WHERE estado = 'activo' 
                          AND (pregunta LIKE :s OR respuesta LIKE :s OR categoria LIKE :s) 
                        ORDER BY categoria ASC, orden ASC, id DESC";
                $stmt = $db->prepare($sql);
                $stmt->execute([':s' => '%' . $search . '%']);
            } else {
                $sql = "SELECT * FROM faq 
                        WHERE estado = 'activo' 
                        ORDER BY categoria ASC, orden ASC, id DESC";
                $stmt = $db->query($sql);
            }

            $faqs = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

            // Agrupar preguntas por categoría
            $grouped = [];
            foreach ($faqs as $faq) {
                $cat = $faq['categoria'] ?: 'General';
                $grouped[$cat][] = $faq;
            }

            return $grouped;
        } catch (\Throwable $e) {
            error_log("Error en HelpCenter::getFaqs: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene los miembros activos de un comité específico (copasst o convivencia)
     */
    public static function getComiteMiembros(string $comite): array
    {
        try {
            $db = Database::getConnection();
            $sql = "SELECT * FROM comites_miembros 
                    WHERE comite = :c AND estado = 'activo' 
                    ORDER BY id ASC";
            $stmt = $db->prepare($sql);
            $stmt->execute([':c' => $comite]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (\Throwable $e) {
            error_log("Error en HelpCenter::getComiteMiembros: " . $e->getMessage());
            return [];
        }
    }

    /**
 * Obtiene las solicitudes de un usuario específico (Privado)
 */
    public static function getSolicitudesPorUsuario(int $userId): array
    {
        try {
            $db = Database::getConnection();
            $sql = "SELECT * FROM comites_solicitudes 
                    WHERE user_id = :u 
                    ORDER BY id DESC";
            $stmt = $db->prepare($sql);
            $stmt->execute([':u' => $userId]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (\Throwable $e) {
            error_log("Error en HelpCenter::getSolicitudesPorUsuario: " . $e->getMessage());
            return [];
        }
    }

}