<?php

namespace App\Controllers;

use Config\Database;

class BuscadorController
{
    public function index(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $query = trim($_GET['q'] ?? '');
        
        header('Content-Type: application/json');

        if (empty($query)) {
            echo json_encode(['secciones' => [], 'resultados' => []]);
            exit;
        }

        $db = Database::getConnection();
        $like = '%' . $query . '%';

        // 1. Coincidencias directas con Páginas / Secciones de la Intranet (Navegación Rápida)
        $paginas = [
            ['titulo' => 'Directorio Corporativo', 'url' => '/yaconecta/public/directorio', 'icono' => '📇', 'clave' => 'directorio colaboradores personas telefono celular correo'],
            ['titulo' => 'Gestión Documental', 'url' => '/yaconecta/public/documentos', 'icono' => '📁', 'clave' => 'documentos formatos politicas reglamentos archivos pdf'],
            ['titulo' => 'Clasificados (Entre Nosotros)', 'url' => '/yaconecta/public/clasificados', 'icono' => '🏪', 'clave' => 'clasificados anuncios ventas compras productos'],
            ['titulo' => 'Banco de Ideas (Innovamos)', 'url' => '/yaconecta/public/banco-ideas', 'icono' => '💡', 'clave' => 'ideas banco innovamos sugerencias propuestas'],
            ['titulo' => 'Quiénes Somos (Historia, Misión y Visión)', 'url' => '/yaconecta/public/quienes-somos', 'icono' => '👥', 'clave' => 'quienes somos historia mision vision valores empresa'],
            ['titulo' => 'Galería & Noticias', 'url' => '/yaconecta/public/noticias', 'icono' => '🖼️', 'clave' => 'noticias galeria eventos fotos comunicados']
        ];

        $seccionesCoincidentes = [];
        foreach ($paginas as $pag) {
            if (mb_stripos($pag['titulo'], $query) !== false || mb_stripos($pag['clave'], $query) !== false) {
                $seccionesCoincidentes[] = [
                    'titulo' => $pag['titulo'],
                    'url'    => $pag['url'],
                    'icono'  => $pag['icono']
                ];
            }
        }

        // 2. Coincidencias en Base de Datos (Colaboradores y Documentos)
        $resultadosDB = [];

        try {
            // Buscar Colaboradores
            $stmt = $db->prepare("SELECT id, CONCAT(nombre, ' ', apellido) AS titulo, cargo AS subtitulo FROM colaboradores WHERE estado = 'activo' AND (nombre LIKE :q OR apellido LIKE :q OR cargo LIKE :q OR area LIKE :q) LIMIT 4");
            $stmt->execute([':q' => $like]);
            $colabs = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
            foreach ($colabs as $c) {
                $c['icono'] = '👤';
                $c['url']   = '/yaconecta/public/directorio';
                $resultadosDB[] = $c;
            }

            // Buscar Documentos
            try {
                $stmt = $db->prepare("SELECT id, titulo, categoria AS subtitulo FROM documentos WHERE titulo LIKE :q OR descripcion LIKE :q OR categoria LIKE :q LIMIT 4");
                $stmt->execute([':q' => $like]);
                $docs = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
                foreach ($docs as $d) {
                    $d['icono'] = '📄';
                    $d['url']   = '/yaconecta/public/documentos';
                    $resultadosDB[] = $d;
                }
            } catch (\Throwable $e) {}

        } catch (\Throwable $e) {
            error_log("Error en BuscadorController API: " . $e->getMessage());
        }

        echo json_encode([
            'secciones'  => $seccionesCoincidentes,
            'resultados' => $resultadosDB
        ]);
        exit;
    }
}