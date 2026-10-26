<?php

namespace App\Controllers;

use App\Models\Colaborador;

class DirectorioController
{
    public function index(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        foreach ([
            ['nombre' => 'Juan Andrés', 'apellido' => 'Henao', 'cargo' => 'Analista de control interno y calidad', 'area' => 'Control Interno y Calidad'],
            ['nombre' => 'Andrea', 'apellido' => 'Cortes', 'cargo' => 'Aprendiz SENA', 'area' => 'Control Interno y Calidad'],
            ['nombre' => 'Yeimy', 'apellido' => 'Cortés', 'cargo' => 'Jefe de Control Interno y Calidad', 'area' => 'Control Interno y Calidad'],
            ['nombre' => 'Diego', 'apellido' => 'Chacón', 'cargo' => 'Jefe de Operaciones', 'area' => 'Operaciones'],
        ] as $perfilInicial) {
            Colaborador::agregarSiNoExiste($perfilInicial);
        }

        $q = trim($_GET['q'] ?? '');
        $area = trim($_GET['area'] ?? '');
        $cargo = trim($_GET['cargo'] ?? '');
        $colaboradores = Colaborador::buscar($q, $area, $cargo);
        $filtrosDirectorio = Colaborador::obtenerAreasYcargos();
        $totalColaboradores = count(Colaborador::buscar());
        $totalActivos = $totalColaboradores;
        $totalInactivos = 0;
        if (!$colaboradores) {
            $colaboradores = [
                ['nombre'=>'Juan Andrés','apellido'=>'Henao','cargo'=>'Analista de control interno y calidad','area'=>'Control Interno y Calidad','email'=>'','telefono'=>'','extension'=>'','foto'=>'','estado'=>'activo'],
                ['nombre'=>'Andrea','apellido'=>'Cortes','cargo'=>'Aprendiz SENA','area'=>'Control Interno y Calidad','email'=>'','telefono'=>'','extension'=>'','foto'=>'','estado'=>'activo'],
                ['nombre'=>'Yeimy','apellido'=>'Cortés','cargo'=>'Jefe de Control Interno y Calidad','area'=>'Control Interno y Calidad','email'=>'','telefono'=>'','extension'=>'','foto'=>'','estado'=>'activo'],
                ['nombre'=>'Diego','apellido'=>'Chacón','cargo'=>'Jefe de Operaciones','area'=>'Operaciones','email'=>'','telefono'=>'','extension'=>'','foto'=>'','estado'=>'activo'],
            ];
            if ($q !== '') $colaboradores = array_values(array_filter($colaboradores, static fn(array $c): bool => stripos(implode(' ', $c), $q) !== false));
            if ($area !== '') $colaboradores = array_values(array_filter($colaboradores, static fn(array $c): bool => $c['area'] === $area));
            if ($cargo !== '') $colaboradores = array_values(array_filter($colaboradores, static fn(array $c): bool => $c['cargo'] === $cargo));
            $filtrosDirectorio = ['areas'=>['Control Interno y Calidad','Operaciones'],'cargos'=>array_column($colaboradores, 'cargo')];
            $filtrosDirectorio['cargos'] = array_values(array_unique(array_merge($filtrosDirectorio['cargos'], ['Analista de control interno y calidad','Aprendiz SENA','Jefe de Control Interno y Calidad','Jefe de Operaciones'])));
            $totalColaboradores = 4;
            $totalActivos = 4;
        }

        $ds = DIRECTORY_SEPARATOR;
        $vista = dirname(__DIR__, 2) . $ds . 'views' . $ds . 'directorio' . $ds . 'index.php';

        if (file_exists($vista)) {
            require_once $vista;
        } else {
            echo "Vista del directorio no encontrada.";
        }
    }
}
