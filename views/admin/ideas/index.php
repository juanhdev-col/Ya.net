<!DOCTYPE html>
<html lang="es">

<?php 
    $pageTitle = "Ya.Net | Gestión Banco de Ideas";
    require_once dirname(__DIR__, 2) . '/partials/head.php'; 
?>

<body class="bg-gray-100 font-sans flex flex-col min-h-screen text-gray-800">

    <?php require_once dirname(__DIR__, 2) . '/partials/navbar.php'; ?>

    <main class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full flex-grow space-y-6">
        
        <!-- ALERTAS -->
        <?php if (isset($_GET['msg']) && $_GET['msg'] === 'updated'): ?>
            <div class="p-4 bg-blue-100 border border-blue-300 text-blue-800 rounded-xl text-xs font-bold">
                ✏️ Estado de la idea actualizado correctamente.
            </div>
        <?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
            <div class="p-4 bg-red-100 border border-red-300 text-red-800 rounded-xl text-xs font-bold">
                🗑️ Registro de propuesta eliminado.
            </div>
        <?php endif; ?>

        <!-- CABECERA DE SECCIÓN -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-yjBlue flex items-center gap-2">
                    <span class="w-3 h-7 bg-yjOrange rounded-full"></span> Banco de Ideas e Innovación
                </h1>
                <p class="text-xs text-gray-500 mt-1">Revisa y evalúa las sugerencias e iniciativas propuestas por los colaboradores.</p>
            </div>

            <form action="/yaconecta/public/admin/ideas" method="GET" class="flex gap-2 w-full md:w-80">
                <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" 
                       placeholder="Buscar por título o contenido..." 
                       class="w-full px-4 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-yjOrange outline-none">
                <button type="submit" class="bg-yjOrange hover:bg-orange-600 text-white font-bold text-xs px-5 py-2 rounded-xl transition cursor-pointer">
                    Buscar
                </button>
            </form>
        </div>

        <!-- TABLA DE IDEAS -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-[11px] uppercase tracking-wider text-gray-500 font-bold">
                            <th class="p-4">Propuesta / Título</th>
                            <th class="p-4">Colaborador / Área</th>
                            <th class="p-4">Fecha</th>
                            <th class="p-4">Estado</th>
                            <th class="p-4 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-xs">
                        <?php if (!empty($ideas)): ?>
                            <?php foreach ($ideas as $i): ?>
                                <tr class="hover:bg-gray-50/80 transition">
                                    <!-- COLUMNA 1: PROPUESTA / TÍTULO -->
                                    <td class="p-4 max-w-md">
                                        <div class="font-bold text-gray-800 text-sm"><?= htmlspecialchars($i['titulo']) ?></div>
                                        <p class="text-xs text-gray-500 mt-1 line-clamp-2"><?= htmlspecialchars($i['descripcion'] ?? '') ?></p>
                                        
                                        <?php if (!empty($i['adjunto'])): ?>
                                            <a href="/yaconecta/public/uploads/ideas/<?= htmlspecialchars($i['adjunto']) ?>" target="_blank" class="inline-flex items-center gap-1 text-[11px] font-bold text-yjOrange hover:underline mt-2">
                                                📎 Ver Archivo Adjunto
                                            </a>
                                        <?php endif; ?>
                                    </td>

                                    <!-- COLUMNA 2: COLABORADOR / ÁREA -->
                                    <td class="p-4">
                                        <div class="font-bold text-gray-700">
                                            <?= htmlspecialchars(($i['colaborador_nombre'] ?? 'Anónimo') . ' ' . ($i['colaborador_apellido'] ?? '')) ?>
                                        </div>
                                        <span class="bg-blue-50 text-yjBlue font-bold text-[10px] px-2.5 py-0.5 rounded-full uppercase">
                                            <?= htmlspecialchars($i['colaborador_area'] ?? 'General') ?>
                                        </span>
                                    </td>

                                    <!-- COLUMNA 3: FECHA -->
                                    <td class="p-4 text-gray-500 text-[11px]">
                                        <?= htmlspecialchars($i['created_at'] ?? 'N/A') ?>
                                    </td>

                                    <!-- COLUMNA ESTADO -->
                                    <td class="p-4">
                                        <?php 
                                            $estadoTexto = !empty($i['estado']) ? $i['estado'] : 'Pendiente';
                                            $st = strtolower(trim($estadoTexto));
                                            $badgeClass = 'bg-yellow-100 text-yellow-800 border border-yellow-300';
                                            if ($st === 'aprobada' || $st === 'aprobado') $badgeClass = 'bg-green-100 text-green-800 border border-green-300';
                                            if ($st === 'rechazada' || $st === 'rechazado') $badgeClass = 'bg-red-100 text-red-800 border border-red-300';
                                            if ($st === 'en revision' || $st === 'en revisión') $badgeClass = 'bg-blue-100 text-blue-800 border border-blue-300';
                                        ?>
                                        <span class="inline-flex items-center font-black text-[10px] px-3 py-1 rounded-full uppercase <?= $badgeClass ?>">
                                            <?= htmlspecialchars($estadoTexto) ?>
                                        </span>
                                    </td>

                                    <!-- COLUMNA 5: ACCIONES -->
                                    <td class="p-4 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <button onclick='evaluarIdea(<?= json_encode($i, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' 
                                                    class="p-1.5 bg-blue-50 text-yjBlue hover:bg-blue-100 rounded-lg text-xs font-bold transition" title="Evaluar / Cambiar Estado">
                                                ✏️ Evaluar
                                            </button>

                                            <a href="/yaconecta/public/admin/ideas/delete?id=<?= $i['id'] ?>" onclick="return confirm('¿Seguro que deseas eliminar esta propuesta?');" 
                                               class="p-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg text-xs font-bold transition" title="Eliminar">
                                                🗑️
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="p-8 text-center text-gray-400 font-bold">
                                    No hay ideas o propuestas registradas actualmente.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <!-- MODAL EVALUAR IDEA -->
    <div id="modalIdea" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-lg overflow-hidden shadow-2xl border border-gray-200">
            <div class="p-4 bg-yjBlue text-white flex justify-between items-center">
                <h3 class="font-bold text-sm">Evaluar Propuesta de Idea</h3>
                <button onclick="cerrarModal()" class="text-white hover:text-gray-300 font-bold text-xs">✖</button>
            </div>
            
            <form action="/yaconecta/public/admin/ideas/update-status" method="POST" class="p-6 space-y-4">
                <input type="hidden" name="id" id="modal_idea_id" value="">

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase">Título</label>
                    <div id="modal_idea_titulo" class="font-bold text-gray-800 text-sm mt-0.5"></div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Estado de Evaluación</label>
                    <select name="estado" id="modal_idea_estado" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl outline-none bg-white font-bold">
                        <option value="Pendiente">Pendiente</option>
                        <option value="En Revision">En Revisión</option>
                        <option value="Aprobada">Aprobada</option>
                        <option value="Rechazada">Rechazada</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Observaciones / Retroalimentación</label>
                    <textarea name="observaciones" id="modal_idea_observaciones" rows="3" placeholder="Comentarios para el colaborador..." class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl outline-none"></textarea>
                </div>

                <div class="pt-2 flex justify-end gap-2">
                    <button type="button" onclick="cerrarModal()" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold text-xs px-4 py-2 rounded-xl transition cursor-pointer">
                        Cancelar
                    </button>
                    <button type="submit" class="bg-yjOrange hover:bg-orange-600 text-white font-bold text-xs px-5 py-2 rounded-xl transition cursor-pointer">
                        Guardar Evaluación
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function evaluarIdea(idea) {
            document.getElementById('modal_idea_id').value = idea.id;
            document.getElementById('modal_idea_titulo').innerText = idea.titulo || '';
            document.getElementById('modal_idea_estado').value = idea.estado || 'Pendiente';
            document.getElementById('modal_idea_observaciones').value = idea.observaciones || '';
            
            document.getElementById('modalIdea').classList.remove('hidden');
        }

        function cerrarModal() {
            document.getElementById('modalIdea').classList.add('hidden');
        }
    </script>

    <?php require_once dirname(__DIR__, 2) . '/partials/footer.php'; ?>

</body>
</html>