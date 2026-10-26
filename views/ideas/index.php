<!DOCTYPE html>
<html lang="es">

<?php 
    $pageTitle = "Ya.Net | Banco de Ideas e Innovación";
    // Ajusta la profundidad de la carpeta si es necesario (dirname(__DIR__, 1) o ( __DIR__ . '/../partials/head.php' ))
    require_once dirname(__DIR__) . '/partials/head.php'; 
?>

<body class="bg-gray-100 font-sans flex flex-col min-h-screen text-gray-800">

    <?php require_once dirname(__DIR__) . '/partials/navbar.php'; ?>

    <main class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full flex-grow space-y-6">
        
        <?php if (isset($_GET['msg']) && $_GET['msg'] === 'submitted'): ?>
            <div class="p-4 bg-green-100 border border-green-300 text-green-800 rounded-xl text-xs font-bold">
                🚀 ¡Tu idea ha sido enviada con éxito! El equipo evaluador la revisará pronto.
            </div>
        <?php endif; ?>

        <!-- CABECERA -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <span class="bg-orange-100 text-yjOrange text-[10px] font-black tracking-wider uppercase px-3 py-1 rounded-full">
                    INNOVACIÓN PARTICIPATIVA
                </span>
                <h1 class="text-3xl font-black text-yjBlue mt-2">Banco de Ideas</h1>
                <p class="text-xs text-gray-500 mt-1">Comparte tus iniciativas para mejorar procesos, cultura e innovación en la empresa.</p>
            </div>

            <button onclick="toggleFormModal()" class="bg-yjOrange hover:bg-orange-600 text-white font-bold text-xs px-5 py-3 rounded-xl transition shadow-md flex items-center gap-2 cursor-pointer">
                💡 <span>Proponer una Idea</span>
            </button>
        </div>

        <!-- LISTADO DE IDEAS / PROPUESTAS DE LA COMUNIDAD -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php if (!empty($ideasPublicas)): ?>
        <?php foreach ($ideasPublicas as $idea): ?>
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 flex flex-col justify-between hover:shadow-md transition">
                <div class="space-y-3">
                    <div class="flex items-center justify-between gap-2">
                        <span class="bg-blue-50 text-yjBlue text-[10px] font-bold px-2.5 py-1 rounded-full uppercase">
                            <?= htmlspecialchars($idea['categoria'] ?? 'General') ?>
                        </span>
                        
                        <?php 
                            $estadoTexto = !empty($idea['estado']) ? $idea['estado'] : 'Pendiente';
                            $st = strtolower(trim($estadoTexto));
                            $badgeClass = 'bg-yellow-100 text-yellow-800 border border-yellow-300';
                            if ($st === 'aprobada' || $st === 'aprobado') $badgeClass = 'bg-green-100 text-green-800 border border-green-300';
                            if ($st === 'rechazada' || $st === 'rechazado') $badgeClass = 'bg-red-100 text-red-800 border border-red-300';
                            if ($st === 'en revision' || $st === 'en revisión') $badgeClass = 'bg-blue-100 text-blue-800 border border-blue-300';
                        ?>
                        <span class="inline-flex items-center text-[10px] font-black px-2.5 py-1 rounded-full uppercase <?= $badgeClass ?>">
                            <?= htmlspecialchars($estadoTexto) ?>
                        </span>
                    </div>

                    <h3 class="font-black text-gray-800 text-base leading-snug">
                        <?= htmlspecialchars($idea['titulo']) ?>
                    </h3>

                    <p class="text-xs text-gray-600 line-clamp-3">
                        <?= htmlspecialchars($idea['descripcion']) ?>
                    </p>

                    <?php if (!empty($idea['adjunto'])): ?>
                        <div class="mt-3 pt-2 border-t border-gray-100">
                            <a href="/yaconecta/public/uploads/ideas/<?= htmlspecialchars($idea['adjunto']) ?>" target="_blank" class="text-xs font-bold text-yjOrange hover:underline flex items-center gap-1">
                                📎 Ver archivo adjunto
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between text-[11px] text-gray-400">
                    <span class="font-bold text-gray-700">
                        👤 <?= htmlspecialchars(($idea['colaborador_nombre'] ?? 'Carlos') . ' ' . ($idea['colaborador_apellido'] ?? 'Rodríguez')) ?>
                    </span>
                    <span><?= date('d/m/Y', strtotime($idea['created_at'])) ?></span>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-span-full bg-white rounded-2xl p-12 text-center text-gray-400 font-bold border border-gray-200">
            Aún no hay propuestas registradas. ¡Sé el primero en compartir una idea!
        </div>
    <?php endif; ?>
</div>

    </main>

    <!-- MODAL PARA POSTULAR UNA NUEVA IDEA -->
    <div id="modalNuevaIdea" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-lg overflow-hidden shadow-2xl border border-gray-200">
            <div class="p-4 bg-yjBlue text-white flex justify-between items-center">
                <h3 class="font-bold text-sm flex items-center gap-2">💡 Proponer Nueva Idea</h3>
                <button onclick="toggleFormModal()" class="text-white hover:text-gray-300 font-bold text-xs">✖</button>
            </div>
            
            <!-- Cambia el form agregando enctype="multipart/form-data" -->
        <form action="/yaconecta/public/ideas/store" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Título de la Idea</label>
                <input type="text" name="title" required placeholder="Ej: Digitalización del proceso de pañol" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl outline-none focus:ring-2 focus:ring-yjOrange">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Categoría</label>
                <select name="category" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl outline-none bg-white">
                    <option value="Procesos">Optimización de Procesos</option>
                    <option value="Tecnología">Tecnología e Innovación</option>
                    <option value="Cultura">Cultura y Bienestar</option>
                    <option value="Sostenibilidad">Sostenibilidad</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Descripción detallada</label>
                <textarea name="description" rows="4" required placeholder="Explica la problemática, tu propuesta y los beneficios esperados..." class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl outline-none focus:ring-2 focus:ring-yjOrange"></textarea>
            </div>

            <!-- CAMPO NUEVO: ADJUNTO -->
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Adjuntar Archivo o Imagen (Opcional)</label>
                <input type="file" name="adjunto" accept="image/*,.pdf,.doc,.docx" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-orange-50 file:text-yjOrange hover:file:bg-orange-100">
                <p class="text-[10px] text-gray-400 mt-1">Soporta imágenes (PNG, JPG), PDF y documentos Word.</p>
            </div>

            <div class="pt-2 flex justify-end gap-2">
                <button type="button" onclick="toggleFormModal()" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold text-xs px-4 py-2 rounded-xl transition cursor-pointer">
                    Cancelar
                </button>
                <button type="submit" class="bg-yjOrange hover:bg-orange-600 text-white font-bold text-xs px-5 py-2 rounded-xl transition cursor-pointer">
                    Enviar Propuesta
                </button>
            </div>
        </form>
        </div>
    </div>

    <script>
        function toggleFormModal() {
            document.getElementById('modalNuevaIdea').classList.toggle('hidden');
        }
    </script>

    <?php require_once dirname(__DIR__) . '/partials/footer.php'; ?>

</body>
</html>