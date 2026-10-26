<?php

namespace App\Controllers;

class AdminController
{
    public function index()
    {
        // Datos maquetados para la vista previa del Dashboard
        $stats = [
            'total_documentos'    => 24,
            'total_noticias'      => 12,
            'total_interacciones' => 158,
            'usuarios_activos'    => 45
        ];

        // Lista de actividades recientes de usuarios (Log Stream)
        $actividades = [
            [
                'usuario'        => 'Carlos Rodríguez',
                'email'          => 'carlos.rodriguez@yajuego.co',
                'iniciales'      => 'CR',
                'color'          => 'bg-blue-100 text-yjBlue',
                'accion'         => '📥 Descarga Formato',
                'tipo_badge'     => 'bg-orange-50 text-yjOrange',
                'recurso'        => 'CPR-FOR-002 Requisición de Compra',
                'ip_dispositivo' => 'Desktop (192.168.1.45)',
                'tiempo'         => 'Hace 5 minutos'
            ],
            [
                'usuario'        => 'Ana María Gómez',
                'email'          => 'ana.gomez@yajuego.co',
                'iniciales'      => 'AM',
                'color'          => 'bg-purple-100 text-purple-700',
                'accion'         => '👁️ Leyó Noticia',
                'tipo_badge'     => 'bg-blue-50 text-yjBlue',
                'recurso'        => 'Lanzamiento Nueva Plataforma Auditoría',
                'ip_dispositivo' => 'Mobile (192.168.1.88)',
                'tiempo'         => 'Hace 18 minutos'
            ],
            [
                'usuario'        => 'Juan Manuel Pérez',
                'email'          => 'juan.perez@yajuego.co',
                'iniciales'      => 'JM',
                'color'          => 'bg-green-100 text-green-700',
                'accion'         => '🔗 Acceso a App',
                'tipo_badge'     => 'bg-green-50 text-green-700',
                'recurso'        => 'BUK (Plataforma HR)',
                'ip_dispositivo' => 'Desktop (192.168.1.12)',
                'tiempo'         => 'Hace 42 minutos'
            ],
            [
                'usuario'        => 'Laura Vargas',
                'email'          => 'laura.vargas@yajuego.co',
                'iniciales'      => 'LV',
                'color'          => 'bg-gray-200 text-gray-700',
                'accion'         => '🔑 Inicio de Sesión',
                'tipo_badge'     => 'bg-gray-100 text-gray-700',
                'recurso'        => 'Intranet YA CONECTA',
                'ip_dispositivo' => 'Desktop (192.168.1.90)',
                'tiempo'         => 'Hoy, 08:15 AM'
            ]
        ];

        $ds = DIRECTORY_SEPARATOR;
        // Apunta directamente a dashboard.php dentro de views/admin/
        $vista = dirname(__DIR__, 2) . $ds . 'views' . $ds . 'admin' . $ds . 'dashboard.php';

        if (file_exists($vista)) {
            require_once $vista;
        } else {
            echo "Vista admin no encontrada en: " . $vista;
        }
    }
}