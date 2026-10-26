<!DOCTYPE html>
<html lang="es">

<?php 
    $pageTitle = "Ya.Net | Dashboard Administrador";
    require_once __DIR__ . '/../partials/head.php'; 
?>

<!-- Librería CDN Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<body class="bg-gray-100 font-sans flex flex-col min-h-screen text-gray-800">

    <?php require_once __DIR__ . '/../partials/navbar.php'; ?>

    <main class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full flex-grow space-y-8">
        
        <!-- Encabezado Principal -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <span class="text-[10px] font-black uppercase tracking-wider text-yjOrange bg-orange-50 px-2.5 py-1 rounded-full">
                    PANEL DE CONTROL
                </span>
                <h1 class="text-2xl font-black text-yjBlue mt-2 flex items-center gap-2">
                    <span class="w-3 h-7 bg-yjOrange rounded-full"></span> Dashboard General
                </h1>
                <p class="text-xs text-gray-500 mt-0.5">Gestión y monitoreo de interacción en la Intranet Ya.Net.</p>
            </div>

            <!-- Accesos Rápidos a Módulos Admin -->
            <div class="flex flex-wrap items-center gap-2">
                <a href="/yaconecta/public/admin/documentos" class="bg-yjBlue hover:bg-blue-900 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition flex items-center gap-1.5 shadow-xs">
                    <span>📄</span> Documentos
                </a>
                <a href="/yaconecta/public/admin/noticias" class="bg-yjOrange hover:bg-orange-600 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition flex items-center gap-1.5 shadow-xs">
                    <span>📢</span> Noticias
                </a>
                <a href="/yaconecta/public/admin/apps" class="bg-gray-800 hover:bg-gray-900 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition flex items-center gap-1.5 shadow-xs">
                    <span>📱</span> Apps
                </a>
                <a href="/yaconecta/public/admin/directorio" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs px-4 py-2.5 rounded-xl transition flex items-center gap-1.5 border border-gray-200">
                    <span>👥</span> Directorio
                </a>
            </div>
        </div>

        <!-- Tarjetas de Métricas Rápidas (KPIs) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-2xs flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-bold text-gray-400 uppercase">Documentos SGC</p>
                    <p class="text-2xl font-black text-gray-800 mt-1"><?= $stats['total_documentos'] ?? 0 ?></p>
                    <span class="text-[10px] text-green-600 font-bold">↑ 3 subidos esta semana</span>
                </div>
                <div class="w-12 h-12 bg-blue-50 text-yjBlue rounded-2xl flex items-center justify-center text-xl font-bold">
                    📂
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-2xs flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-bold text-gray-400 uppercase">Noticias Publicadas</p>
                    <p class="text-2xl font-black text-gray-800 mt-1"><?= $stats['total_noticias'] ?? 0 ?></p>
                    <span class="text-[10px] text-orange-600 font-bold">2 borradores pendientes</span>
                </div>
                <div class="w-12 h-12 bg-orange-50 text-yjOrange rounded-2xl flex items-center justify-center text-xl font-bold">
                    📰
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-2xs flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-bold text-gray-400 uppercase">Interacciones Hoy</p>
                    <p class="text-2xl font-black text-gray-800 mt-1"><?= $stats['total_interacciones'] ?? 0 ?></p>
                    <span class="text-[10px] text-green-600 font-bold">↑ 18% vs ayer</span>
                </div>
                <div class="w-12 h-12 bg-green-50 text-green-600 rounded-2xl flex items-center justify-center text-xl font-bold">
                    ⚡
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-2xs flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-bold text-gray-400 uppercase">Usuarios Activos</p>
                    <p class="text-2xl font-black text-gray-800 mt-1"><?= $stats['usuarios_activos'] ?? 0 ?></p>
                    <span class="text-[10px] text-gray-400 font-bold">De 60 colaboradores</span>
                </div>
                <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-2xl flex items-center justify-center text-xl font-bold">
                    👥
                </div>
            </div>
        </div>

        <!-- SECCIÓN DE GRÁFICOS INTERACTIVOS -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Gráfico 1: Barras (Interacciones Semanales) -->
            <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-gray-200 shadow-2xs space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <div>
                        <h3 class="text-sm font-black text-yjBlue flex items-center gap-2">
                            <span>📈</span> Interacciones Semanales
                        </h3>
                        <p class="text-[11px] text-gray-400">Total de clics y consultas recibidas en la intranet por día.</p>
                    </div>
                    <span class="text-[10px] bg-gray-100 text-gray-600 font-bold px-2.5 py-1 rounded-lg">Últimos 7 días</span>
                </div>
                <div class="h-64">
                    <canvas id="barChartInteracciones"></canvas>
                </div>
            </div>

            <!-- Gráfico 2: Anillo (Distribución por Módulo) -->
            <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-2xs space-y-4">
                <div class="border-b border-gray-100 pb-3">
                    <h3 class="text-sm font-black text-yjBlue flex items-center gap-2">
                        <span>🍩</span> Actividad por Módulo
                    </h3>
                    <p class="text-[11px] text-gray-400">Uso porcentual de las diferentes secciones.</p>
                </div>
                <div class="h-64 flex items-center justify-center">
                    <canvas id="doughnutChartModulos"></canvas>
                </div>
            </div>

        </div>

        <!-- Módulo de Actividad e Interacción por Usuario (Tabla) -->
        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-2xs space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-100 pb-4">
                <div>
                    <h2 class="text-lg font-black text-yjBlue flex items-center gap-2">
                        <span>📊</span> Registro Reciente de Usuarios
                    </h2>
                    <p class="text-xs text-gray-500 mt-0.5">Trazabilidad en tiempo real de acciones y descargas dentro del portal.</p>
                </div>

                <div class="flex items-center gap-2">
                    <select class="px-3 py-2 text-xs border border-gray-300 rounded-xl bg-white focus:ring-2 focus:ring-yjOrange outline-none font-medium">
                        <option>Todas las acciones</option>
                        <option>Descargas de Documentos</option>
                        <option>Lectura de Noticias</option>
                        <option>Acceso a Apps</option>
                        <option>Inicio de Sesión</option>
                    </select>
                    
                    <input type="text" placeholder="Buscar por usuario..." class="px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-yjOrange outline-none w-48">
                </div>
            </div>

            <!-- Tabla de Registro de Actividad -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-gray-600">
                    <thead class="bg-gray-50 text-gray-700 font-bold border-b border-gray-200 uppercase text-[10px]">
                        <tr>
                            <th class="p-3.5">Usuario / Colaborador</th>
                            <th class="p-3.5">Acción Realizada</th>
                            <th class="p-3.5">Módulo / Recurso</th>
                            <th class="p-3.5">Dispositivo / IP</th>
                            <th class="p-3.5 text-right">Fecha y Hora</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 font-medium">
                        <?php if (!empty($actividades)): ?>
                            <?php foreach ($actividades as $act): ?>
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="p-3.5 flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-full <?= $act['color'] ?> font-black text-xs flex items-center justify-center shrink-0">
                                            <?= htmlspecialchars($act['iniciales']) ?>
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-800"><?= htmlspecialchars($act['usuario']) ?></div>
                                            <div class="text-[10px] text-gray-400"><?= htmlspecialchars($act['email']) ?></div>
                                        </div>
                                    </td>
                                    <td class="p-3.5">
                                        <span class="<?= $act['tipo_badge'] ?> text-[10px] font-bold px-2 py-0.5 rounded-md inline-block">
                                            <?= htmlspecialchars($act['accion']) ?>
                                        </span>
                                    </td>
                                    <td class="p-3.5 font-bold text-gray-700">
                                        <?= htmlspecialchars($act['recurso']) ?>
                                    </td>
                                    <td class="p-3.5 text-gray-400 text-[11px]">
                                        <?= htmlspecialchars($act['ip_dispositivo']) ?>
                                    </td>
                                    <td class="p-3.5 text-right text-gray-400 text-[11px]">
                                        <?= htmlspecialchars($act['tiempo']) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="p-6 text-center text-gray-400">
                                    No hay registros de actividad recientes.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <?php require_once __DIR__ . '/../partials/footer.php'; ?>

    <!-- SCRIPT DE INICIALIZACIÓN DE CHART.JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            
            // 1. Gráfico de Barras - Interacciones Semanales
            const ctxBar = document.getElementById('barChartInteracciones').getContext('2d');
            new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'],
                    datasets: [{
                        label: 'Interacciones',
                        data: [120, 190, 150, 220, 180, 45, 30],
                        backgroundColor: '#F37021', // Naranja Yajuego
                        borderRadius: 8,
                        hoverBackgroundColor: '#d65b12'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#f3f4f6' },
                            ticks: { font: { size: 10 } }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 10 } }
                        }
                    }
                }
            });

            // 2. Gráfico de Anillo / Dona - Actividad por Módulo
            const ctxDoughnut = document.getElementById('doughnutChartModulos').getContext('2d');
            new Chart(ctxDoughnut, {
                type: 'doughnut',
                data: {
                    labels: ['Documentos SGC', 'Noticias', 'Acceso a Apps', 'Directorio'],
                    datasets: [{
                        data: [40, 25, 20, 15],
                        backgroundColor: [
                            '#003366', // Azul Yajuego
                            '#F37021', // Naranja Yajuego
                            '#10B981', // Verde
                            '#8B5CF6'  // Morado
                        ],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                font: { size: 11 },
                                boxWidth: 12,
                                padding: 15
                            }
                        }
                    },
                    cutout: '65%'
                }
            });

        });
    </script>

</body>
</html>