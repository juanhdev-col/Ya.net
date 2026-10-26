<?php
// views/centro-ayuda/index.php

$baseDir = dirname(__DIR__, 2);
require_once $baseDir . '/views/partials/head.php';
require_once $baseDir . '/views/partials/navbar.php';
?>

<!-- Contenedor Principal del Dashboard -->
<main class="p-4 sm:p-6 lg:p-8 max-w-7xl mx-auto space-y-8">

    <!-- Alertas Flash -->
    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'solicitud_enviada'): ?>
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-emerald-600 text-xl"></i>
                <span class="text-sm font-medium">¡Tu pregunta o caso ha sido enviado exitosamente! Puedes hacerle seguimiento en la pestaña "Mis Solicitudes".</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    <?php endif; ?>

    <!-- Encabezado del Módulo -->
    <div class="bg-gradient-to-r from-blue-700 to-indigo-800 rounded-2xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="relative z-10 max-w-2xl">
            <span class="inline-block px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-xs font-semibold tracking-wide uppercase mb-3">
                Centro de Ayuda
            </span>
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight">¿Necesitas Algo?</h1>
            <p class="mt-2 text-blue-100 text-sm sm:text-base">
                Centralizamos las preguntas más frecuentes de la organización y los canales de apoyo directo a través de nuestros comités corporativos.
            </p>
        </div>

        <!-- Botón de acceso al Panel Administrativo (Solo si es Admin/Líder) -->
        <?php if (($_SESSION['usuario']['role_id'] ?? 3) <= 2): ?>
            <a href="/yaconecta/public/admin/centro-ayuda" class="relative z-10 px-4 py-2.5 bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/20 text-white rounded-xl text-xs font-semibold transition flex items-center gap-2 shrink-0">
                <i class="fa-solid fa-sliders"></i>
                Gestionar Contenido
            </a>
        <?php endif; ?>

        <div class="absolute -right-10 -bottom-10 opacity-10 text-white pointer-events-none">
            <i class="fa-solid fa-handshake-angle text-[220px]"></i>
        </div>
    </div>

    <!-- Navegación por Pestañas (Tabs) -->
    <div class="flex flex-wrap border-b border-slate-200 bg-white p-2 rounded-2xl shadow-sm gap-2">
        <button onclick="switchTab('faq')" id="tab-faq" class="tab-btn flex-1 min-w-[160px] py-3 px-4 rounded-xl font-medium text-sm transition-all duration-200 flex items-center justify-center gap-2 bg-blue-600 text-white shadow">
            <i class="fa-solid fa-circle-question"></i>
            Preguntas Frecuentes
        </button>
        <button onclick="switchTab('copasst')" id="tab-copasst" class="tab-btn flex-1 min-w-[160px] py-3 px-4 rounded-xl font-medium text-sm transition-all duration-200 flex items-center justify-center gap-2 text-slate-600 hover:bg-slate-100">
            <i class="fa-solid fa-hard-hat"></i>
            Comité COPASST
        </button>
        <button onclick="switchTab('convivencia')" id="tab-convivencia" class="tab-btn flex-1 min-w-[160px] py-3 px-4 rounded-xl font-medium text-sm transition-all duration-200 flex items-center justify-center gap-2 text-slate-600 hover:bg-slate-100">
            <i class="fa-solid fa-people-holding-hands"></i>
            Convivencia Laboral
        </button>
        <button onclick="switchTab('mis-casos')" id="tab-mis-casos" class="tab-btn flex-1 min-w-[160px] py-3 px-4 rounded-xl font-medium text-sm transition-all duration-200 flex items-center justify-center gap-2 text-slate-600 hover:bg-slate-100">
            <i class="fa-solid fa-lock text-indigo-600"></i>
            Mis Solicitudes (<?= count($misSolicitudes ?? []) ?>)
        </button>
    </div>

    <!-- ========================================== -->
    <!-- CONTENIDO 1: PREGUNTAS FRECUENTES (FAQ) -->
    <!-- ========================================== -->
    <div id="content-faq" class="tab-content">
        <!-- Buscador de FAQ + Botón para Hacer Pregunta -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4 mb-8">
            <form method="GET" action="/yaconecta/public/centro-ayuda" class="flex-1 max-w-xl">
                <div class="relative">
                    <input type="text" name="q" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" 
                           placeholder="Buscar una duda o palabra clave (ej: permisos, certificados, VPN)..." 
                           class="w-full pl-12 pr-28 py-3.5 bg-white border border-slate-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    <i class="fa-solid fa-magnifying-glass absolute left-4 top-4 text-slate-400"></i>
                    <button type="submit" class="absolute right-2 top-2 px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold transition">
                        Buscar
                    </button>
                </div>
            </form>

            <button onclick="openSolicitudModal('general')" class="px-5 py-3.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-xl shadow transition flex items-center justify-center gap-2 shrink-0">
                <i class="fa-solid fa-circle-plus text-blue-400"></i>
                Hacer una Pregunta
            </button>
        </div>

        <?php if (empty($faqsGrouped)): ?>
            <div class="bg-white rounded-2xl p-8 text-center border border-slate-200 shadow-sm">
                <i class="fa-solid fa-folder-open text-4xl text-slate-300 mb-3"></i>
                <h3 class="text-base font-semibold text-slate-700">No se encontraron preguntas frecuentes</h3>
                <p class="text-xs text-slate-500 mt-1 mb-4">¿No encontraste lo que buscabas? Envía tu inquietud directamente al equipo correspondiente.</p>
                <button onclick="openSolicitudModal('general')" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl transition inline-flex items-center gap-2">
                    <i class="fa-solid fa-paper-plane"></i>
                    Enviar mi Pregunta
                </button>
            </div>
        <?php else: ?>
            <div class="space-y-6">
                <?php foreach ($faqsGrouped as $categoria => $preguntas): ?>
                    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
                        <h2 class="text-lg font-bold text-slate-800 mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                            <span class="w-3 h-3 bg-blue-600 rounded-full inline-block"></span>
                            <?= htmlspecialchars($categoria) ?>
                        </h2>
                        <div class="space-y-3">
                            <?php foreach ($preguntas as $faq): ?>
                                <div class="border border-slate-200 rounded-xl overflow-hidden transition-all">
                                    <button onclick="toggleFaq('faq-item-<?= $faq['id'] ?>')" 
                                            class="w-full text-left p-4 bg-slate-50/50 hover:bg-slate-100/80 font-medium text-slate-800 text-sm flex items-center justify-between gap-4 transition">
                                        <span><?= htmlspecialchars($faq['pregunta']) ?></span>
                                        <i id="icon-faq-item-<?= $faq['id'] ?>" class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200"></i>
                                    </button>
                                    <div id="faq-item-<?= $faq['id'] ?>" class="hidden p-4 bg-white text-slate-600 text-sm border-t border-slate-100 leading-relaxed">
                                        <?= nl2br(htmlspecialchars($faq['respuesta'])) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- ========================================== -->
    <!-- CONTENIDO 2: COMITÉ COPASST -->
    <!-- ========================================== -->
    <div id="content-copasst" class="tab-content hidden space-y-6">
        <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
            <div class="flex items-start gap-4">
                <div class="p-4 bg-amber-50 text-amber-600 rounded-2xl">
                    <i class="fa-solid fa-hard-hat text-3xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-800">Comité Paritario de Seguridad y Salud en el Trabajo (COPASST)</h2>
                    <p class="text-sm text-slate-600 mt-1 leading-relaxed">
                        Encargado de promover la salud ocupacional, la prevención de riesgos laborales y vigilar el cumplimiento de las normas de seguridad en YA JUEGO.
                    </p>
                </div>
            </div>
            <button onclick="openSolicitudModal('copasst')" class="px-5 py-3 bg-amber-600 hover:bg-amber-700 text-white font-semibold text-xs rounded-xl shadow transition flex items-center gap-2 shrink-0">
                <i class="fa-solid fa-paper-plane"></i>
                Enviar Reporte a COPASST
            </button>
        </div>

        <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Integrantes del Comité</h3>

        <?php if (empty($copasstMembers)): ?>
            <div class="bg-white rounded-2xl p-8 text-center border border-slate-200 shadow-sm">
                <p class="text-sm text-slate-500">No hay integrantes registrados actualmente para el comité COPASST.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($copasstMembers as $member): ?>
                    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm hover:shadow-md transition">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-full bg-slate-100 overflow-hidden flex-shrink-0 border border-slate-200">
                                <img src="/yaconecta/public/uploads/photos/<?= htmlspecialchars($member['foto'] ?: 'default_avatar.png') ?>" 
                                     alt="Foto" class="w-full h-full object-cover"
                                     onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($member['nombre_completo']) ?>&background=0D8ABC&color=fff'">
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-800 text-sm"><?= htmlspecialchars($member['nombre_completo']) ?></h4>
                                <p class="text-xs text-blue-600 font-semibold"><?= htmlspecialchars($member['rol_comite']) ?></p>
                                <p class="text-xs text-slate-500 mt-0.5"><?= htmlspecialchars($member['cargo_empresa'] ?: 'Colaborador') ?></p>
                            </div>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-600">
                            <span class="bg-slate-100 px-2.5 py-1 rounded-full text-[11px] font-medium text-slate-600">
                                Rep: <?= htmlspecialchars($member['representacion']) ?>
                            </span>
                            <?php if (!empty($member['correo'])): ?>
                                <a href="mailto:<?= htmlspecialchars($member['correo']) ?>" class="text-blue-600 hover:underline font-medium flex items-center gap-1">
                                    <i class="fa-regular fa-envelope"></i> Contactar
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- ========================================== -->
    <!-- CONTENIDO 3: COMITÉ DE CONVIVENCIA LABORAL -->
    <!-- ========================================== -->
    <div id="content-convivencia" class="tab-content hidden space-y-6">
        <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
            <div class="flex items-start gap-4">
                <div class="p-4 bg-emerald-50 text-emerald-600 rounded-2xl">
                    <i class="fa-solid fa-people-holding-hands text-3xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-800">Comité de Convivencia Laboral</h2>
                    <p class="text-sm text-slate-600 mt-1 leading-relaxed">
                        Espacio de diálogo y mediación destinado a prevenir el acoso laboral, generar un ambiente de trabajo armónico y proteger la salud mental.
                    </p>
                </div>
            </div>
            <button onclick="openSolicitudModal('convivencia')" class="px-5 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs rounded-xl shadow transition flex items-center gap-2 shrink-0">
                <i class="fa-solid fa-shield-halved"></i>
                Enviar Caso Confidencial
            </button>
        </div>

        <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Integrantes del Comité</h3>

        <?php if (empty($convivenciaMembers)): ?>
            <div class="bg-white rounded-2xl p-8 text-center border border-slate-200 shadow-sm">
                <p class="text-sm text-slate-500">No hay integrantes registrados actualmente para el Comité de Convivencia.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($convivenciaMembers as $member): ?>
                    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm hover:shadow-md transition">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-full bg-slate-100 overflow-hidden flex-shrink-0 border border-slate-200">
                                <img src="/yaconecta/public/uploads/photos/<?= htmlspecialchars($member['foto'] ?: 'default_avatar.png') ?>" 
                                     alt="Foto" class="w-full h-full object-cover"
                                     onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($member['nombre_completo']) ?>&background=059669&color=fff'">
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-800 text-sm"><?= htmlspecialchars($member['nombre_completo']) ?></h4>
                                <p class="text-xs text-emerald-600 font-semibold"><?= htmlspecialchars($member['rol_comite']) ?></p>
                                <p class="text-xs text-slate-500 mt-0.5"><?= htmlspecialchars($member['cargo_empresa'] ?: 'Colaborador') ?></p>
                            </div>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-600">
                            <span class="bg-slate-100 px-2.5 py-1 rounded-full text-[11px] font-medium text-slate-600">
                                Rep: <?= htmlspecialchars($member['representacion']) ?>
                            </span>
                            <?php if (!empty($member['correo'])): ?>
                                <a href="mailto:<?= htmlspecialchars($member['correo']) ?>" class="text-emerald-600 hover:underline font-medium flex items-center gap-1">
                                    <i class="fa-regular fa-envelope"></i> Contactar
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- ========================================== -->
    <!-- CONTENIDO 4: MIS SOLICITUDES PRIVADAS -->
    <!-- ========================================== -->
    <div id="content-mis-casos" class="tab-content hidden space-y-6">
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
            <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-lock text-indigo-600"></i> Mis Casos y Preguntas Enviadas
            </h2>
            <p class="text-xs text-slate-500 mt-1">Este espacio es privado. Únicamente tú y el equipo encargado pueden revisar estas inquietudes.</p>
        </div>

        <?php if (empty($misSolicitudes)): ?>
            <div class="bg-white rounded-2xl p-8 text-center border border-slate-200 shadow-sm">
                <i class="fa-solid fa-user-shield text-4xl text-slate-300 mb-3"></i>
                <h3 class="text-base font-semibold text-slate-700">No has enviado preguntas o reportes</h3>
                <p class="text-xs text-slate-500 mt-1">Utiliza el botón de "Hacer una Pregunta" o los formularios de comités para enviar solicitudes.</p>
            </div>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($misSolicitudes as $caso): ?>
                    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
                        <div class="flex items-center justify-between gap-4 border-b border-slate-100 pb-3 mb-3">
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-slate-100 text-slate-600">
                                    <?= $caso['comite'] === 'general' ? 'Pregunta General' : 'Comité ' . strtoupper($caso['comite']) ?>
                                </span>
                                <h4 class="font-bold text-slate-800 text-sm mt-1"><?= htmlspecialchars($caso['asunto']) ?></h4>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold 
                                <?= $caso['estado'] === 'pendiente' ? 'bg-amber-100 text-amber-700' : '' ?>
                                <?= $caso['estado'] === 'en_revision' ? 'bg-blue-100 text-blue-700' : '' ?>
                                <?= $caso['estado'] === 'resuelto' ? 'bg-emerald-100 text-emerald-700' : '' ?>">
                                <?= ucfirst(str_replace('_', ' ', $caso['estado'])) ?>
                            </span>
                        </div>
                        <p class="text-xs text-slate-600 leading-relaxed"><?= nl2br(htmlspecialchars($caso['mensaje'])) ?></p>
                        
                        <?php if (!empty($caso['respuesta_admin'])): ?>
                            <div class="mt-3 p-3 bg-indigo-50 border-l-4 border-indigo-500 rounded-r-xl text-xs text-indigo-900">
                                <strong>Respuesta del Administrador / Comité:</strong>
                                <p class="mt-1"><?= nl2br(htmlspecialchars($caso['respuesta_admin'])) ?></p>
                            </div>
                        <?php endif; ?>

                        <div class="mt-3 pt-2 text-[11px] text-slate-400 flex items-center justify-between">
                            <span>Enviado el: <?= date('d/m/Y h:i A', strtotime($caso['created_at'])) ?></span>
                            <span><?= $caso['es_anonimo'] ? '🔒 Enviado como Anónimo' : '👤 Registrado' ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

</main>

<!-- Modal Formulario para Hacer una Pregunta o Enviar Caso -->
<div id="modalSolicitud" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 relative">
        <button onclick="closeSolicitudModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>

        <h3 id="modalTitle" class="text-lg font-bold text-slate-800 mb-1">Hacer una Pregunta</h3>
        <p class="text-xs text-slate-500 mb-4">Tu duda será enviada a Control Interno / Calidad para ser respondida.</p>

        <form action="/yaconecta/public/centro-ayuda/enviar" method="POST" enctype="multipart/form-data" class="space-y-4">
            <input type="hidden" name="comite" id="formComiteTarget" value="general">

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Tipo de Consulta / Caso</label>
                <select name="tipo_solicitud" required class="w-full text-xs p-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="Pregunta General">Pregunta Frecuente / Inquietud General</option>
                    <option value="SGC y Procesos">Sistema de Gestión de Calidad (SGC)</option>
                    <option value="Condición Insegura">Reporte de Condición Insegura (COPASST)</option>
                    <option value="Convivencia">Consulta de Convivencia Laboral</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Asunto / Pregunta</label>
                <input type="text" name="asunto" required placeholder="Ej: ¿Dónde encuentro la plantilla de viáticos?" class="w-full text-xs p-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Detalle de la Inquietud</label>
                <textarea name="mensaje" rows="4" required placeholder="Explica detalladamente tu duda o requerimiento..." class="w-full text-xs p-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none"></textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Adjuntar Archivo (Opcional)</label>
                <input type="file" name="adjunto" class="w-full text-xs p-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
            </div>

            <div class="flex items-center gap-2 pt-1">
                <input type="checkbox" name="es_anonimo" value="1" id="anonimoCheck" class="rounded text-blue-600 focus:ring-blue-500">
                <label for="anonimoCheck" class="text-xs text-slate-600 font-medium">Enviar de forma anónima / confidencial</label>
            </div>

            <div class="flex justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeSolicitudModal()" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl text-xs font-semibold hover:bg-slate-200">Cancelar</button>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold shadow">Enviar Inquietud</button>
            </div>
        </form>
    </div>
</div>

<!-- JavaScript para Interactividad de Pestañas y Modales -->
<script>
function switchTab(tabName) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('bg-blue-600', 'text-white', 'shadow');
        btn.classList.add('text-slate-600', 'hover:bg-slate-100');
    });

    document.getElementById('content-' + tabName).classList.remove('hidden');
    const activeBtn = document.getElementById('tab-' + tabName);
    activeBtn.classList.remove('text-slate-600', 'hover:bg-slate-100');
    activeBtn.classList.add('bg-blue-600', 'text-white', 'shadow');
}

function toggleFaq(id) {
    const el = document.getElementById(id);
    const icon = document.getElementById('icon-' + id);
    if (el.classList.contains('hidden')) {
        el.classList.remove('hidden');
        if (icon) icon.classList.add('rotate-180');
    } else {
        el.classList.add('hidden');
        if (icon) icon.classList.remove('rotate-180');
    }
}

function openSolicitudModal(comite) {
    document.getElementById('formComiteTarget').value = comite;
    const modalTitle = document.getElementById('modalTitle');
    
    if (comite === 'copasst') {
        modalTitle.innerText = 'Enviar Reporte / Caso a COPASST';
    } else if (comite === 'convivencia') {
        modalTitle.innerText = 'Enviar Caso al Comité de Convivencia';
    } else {
        modalTitle.innerText = 'Hacer una Pregunta / Inquietud';
    }

    document.getElementById('modalSolicitud').classList.remove('hidden');
    document.getElementById('modalSolicitud').classList.add('flex');
}

function closeSolicitudModal() {
    document.getElementById('modalSolicitud').classList.add('hidden');
    document.getElementById('modalSolicitud').classList.remove('flex');
}
</script>

<?php
require_once $baseDir . '/views/partials/footer.php';
?>