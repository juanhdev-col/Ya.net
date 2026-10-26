<!DOCTYPE html>
<html lang="es">

<?php 
    $pageTitle = "Ya.Net | Admin - Quiénes Somos";
    require_once dirname(__DIR__, 3) . '/views/partials/head.php'; 

    $historia = $sections['historia'] ?? [];
    $mision = $sections['mision'] ?? [];
    $vision = $sections['vision'] ?? [];
    $cultura = $sections['cultura'] ?? [];
    $mapaProcesos = $sections['mapa_procesos'] ?? [];
    $organigrama = $sections['organigrama'] ?? [];
?>

<body class="bg-gray-100 font-sans flex flex-col min-h-screen text-gray-800">

    <?php require_once dirname(__DIR__, 3) . '/views/partials/navbar.php'; ?>

    <main class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full flex-grow space-y-6">

        <!-- ALERTAS -->
        <?php if (isset($_GET['msg'])): ?>
            <div class="p-4 bg-green-100 border border-green-300 text-green-800 rounded-xl text-xs font-bold">
                ✅ Cambios e información guardados correctamente.
            </div>
        <?php endif; ?>

        <!-- ENCABEZADO -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-yjBlue flex items-center gap-2">
                    <span class="w-3 h-7 bg-yjOrange rounded-full"></span> Gestión Quiénes Somos
                </h1>
                <p class="text-xs text-gray-500 mt-1">Administra la historia, galería multimedial, misión, visión, mapa de procesos y organigrama.</p>
            </div>
            <a href="/yaconecta/public/quienes-somos" target="_blank" class="bg-yjBlue hover:bg-blue-900 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition shadow flex items-center gap-2 self-start cursor-pointer">
                👁️ Ver Vista Pública
            </a>
        </div>

        <!-- PESTAÑAS DEL PANEL ADMIN -->
        <div class="flex flex-wrap border-b border-gray-200 bg-white p-2 rounded-2xl shadow-sm gap-2">
            <button onclick="switchAdminTab('historia')" id="atab-historia" class="atab-btn flex-1 min-w-[140px] py-2.5 px-3 rounded-xl font-bold text-xs transition bg-yjBlue text-white shadow">
                📜 Historia & Galería
            </button>
            <button onclick="switchAdminTab('mision')" id="atab-mision" class="atab-btn flex-1 min-w-[140px] py-2.5 px-3 rounded-xl font-bold text-xs transition text-gray-600 hover:bg-gray-100">
                🎯 Misión, Visión y Valores
            </button>
            <button onclick="switchAdminTab('cultura')" id="atab-cultura" class="atab-btn flex-1 min-w-[140px] py-2.5 px-3 rounded-xl font-bold text-xs transition text-gray-600 hover:bg-gray-100">
                🏆 Cultura
            </button>
            <button onclick="switchAdminTab('mapa')" id="atab-mapa" class="atab-btn flex-1 min-w-[140px] py-2.5 px-3 rounded-xl font-bold text-xs transition text-gray-600 hover:bg-gray-100">
                🗺️ Mapa Procesos
            </button>
            <button onclick="switchAdminTab('organigrama')" id="atab-organigrama" class="atab-btn flex-1 min-w-[140px] py-2.5 px-3 rounded-xl font-bold text-xs transition text-gray-600 hover:bg-gray-100">
                🏢 Organigrama
            </button>
        </div>

        <!-- 1. PESTAÑA HISTORIA & GALERÍA MULTIMEDIA -->
        <div id="acontent-historia" class="acontent-tab space-y-6">
            <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm space-y-4">
                <h2 class="text-base font-black text-yjBlue">Editar Texto de Nuestra Historia</h2>
                <form action="/yaconecta/public/admin/quienes-somos/update-section" method="POST" class="space-y-4">
                    <input type="hidden" name="section_key" value="historia">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Antetítulo</label>
                            <input type="text" name="subtitle" value="<?= htmlspecialchars($historia['subtitle'] ?? 'NUESTRA HISTORIA') ?>" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Título Principal</label>
                            <input type="text" name="title" value="<?= htmlspecialchars($historia['title'] ?? '') ?>" required class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Contenido de la Historia</label>
                        <textarea name="content" rows="4" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl outline-none"><?= htmlspecialchars($historia['content'] ?? '') ?></textarea>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="bg-yjOrange hover:bg-orange-600 text-white font-bold text-xs px-5 py-2.5 rounded-xl transition">Guardar Historia</button>
                    </div>
                </form>
            </div>

            <!-- GALERÍA DE FOTOS Y VIDEOS DE LA HISTORIA -->
            <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm space-y-4">
                <h2 class="text-base font-black text-yjBlue">Galería Multimedia (Imágenes y Videos del Carrusel)</h2>
                
                <form action="/yaconecta/public/admin/quienes-somos/add-media" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end bg-gray-50 p-4 rounded-xl border border-gray-200">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Tipo</label>
                        <select name="type" id="media_type_select" onchange="toggleMediaInput()" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl bg-white outline-none">
                            <option value="image">Imagen</option>
                            <option value="video">Video (MP4 / YouTube)</option>
                        </select>
                    </div>
                    <div id="input_file_box">
                        <label class="block text-xs font-bold text-gray-700 mb-1">Archivo Imagen/Video</label>
                        <input type="file" name="media_file" class="w-full text-xs text-gray-500">
                    </div>
                    <div id="input_url_box" class="hidden">
                        <label class="block text-xs font-bold text-gray-700 mb-1">URL YouTube o Video</label>
                        <input type="text" name="video_url" placeholder="Ej: https://www.youtube.com/embed/..." class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Leyenda / Descripción</label>
                        <input type="text" name="caption" placeholder="Ej: Discurso del Presidente" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl outline-none">
                    </div>
                    <div>
                        <button type="submit" class="w-full bg-yjBlue hover:bg-blue-900 text-white font-bold text-xs py-2.5 rounded-xl transition">Añadir a la Galería</button>
                    </div>
                </form>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-4">
                    <?php foreach ($media as $m): ?>
                        <div class="relative bg-gray-100 p-2 rounded-xl border border-gray-200 space-y-2">
                            <?php if ($m['type'] === 'video'): ?>
                                <div class="bg-black/80 h-28 rounded-lg flex items-center justify-center text-white text-2xl">🎥</div>
                            <?php else: ?>
                                <img src="/yaconecta/public<?= htmlspecialchars($m['file_path']) ?>" class="h-28 w-full object-cover rounded-lg">
                            <?php endif; ?>
                            <div class="text-[10px] text-gray-600 truncate font-semibold"><?= htmlspecialchars($m['caption'] ?? 'Sin título') ?></div>
                            <a href="/yaconecta/public/admin/quienes-somos/delete-media?id=<?= $m['id'] ?>" onclick="return confirm('¿Eliminar del carrusel?');" class="block text-center text-red-600 text-[10px] font-bold hover:underline">Eliminar</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- 2. PESTAÑA MISIÓN, VISIÓN Y VALORES -->
        <div id="acontent-mision" class="acontent-tab hidden space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <form action="/yaconecta/public/admin/quienes-somos/update-section" method="POST" class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm space-y-3">
                    <input type="hidden" name="section_key" value="mision">
                    <h2 class="text-base font-black text-yjBlue">🎯 Editar Misión</h2>
                    <input type="text" name="title" value="<?= htmlspecialchars($mision['title'] ?? 'Nuestra Misión') ?>" required class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl outline-none">
                    <textarea name="content" rows="4" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl outline-none"><?= htmlspecialchars($mision['content'] ?? '') ?></textarea>
                    <button type="submit" class="bg-yjOrange text-white font-bold text-xs px-4 py-2 rounded-xl">Guardar Misión</button>
                </form>

                <form action="/yaconecta/public/admin/quienes-somos/update-section" method="POST" class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm space-y-3">
                    <input type="hidden" name="section_key" value="vision">
                    <h2 class="text-base font-black text-yjBlue">🚀 Editar Visión</h2>
                    <input type="text" name="title" value="<?= htmlspecialchars($vision['title'] ?? 'Nuestra Visión') ?>" required class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl outline-none">
                    <textarea name="content" rows="4" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl outline-none"><?= htmlspecialchars($vision['content'] ?? '') ?></textarea>
                    <button type="submit" class="bg-yjOrange text-white font-bold text-xs px-4 py-2 rounded-xl">Guardar Visión</button>
                </form>
            </div>

            <!-- VALORES -->
            <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm space-y-4">
                <h2 class="text-base font-black text-yjBlue">Valores Corporativos</h2>
                <form action="/yaconecta/public/admin/quienes-somos/save-value" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4 bg-gray-50 p-4 rounded-xl items-end">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Ícono / Emoji</label>
                        <input type="text" name="icon" placeholder="Ej: 🤝" required class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Título</label>
                        <input type="text" name="title" placeholder="Ej: Integridad" required class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Descripción</label>
                        <input type="text" name="description" placeholder="Breve descripción..." required class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl outline-none">
                    </div>
                    <div>
                        <button type="submit" class="w-full bg-yjBlue text-white font-bold text-xs py-2.5 rounded-xl">Agregar Valor</button>
                    </div>
                </form>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                    <?php foreach ($valores as $v): ?>
                        <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl space-y-1">
                            <div class="text-2xl"><?= htmlspecialchars($v['icon']) ?></div>
                            <h3 class="font-bold text-xs text-gray-800"><?= htmlspecialchars($v['title']) ?></h3>
                            <p class="text-[11px] text-gray-500"><?= htmlspecialchars($v['description']) ?></p>
                            <a href="/yaconecta/public/admin/quienes-somos/delete-value?id=<?= $v['id'] ?>" onclick="return confirm('¿Eliminar valor?');" class="text-red-600 text-[10px] font-bold block pt-2">Eliminar</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- 3. PESTAÑA CULTURA -->
        <div id="acontent-cultura" class="acontent-tab hidden bg-white p-6 rounded-2xl border border-gray-200 shadow-sm space-y-4">
            <h2 class="text-base font-black text-yjBlue">Cultura Organizacional</h2>
            <form action="/yaconecta/public/admin/quienes-somos/update-section" method="POST" enctype="multipart/form-data" class="space-y-4">
                <input type="hidden" name="section_key" value="cultura">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Antetítulo</label>
                        <input type="text" name="subtitle" value="<?= htmlspecialchars($cultura['subtitle'] ?? 'VIVE YAJUEGO') ?>" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Título Principal</label>
                        <input type="text" name="title" value="<?= htmlspecialchars($cultura['title'] ?? 'Cultura Organizacional') ?>" required class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Descripción de la Cultura</label>
                    <textarea name="content" rows="4" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl outline-none"><?= htmlspecialchars($cultura['content'] ?? '') ?></textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Imagen Representativa de Cultura</label>
                    <input type="file" name="media" class="w-full text-xs text-gray-500">
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="bg-yjOrange text-white font-bold text-xs px-5 py-2.5 rounded-xl">Guardar Cultura</button>
                </div>
            </form>
        </div>

        <!-- 4. PESTAÑA MAPA DE PROCESOS -->
        <div id="acontent-mapa" class="acontent-tab hidden bg-white p-6 rounded-2xl border border-gray-200 shadow-sm space-y-4">
            <h2 class="text-base font-black text-yjBlue">Mapa de Procesos (SGC)</h2>
            <form action="/yaconecta/public/admin/quienes-somos/update-section" method="POST" enctype="multipart/form-data" class="space-y-4">
                <input type="hidden" name="section_key" value="mapa_procesos">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Antetítulo</label>
                        <input type="text" name="subtitle" value="<?= htmlspecialchars($mapaProcesos['subtitle'] ?? 'SISTEMA DE GESTIÓN DE CALIDAD') ?>" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Título Principal</label>
                        <input type="text" name="title" value="<?= htmlspecialchars($mapaProcesos['title'] ?? 'Mapa de Procesos') ?>" required class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Descripción Breve</label>
                    <textarea name="content" rows="2" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl outline-none"><?= htmlspecialchars($mapaProcesos['content'] ?? '') ?></textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Subir Imagen del Mapa de Procesos (PNG / JPG alta calidad)</label>
                    <input type="file" name="media" class="w-full text-xs text-gray-500">
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="bg-yjOrange text-white font-bold text-xs px-5 py-2.5 rounded-xl">Guardar Mapa de Procesos</button>
                </div>
            </form>
        </div>

        <!-- 5. PESTAÑA ORGANIGRAMA -->
        <div id="acontent-organigrama" class="acontent-tab hidden bg-white p-6 rounded-2xl border border-gray-200 shadow-sm space-y-4">
            <h2 class="text-base font-black text-yjBlue">Organigrama Institucional</h2>
            <form action="/yaconecta/public/admin/quienes-somos/update-section" method="POST" enctype="multipart/form-data" class="space-y-4">
                <input type="hidden" name="section_key" value="organigrama">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Antetítulo</label>
                        <input type="text" name="subtitle" value="<?= htmlspecialchars($organigrama['subtitle'] ?? 'ESTRUCTURA ORGANIZACIONAL') ?>" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Título Principal</label>
                        <input type="text" name="title" value="<?= htmlspecialchars($organigrama['title'] ?? 'Organigrama Institucional') ?>" required class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Descripción Breve</label>
                    <textarea name="content" rows="2" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl outline-none"><?= htmlspecialchars($organigrama['content'] ?? '') ?></textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Subir Imagen / PDF de Organigrama</label>
                    <input type="file" name="media" class="w-full text-xs text-gray-500">
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="bg-yjOrange text-white font-bold text-xs px-5 py-2.5 rounded-xl">Guardar Organigrama</button>
                </div>
            </form>
        </div>

    </main>

    <script>
        function switchAdminTab(tab) {
            document.querySelectorAll('.acontent-tab').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.atab-btn').forEach(btn => {
                btn.classList.remove('bg-yjBlue', 'text-white', 'shadow');
                btn.classList.add('text-gray-600', 'hover:bg-gray-100');
            });

            document.getElementById('acontent-' + tab).classList.remove('hidden');
            const active = document.getElementById('atab-' + tab);
            active.classList.remove('text-gray-600', 'hover:bg-gray-100');
            active.classList.add('bg-yjBlue', 'text-white', 'shadow');
        }

        function toggleMediaInput() {
            const type = document.getElementById('media_type_select').value;
            if (type === 'video') {
                document.getElementById('input_url_box').classList.remove('hidden');
            } else {
                document.getElementById('input_url_box').classList.add('hidden');
            }
        }
    </script>

    <?php require_once dirname(__DIR__, 3) . '/views/partials/footer.php'; ?>

</body>
</html>