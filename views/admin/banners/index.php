<!DOCTYPE html>
<html lang="es">

<?php 
    $pageTitle = "Ya.Net | Admin - Gestión de Banners";
    
    // Resolución centralizada de rutas para partials globales
    $baseViews = dirname(__DIR__, 2);
    $headFile   = $baseViews . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'head.php';
    $navbarFile = $baseViews . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'navbar.php';
    $footerFile = $baseViews . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'footer.php';

    if (file_exists($headFile)) {
        require_once $headFile;
    }
?>

<body class="bg-gray-100 font-sans flex flex-col min-h-screen text-gray-800">

    <?php 
    if (file_exists($navbarFile)) {
        require_once $navbarFile;
    } 
    ?>

    <main class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full flex-grow space-y-6">

        <?php if (isset($_GET['status'])): ?>
            <?php if ($_GET['status'] === 'created'): ?>
                <div class="p-4 bg-green-100 border border-green-300 text-green-800 rounded-xl text-xs font-bold flex items-center justify-between shadow-xs">
                    <span>✅ Banner guardado correctamente.</span>
                    <button onclick="this.parentElement.remove()" class="text-green-900 font-bold ml-2">✕</button>
                </div>
            <?php elseif ($_GET['status'] === 'deleted'): ?>
                <div class="p-4 bg-red-100 border border-red-300 text-red-800 rounded-xl text-xs font-bold flex items-center justify-between shadow-xs">
                    <span>🗑️ Banner eliminado del sistema.</span>
                    <button onclick="this.parentElement.remove()" class="text-red-900 font-bold ml-2">✕</button>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- CABECERA DE SECCIÓN -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-yjBlue flex items-center gap-2">
                    <span class="w-3 h-7 bg-yjOrange rounded-full"></span> Gestión del Carrusel Principal
                </h1>
                <p class="text-xs text-gray-500 mt-1">Sube imágenes o videos de fondo para el banner de la página de inicio.</p>
            </div>

            <div class="flex items-center gap-3">
                <a href="/yaconecta/public/" class="px-5 py-2 bg-yjOrange hover:bg-orange-600 text-white font-bold text-xs rounded-xl transition shadow-sm flex items-center gap-1.5">
                    ← Volver al Inicio
                </a>
            </div>
        </div>

        <!-- CONTENIDO PRINCIPAL EN GRILLA -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            <!-- FORMULARIO: AÑADIR NUEVO BANNER -->
            <div class="lg:col-span-4 bg-white p-6 rounded-2xl shadow-sm border border-gray-200 space-y-4">
                <h2 class="text-sm font-black text-yjBlue border-b border-gray-100 pb-3 flex items-center gap-2">
                    <span class="w-2.5 h-5 bg-yjOrange rounded-full"></span> Añadir Nuevo Banner
                </h2>

                <form action="/yaconecta/public/admin/banners/store" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Título Principal *</label>
                        <input type="text" name="titulo" required placeholder="Ej: ¡Transformando Nuestra Experiencia!" class="w-full px-3 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-yjOrange outline-none">
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Subtítulo / Descripción</label>
                        <textarea name="subtitulo" rows="2" placeholder="Ej: Conoce las últimas capacitaciones y eventos..." class="w-full px-3 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-yjOrange outline-none"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Etiqueta Superior</label>
                            <input type="text" name="etiqueta" placeholder="CULTURA YAJUEGO" class="w-full px-3 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-yjOrange outline-none">
                        </div>
                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Tipo de Media</label>
                            <select name="tipo_media" class="w-full px-3 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-yjOrange outline-none bg-white">
                                <option value="imagen">Imagen</option>
                                <option value="video">Video</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Subir Archivo (Imagen / Video)</label>
                        <input type="file" name="archivo" class="w-full text-gray-500 border border-gray-300 rounded-xl p-1.5 bg-gray-50">
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">O URL Externa de Media</label>
                        <input type="text" name="url_media" placeholder="https://ejemplo.com/imagen.jpg" class="w-full px-3 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-yjOrange outline-none">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Texto Botón</label>
                            <input type="text" name="texto_boton" placeholder="Ver más" class="w-full px-3 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-yjOrange outline-none">
                        </div>
                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Enlace Botón (URL)</label>
                            <input type="text" name="enlace_boton" placeholder="#" class="w-full px-3 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-yjOrange outline-none">
                        </div>
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-yjOrange hover:bg-orange-600 text-white font-bold text-xs rounded-xl shadow-sm transition mt-2">
                        Guardar Banner
                    </button>
                </form>
            </div>

            <!-- TARJETAS: BANNERS ACTIVOS -->
            <div class="lg:col-span-8 space-y-4">
                <h2 class="text-sm font-black text-yjBlue flex items-center gap-2">
                    <span class="w-2.5 h-5 bg-yjOrange rounded-full"></span> Banners Activos en el Home
                </h2>

                <?php if (!empty($banners)): ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <?php foreach ($banners as $b): ?>
                            <?php 
                                // Lectura de columnas con fallback seguro
                                $rawMedia = $b['url_media'] ?? $b['media_url'] ?? '';
                                $mediaUrl = '';

                                if (!empty($rawMedia)) {
                                    if (str_starts_with($rawMedia, 'http://') || str_starts_with($rawMedia, 'https://') || str_starts_with($rawMedia, '/')) {
                                        $mediaUrl = $rawMedia;
                                    } else {
                                        $mediaUrl = '/yaconecta/public/uploads/banners/' . $rawMedia;
                                    }
                                }

                                $tipoMedia = strtolower($b['tipo_media'] ?? 'imagen');
                                $etiqueta  = $b['etiqueta'] ?? $b['tag'] ?? '';
                            ?>

                            <div class="bg-white rounded-2xl border border-gray-200 shadow-2xs hover:shadow-md transition overflow-hidden flex flex-col justify-between">
                                <div>
                                    <div class="relative h-48 bg-gray-900 overflow-hidden flex items-center justify-center">
                                        <?php if (!empty($mediaUrl)): ?>
                                            <?php if ($tipoMedia === 'video'): ?>
                                                <video src="<?= htmlspecialchars($mediaUrl) ?>" class="w-full h-full object-cover" muted loop autoplay></video>
                                            <?php else: ?>
                                                <img src="<?= htmlspecialchars($mediaUrl) ?>" class="w-full h-full object-cover">
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <div class="text-xs font-bold text-gray-400">Sin Imagen o Video</div>
                                        <?php endif; ?>

                                        <?php if (!empty($etiqueta)): ?>
                                            <span class="absolute top-3 left-3 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-yjBlue/90 text-white shadow-xs">
                                                <?= htmlspecialchars((string)$etiqueta) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="p-5 space-y-1">
                                        <h3 class="font-black text-sm text-gray-800 leading-tight"><?= htmlspecialchars((string)($b['titulo'] ?? 'Sin título')) ?></h3>
                                        <?php if (!empty($b['subtitulo'])): ?>
                                            <p class="text-xs text-gray-500 line-clamp-2"><?= htmlspecialchars((string)$b['subtitulo']) ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="p-5 pt-3 border-t border-gray-100 flex justify-between items-center text-xs">
                                    <span class="text-gray-400 font-bold uppercase text-[10px]">Tipo: <?= htmlspecialchars((string)$tipoMedia) ?></span>
                                    <a href="/yaconecta/public/admin/banners/delete?id=<?= $b['id'] ?>" onclick="return confirm('¿Eliminar este banner?')" class="text-red-500 font-bold hover:underline flex items-center gap-1 text-xs">
                                        Eliminar 🗑️
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="bg-white p-12 rounded-2xl text-center border border-gray-200 text-xs text-gray-400">
                        No hay banners registrados en el carrusel principal.
                    </div>
                <?php endif; ?>
            </div>

        </div>

    </main>

    <?php 
    if (file_exists($footerFile)) {
        require_once $footerFile;
    } 
    ?>

</body>
</html>