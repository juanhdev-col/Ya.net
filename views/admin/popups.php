<!DOCTYPE html>
<html lang="es">

<?php 
    $pageTitle = "Ya.Net | Administrador de Pop-ups";
    $baseViews  = dirname(__DIR__);
    $headFile   = $baseViews . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'head.php';
    $navbarFile = $baseViews . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'navbar.php';
    $footerFile = $baseViews . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'footer.php';

    if (file_exists($headFile)) {
        require_once $headFile;
    }
?>

<body class="bg-gray-100 font-sans flex flex-col min-h-screen text-gray-800">

    <?php require_once $navbarFile; ?>

    <main class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full flex-grow space-y-6">

        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-2xl border border-gray-200 shadow-xs">
            <div>
                <h1 class="text-2xl font-black text-yjBlue flex items-center gap-2">
                    <span class="w-3 h-7 bg-yjOrange rounded-full"></span> Gestor de Notificaciones Emergentes (Pop-ups)
                </h1>
                <p class="text-xs text-gray-500 mt-1">Crea y programa notificaciones emergentes por tipo y rango de fecha vigencia.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- FORMULARIO DE CREACIÓN -->
            <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-xs space-y-4 h-fit">
                <h2 class="text-sm font-black text-yjBlue border-b pb-2">➕ Crear Nueva Notificación</h2>

                <form action="/yaconecta/public/admin/popups/guardar" method="POST" enctype="multipart/form-data" class="space-y-3 text-xs">
                    
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Tipo / Categoría de Notificación</label>
                        <select name="tipo" class="w-full px-3 py-2 border rounded-xl focus:ring-2 focus:ring-yjOrange outline-none font-medium">
                            <option value="📄 Publicación de Nuevos Documentos">📄 Publicación de Nuevos Documentos</option>
                            <option value="🔄 Actualización de Documentos">🔄 Actualización de Documentos</option>
                            <option value="📢 Comunicaciones Importantes" selected>📢 Comunicaciones Importantes</option>
                            <option value="📅 Eventos Próximos">📅 Eventos Próximos</option>
                            <option value="📰 Noticias">📰 Noticias</option>
                            <option value="🏆 Reconocimientos">🏆 Reconocimientos</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Título del Aviso</label>
                        <input type="text" name="titulo" required placeholder="Ej: ¡Nuevo Reglamento de Trabajo 2026!" class="w-full px-3 py-2 border rounded-xl focus:ring-2 focus:ring-yjOrange outline-none">
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Mensaje / Contenido</label>
                        <textarea name="contenido" rows="3" required placeholder="Escribe el mensaje detallado..." class="w-full px-3 py-2 border rounded-xl focus:ring-2 focus:ring-yjOrange outline-none"></textarea>
                    </div>

                    <!-- RANGOS DE FECHA Y VIGENCIA -->
                    <div class="grid grid-cols-2 gap-2 p-3 bg-gray-50 rounded-xl border border-gray-200">
                        <div>
                            <label class="block font-bold text-gray-700 mb-1 text-[11px]">Fecha / Hora Inicio</label>
                            <input type="datetime-local" name="fecha_inicio" class="w-full px-2 py-1.5 border rounded-lg text-[11px] outline-none">
                        </div>
                        <div>
                            <label class="block font-bold text-gray-700 mb-1 text-[11px]">Fecha / Hora Fin</label>
                            <input type="datetime-local" name="fecha_fin" class="w-full px-2 py-1.5 border rounded-lg text-[11px] outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Imagen / Banner (Opcional)</label>
                        <input type="file" name="imagen" accept="image/*" class="w-full text-xs text-gray-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:bg-orange-50 file:text-yjOrange font-bold">
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Enlace / Redirección (Opcional)</label>
                        <input type="url" name="link_destino" placeholder="https://..." class="w-full px-3 py-2 border rounded-xl focus:ring-2 focus:ring-yjOrange outline-none">
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Texto del Botón</label>
                        <input type="text" name="texto_boton" value="Ver Más" class="w-full px-3 py-2 border rounded-xl focus:ring-2 focus:ring-yjOrange outline-none">
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Estado</label>
                        <select name="estado" class="w-full px-3 py-2 border rounded-xl focus:ring-2 focus:ring-yjOrange outline-none">
                            <option value="activo">Activo (Publicar)</option>
                            <option value="inactivo">Inactivo (Borrador)</option>
                        </select>
                    </div>

                    <button type="submit" class="w-full bg-yjOrange hover:bg-orange-600 text-white font-bold py-2.5 rounded-xl transition shadow-xs mt-2">
                        Guardar y Programar Notificación
                    </button>
                </form>
            </div>

            <!-- HISTORIAL DE NOTIFICACIONES -->
            <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-gray-200 shadow-xs space-y-4">
                <h2 class="text-sm font-black text-yjBlue border-b pb-2">📋 Histórico de Notificaciones Emergentes</h2>

                <?php if (!empty($popups)): ?>
                    <div class="space-y-3">
                        <?php foreach ($popups as $p): ?>
                            <div class="p-4 rounded-xl border border-gray-100 bg-gray-50 flex items-center justify-between gap-4">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase <?= $p['estado'] === 'activo' ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600' ?>">
                                            <?= $p['estado'] ?>
                                        </span>
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-blue-100 text-yjBlue">
                                            <?= htmlspecialchars($p['tipo'] ?? 'Comunicación') ?>
                                        </span>
                                        <h3 class="font-bold text-xs text-gray-800"><?= htmlspecialchars($p['titulo']) ?></h3>
                                    </div>

                                    <p class="text-[11px] text-gray-500 line-clamp-2"><?= htmlspecialchars($p['contenido']) ?></p>

                                    <!-- MOSTRAR RANGO VIGENCIA -->
                                    <?php if (!empty($p['fecha_inicio']) || !empty($p['fecha_fin'])): ?>
                                        <div class="text-[10px] text-gray-400 font-semibold flex items-center gap-2 pt-1">
                                            <span>🗓️ Vigencia:</span>
                                            <span><?= $p['fecha_inicio'] ? date('d/m/Y H:i', strtotime($p['fecha_inicio'])) : 'Inicio Inmediato' ?></span>
                                            <span>→</span>
                                            <span><?= $p['fecha_fin'] ? date('d/m/Y H:i', strtotime($p['fecha_fin'])) : 'Sin Expiración' ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    <a href="/yaconecta/public/admin/popups/estado?id=<?= $p['id'] ?>&accion=toggle" class="p-2 bg-white border border-gray-200 rounded-lg text-xs font-bold hover:bg-gray-100 transition">
                                        <?= $p['estado'] === 'activo' ? '⏸️ Desactivar' : '▶️ Activar' ?>
                                    </a>
                                    <a href="/yaconecta/public/admin/popups/estado?id=<?= $p['id'] ?>&accion=eliminar" onclick="return confirm('¿Eliminar esta notificación?');" class="p-2 bg-red-50 text-red-600 border border-red-100 rounded-lg text-xs font-bold hover:bg-red-100 transition">
                                        🗑️
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-8 text-xs text-gray-400">
                        No hay notificaciones programadas.
                    </div>
                <?php endif; ?>
            </div>

        </div>

    </main>

    <?php require_once $footerFile; ?>

</body>
</html>