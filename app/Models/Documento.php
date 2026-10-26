<?php

namespace App\Models;

use Config\Database;
use PDO;

class Documento
{
    private static function getDB(): PDO
    {
        return Database::getConnection();
    }

    private static function categoriaSql(): string
    {
        return "CASE
            WHEN d.doc_type = 'politica' THEN 'Políticas'
            WHEN d.doc_type IN ('procedimiento','guia') THEN 'Procesos'
            WHEN d.doc_type = 'formato' THEN 'Formatos'
            WHEN d.doc_type = 'manual' THEN 'Manuales'
            ELSE 'Otros'
        END";
    }

    private static function selectSql(): string
    {
        return "SELECT d.id, d.title, d.code, d.version, d.doc_type,
                       " . self::categoriaSql() . " AS category,
                       COALESCE(dep.name, 'Corporativo') AS process_owner,
                       d.department_id, d.file_path, d.is_downloadable,
                       d.status AS estado, d.valid_from AS validity_date,
                       d.created_at, d.updated_at, d.archived_at, d.archive_reason
                FROM sgc_documents d
                LEFT JOIN departments dep ON dep.id = d.department_id";
    }

    public static function obtenerTodos(string $busqueda = '', string $categoria = '', string $area = '', string $orden = 'recentes'): array
    {
        $sql = self::selectSql() . " WHERE d.status = 'active'";
        $params = [];
        if ($busqueda !== '') {
            $sql .= " AND (d.title LIKE :q_title OR d.code LIKE :q_code OR dep.name LIKE :q_area OR d.doc_type LIKE :q_type)";
            $term = '%' . $busqueda . '%';
            $params['q_title'] = $term;
            $params['q_code'] = $term;
            $params['q_area'] = $term;
            $params['q_type'] = $term;
        }
        if ($categoria !== '') {
            $sql .= ' AND (' . self::categoriaSql() . ') = :category';
            $params['category'] = $categoria;
        }
        if ($area !== '') {
            $sql .= ' AND d.department_id = :department';
            $params['department'] = (int)$area;
        }
        $sql .= $orden === 'alfabetico' ? ' ORDER BY d.title ASC' : ' ORDER BY d.created_at DESC, d.id DESC';
        $stmt = self::getDB()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public static function obtenerObsoletos(): array
    {
        $stmt = self::getDB()->query(self::selectSql() . " WHERE d.status = 'archived' ORDER BY d.archived_at DESC, d.id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public static function obtenerPorId(int $id): ?array
    {
        $stmt = self::getDB()->prepare(self::selectSql() . ' WHERE d.id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public static function obtenerDepartamentos(): array
    {
        return self::getDB()->query('SELECT id, name FROM departments ORDER BY name')->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public static function obtenerResumen(): array
    {
        $stmt = self::getDB()->query("SELECT " . self::categoriaSql() . " AS category, COUNT(*) AS total
            FROM sgc_documents d WHERE d.status = 'active' GROUP BY category");
        $summary = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) ?: [] as $row) {
            $summary[$row['category']] = (int)$row['total'];
        }
        return $summary;
    }

    public static function crear(array $data): bool
    {
        $stmt = self::getDB()->prepare("INSERT INTO sgc_documents
            (code, title, doc_type, department_id, file_path, version, is_downloadable, status, valid_from, created_by)
            VALUES (:code, :title, :doc_type, :department_id, :file_path, :version, :downloadable, 'active', :valid_from, :created_by)");
        return $stmt->execute([
            'code' => $data['code'], 'title' => $data['title'], 'doc_type' => $data['doc_type'],
            'department_id' => $data['department_id'] ?: null, 'file_path' => $data['file_path'],
            'version' => max(1, (int)$data['version']), 'downloadable' => !empty($data['is_downloadable']) ? 1 : 0,
            'valid_from' => $data['valid_from'] ?: null, 'created_by' => $data['created_by'] ?: null
        ]);
    }

    public static function actualizar(array $data): bool
    {
        $sql = 'UPDATE sgc_documents SET code=:code, title=:title, doc_type=:doc_type, department_id=:department_id,
                version=:version, is_downloadable=:downloadable, valid_from=:valid_from';
        $params = [
            'id' => $data['id'], 'code' => $data['code'], 'title' => $data['title'], 'doc_type' => $data['doc_type'],
            'department_id' => $data['department_id'] ?: null, 'version' => max(1, (int)$data['version']),
            'downloadable' => !empty($data['is_downloadable']) ? 1 : 0, 'valid_from' => $data['valid_from'] ?: null
        ];
        if (!empty($data['file_path'])) {
            $sql .= ', file_path=:file_path';
            $params['file_path'] = $data['file_path'];
        }
        $sql .= ' WHERE id=:id AND status=\'active\'';
        return self::getDB()->prepare($sql)->execute($params);
    }

    public static function archivar(int $id, ?int $userId, string $motivo): bool
    {
        $stmt = self::getDB()->prepare("UPDATE sgc_documents SET status='archived', archived_at=NOW(), archived_by=:user, archive_reason=:reason WHERE id=:id AND status='active'");
        return $stmt->execute(['user' => $userId, 'reason' => $motivo, 'id' => $id]);
    }
}
