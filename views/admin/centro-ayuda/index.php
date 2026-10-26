<?php
// views/admin/centro-ayuda/index.php

// Retroceder 3 niveles desde views/admin/centro-ayuda para llegar a la raíz del proyecto
$baseDir = dirname(__DIR__, 3);
require_once $baseDir . '/views/partials/head.php';
require_once $baseDir . '/views/partials/navbar.php';
?>

<main class="p-4 sm:p-6 lg:p-8 max-w-7xl mx-auto space-y-8">

    <!-- Encabezado Admin -->
    
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl p-6 sm:p-8 text-white shadow-lg flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <span class="inline-block px-3 py-1 bg-white/20 backdrop-blur-md text-white rounded-full text-xs font-semibold tracking-wide uppercase mb-2">
                Panel de Gestión
            </span>
            <h1 class="text-2xl sm:text-3xl font-bold">Administración - Centro de Ayuda</h1>
            <p class="text-blue-100 text-xs sm:text-sm mt-1">Gestiona preguntas frecuentes, solicitudes recibidas e integrantes de comités.</p>
        </div>
        <a href="/yaconecta/public/centro-ayuda" class="px-4 py-2 bg-white/10 hover:bg-white/20 border border-white/20 text-white rounded-xl text-xs font-semibold transition flex items-center gap-2 shrink-0">
            <i class="fa-solid fa-arrow-left"></i> Volver a Vista Colaborador
        </a>
    </div>

    <!-- Pestañas de Gestión -->
    <div class="flex flex-wrap border-b border-slate-200 bg-white p-2 rounded-2xl shadow-sm gap-2">
        <button onclick="switchAdminTab('solicitudes')" id="atab-solicitudes" class="atab-btn flex-1 min-w-[150px] py-2.5 px-4 rounded-xl font-medium text-xs transition bg-blue-600 text-white shadow">
            <i class="fa-solid fa-inbox mr-1"></i> Solicitudes Recibidas (<?= count($solicitudes ?? []) ?>)
        </button>
        <button onclick="switchAdminTab('faqs')" id="atab-faqs" class="atab-btn flex-1 min-w-[150px] py-2.5 px-4 rounded-xl font-medium text-xs transition text-slate-600 hover:bg-slate-100">
            <i class="fa-solid fa-list-check mr-1"></i> Preguntas Frecuentes (<?= count($faqs ?? []) ?>)
        </button>
    </div>

    <!-- SECCIÓN 1: SOLICITUDES RECIBIDAS DE COLABORADORES -->
    <div id="acontent-solicitudes" class="atab-content space-y-4">
        <?php if (empty($solicitudes)): ?>
            <div class="bg-white p-8 text-center rounded-2xl border border-slate-200 text-slate-500 text-xs">
                No hay solicitudes recibidas pendientes de revisión.
            </div>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($solicitudes as $sol): ?>
                    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-3">
                        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 pb-3">
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-1 bg-slate-100 text-slate-700 rounded-md text-[10px] font-bold uppercase">
                                    <?= $sol['comite'] === 'general' ? 'Pregunta General' : 'Comité ' . strtoupper($sol['comite']) ?>
                                </span>
                                <span class="text-xs text-slate-400">| Por: <?= htmlspecialchars($sol['nombre_remitente']) ?></span>
                            </div>
                            <span class="px-3 py-1 rounded-full text-[11px] font-semibold 
                                <?= $sol['estado'] === 'pendiente' ? 'bg-amber-100 text-amber-800' : '' ?>
                                <?= $sol['estado'] === 'resuelto' ? 'bg-emerald-100 text-emerald-800' : '' ?>">
                                <?= ucfirst($sol['estado']) ?>
                            </span>
                        </div>

                        <h4 class="font-bold text-slate-800 text-sm"><?= htmlspecialchars($sol['asunto']) ?></h4>
                        <p class="text-xs text-slate-600 bg-slate-50 p-3 rounded-xl"><?= nl2br(htmlspecialchars($sol['mensaje'])) ?></p>

                        <!-- Formulario de Respuesta Directa -->
                        <form action="/yaconecta/public/admin/centro-ayuda/responder" method="POST" class="mt-4 pt-3 border-t border-slate-100 space-y-3">
                            <input type="hidden" name="id" value="<?= $sol['id'] ?>">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 mb-1">Responder al Colaborador</label>
                                <textarea name="respuesta_admin" rows="2" required placeholder="Escribe la respuesta oficial..." class="w-full text-xs p-3 border border-slate-300 rounded-xl outline-none focus:ring-2 focus:ring-blue-500"><?= htmlspecialchars($sol['respuesta_admin'] ?? '') ?></textarea>
                            </div>

                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div class="flex items-center gap-2">
                                    <input type="checkbox" name="publicar_faq" value="1" id="pubFaq_<?= $sol['id'] ?>" class="rounded text-blue-600">
                                    <label for="pubFaq_<?= $sol['id'] ?>" class="text-xs text-slate-700 font-medium">Publicar también como FAQ General</label>
                                </div>
                                <div class="flex items-center gap-2">
                                    <select name="estado" class="text-xs p-2 border border-slate-300 rounded-lg outline-none">
                                        <option value="resuelto" <?= $sol['estado'] === 'resuelto' ? 'selected' : '' ?>>Resuelto</option>
                                        <option value="en_revision" <?= $sol['estado'] === 'en_revision' ? 'selected' : '' ?>>En Revisión</option>
                                        <option value="pendiente" <?= $sol['estado'] === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                                    </select>
                                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold shadow">
                                        Guardar Respuesta
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- SECCIÓN 2: GESTIÓN DE FAQS -->
    <div id="acontent-faqs" class="atab-content hidden space-y-6">
        <!-- Formulario Crear FAQ -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <h3 class="text-sm font-bold text-slate-800 mb-4">Crear Nueva Pregunta Frecuente</h3>
            <form action="/yaconecta/public/admin/centro-ayuda/faq/save" method="POST" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Categoría</label>
                        <input type="text" name="categoria" required placeholder="Ej: Gestión Humana, TI, SGC..." class="w-full text-xs p-3 border border-slate-300 rounded-xl outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Estado</label>
                        <select name="estado" class="w-full text-xs p-3 border border-slate-300 rounded-xl outline-none">
                            <option value="activo">Activo (Visible)</option>
                            <option value="inactivo">Inactivo (Oculto)</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Pregunta</label>
                    <input type="text" name="pregunta" required placeholder="¿Cómo solicito...?" class="w-full text-xs p-3 border border-slate-300 rounded-xl outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Respuesta</label>
                    <textarea name="respuesta" rows="3" required placeholder="Instrucciones paso a paso..." class="w-full text-xs p-3 border border-slate-300 rounded-xl outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold shadow">
                        Guardar FAQ
                    </button>
                </div>
            </form>
        </div>

        <!-- Tabla de FAQs -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-700 uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="p-4">Categoría</th>
                        <th class="p-4">Pregunta</th>
                        <th class="p-4">Estado</th>
                        <th class="p-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($faqs ?? [] as $f): ?>
                        <tr class="hover:bg-slate-50">
                            <td class="p-4 font-bold text-slate-800"><?= htmlspecialchars($f['categoria']) ?></td>
                            <td class="p-4"><?= htmlspecialchars($f['pregunta']) ?></td>
                            <td class="p-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold <?= $f['estado'] === 'activo' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' ?>">
                                    <?= ucfirst($f['estado']) ?>
                                </span>
                            </td>
                            <td class="p-4 text-right">
                                <a href="/yaconecta/public/admin/centro-ayuda/faq/delete?id=<?= $f['id'] ?>" onclick="return confirm('¿Eliminar esta pregunta?')" class="text-rose-600 hover:underline font-semibold">
                                    Eliminar
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</main>

<script>
function switchAdminTab(tab) {
    document.querySelectorAll('.atab-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.atab-btn').forEach(btn => {
        btn.classList.remove('bg-blue-600', 'text-white', 'shadow');
        btn.classList.add('text-slate-600', 'hover:bg-slate-100');
    });

    document.getElementById('acontent-' + tab).classList.remove('hidden');
    const active = document.getElementById('atab-' + tab);
    active.classList.remove('text-slate-600', 'hover:bg-slate-100');
    active.classList.add('bg-blue-600', 'text-white', 'shadow');
}
</script>

<?php require_once $baseDir . '/views/partials/footer.php'; ?>