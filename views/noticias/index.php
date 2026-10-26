<!DOCTYPE html>
<html lang="es">

<?php 
    $pageTitle = "Ya.Net | Noticias y Novedades";
    require_once __DIR__ . '/../partials/head.php'; 
?>

<body class="bg-gray-100 font-sans flex flex-col min-h-screen text-gray-800">

    <?php require_once __DIR__ . '/../partials/navbar.php'; ?>

    <main class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full flex-grow space-y-6">
        
        <!-- CABECERA PRINCIPAL -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-yjBlue flex items-center gap-2">
                    <span class="w-3 h-7 bg-yjOrange rounded-full"></span> Noticias y Comunicados
                </h1>
                <p class="text-xs text-gray-500 mt-1">Entérate de las últimas novedades, acontecimientos y avisos importantes de la empresa.</p>
            </div>

            <!-- BUSCADOR / FILTRO -->
            <form action="/yaconecta/public/noticias" method="GET" class="flex gap-2 w-full md:w-80">
                <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? $_GET['q'] ?? '') ?>" 
                       placeholder="Buscar noticias o eventos..." 
                       class="w-full px-4 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-yjOrange outline-none shadow-2xs">
                <button type="submit" class="bg-yjOrange hover:bg-orange-600 text-white font-bold text-xs px-5 py-2 rounded-xl transition shadow-sm cursor-pointer">
                    Buscar
                </button>
            </form>
        </div>

        <!-- REJILLA DE NOTICIAS PÚBLICAS -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <?php if (!empty($noticias) && is_array($noticias)): ?>
                <?php foreach ($noticias as $item): ?>
                    <?php
                        // Tratamiento seguro de la imagen
                        $imgSrc = '/yaconecta/public/assets/img/logo.jpg';
                        if (!empty($item['imagen'])) {
                            if (strpos($item['imagen'], 'http') === 0 || strpos($item['imagen'], '/') === 0) {
                                $imgSrc = $item['imagen'];
                            } else {
                                $imgSrc = '/yaconecta/public/uploads/noticias/' . $item['imagen'];
                            }
                        }

                        // Preparación segura de variables para JavaScript (evita errores con null)
                        $tituloJs    = $item['titulo'] ?? '';
                        $categoriaJs = $item['categoria'] ?? 'Noticia';
                        $fechaJs     = !empty($item['created_at']) ? date('d/m/Y', strtotime($item['created_at'])) : date('d/m/Y');
                        $contenidoJs = !empty($item['contenido']) ? $item['contenido'] : ($item['resumen'] ?? '');
                    ?>

                    <div class="bg-white rounded-2xl border border-gray-200 shadow-2xs hover:shadow-md transition overflow-hidden flex flex-col justify-between group">
                        
                        <!-- PORTADA DE LA NOTICIA -->
                        <div class="relative w-full h-48 bg-gray-200 overflow-hidden">
                            <img src="<?= htmlspecialchars($imgSrc) ?>" 
                                 alt="" 
                                 onerror="this.onerror=null; this.src='https://placehold.co/600x400/e2e8f0/1e293b?text=YA+CONECTA';"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300 block">
                                
                            <span class="absolute top-3 left-3 bg-yjBlue/90 backdrop-blur-xs text-white text-[10px] font-extrabold px-3 py-1 rounded-full uppercase tracking-wider z-10">
                                <?= htmlspecialchars($categoriaJs) ?>
                            </span>
                        </div>

                        <!-- CONTENIDO -->
                        <div class="p-5 flex-grow flex flex-col justify-between">
                            <div>
                                <p class="text-[10px] text-gray-400 font-bold mb-1">
                                    📅 <?= $fechaJs ?>
                                </p>
                                <h3 class="font-black text-sm text-gray-800 line-clamp-2 leading-tight group-hover:text-yjOrange transition">
                                    <?= htmlspecialchars($tituloJs) ?>
                                </h3>
                                <p class="text-xs text-gray-500 mt-2 line-clamp-3">
                                    <?= htmlspecialchars($contenidoJs) ?>
                                </p>
                            </div>

                            <!-- BOTÓN MODAL USANDO JSON_ENCODE SEGURO -->
                            <button onclick='openNewsModal(<?= json_encode($tituloJs) ?>, <?= json_encode($categoriaJs) ?>, <?= json_encode($fechaJs) ?>, <?= json_encode($contenidoJs) ?>, <?= json_encode($imgSrc) ?>)' 
                                    class="w-full mt-4 bg-gray-50 hover:bg-yjBlue hover:text-white text-gray-700 text-[11px] font-bold py-2 rounded-xl border border-gray-200 transition text-center cursor-pointer">
                                Leer más →
                            </button>
                        </div>

                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-full bg-white p-12 rounded-2xl text-center border border-gray-200">
                    <p class="text-sm font-bold text-gray-500">No hay noticias o comunicados disponibles en este momento.</p>
                </div>
            <?php endif; ?>
        </div>

    </main>

    <!-- MODAL DE LECTURA DE NOTICIA -->
    <div id="newsModal" class="fixed inset-0 bg-black/50 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-2xl w-full p-6 shadow-2xl border border-gray-100 relative animate-fadeIn max-h-[90vh] overflow-y-auto">
            <button onclick="closeNewsModal()" class="absolute top-4 right-4 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold transition">✕</button>
            
            <span id="modalCategoria" class="bg-yjOrange text-white text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-wider inline-block mb-3">--</span>
            
            <h2 id="modalTitulo" class="text-xl font-black text-yjBlue mb-2 leading-snug">--</h2>
            <p id="modalFecha" class="text-xs text-gray-400 font-bold mb-4">--</p>

            <div class="w-full max-h-[70vh] bg-gray-100 rounded-xl overflow-hidden mb-4 flex items-center justify-center">
                <img id="modalImagen" src="" alt="Portada" onerror="this.onerror=null; this.src='https://placehold.co/600x400/e2e8f0/1e293b?text=YA+CONECTA';" class="w-full h-auto max-h-[70vh] object-contain rounded-xl">
            </div>

            <div id="modalContenido" class="text-xs text-gray-600 leading-relaxed space-y-3 whitespace-pre-line">
                --
            </div>

            <div class="mt-6 pt-4 border-t border-gray-100 flex justify-end">
                <button onclick="closeNewsModal()" class="px-5 bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs font-bold py-2 rounded-xl transition cursor-pointer">
                    Cerrar
                </button>
            </div>
        </div>
    </div>

    <script>
        function openNewsModal(titulo, categoria, fecha, contenido, imagen) {
            document.getElementById('modalTitulo').innerText = titulo;
            document.getElementById('modalCategoria').innerText = categoria;
            document.getElementById('modalFecha').innerText = '📅 Publicado el ' + fecha;
            document.getElementById('modalContenido').innerText = contenido;
            
            const modalImg = document.getElementById('modalImagen');
            modalImg.src = imagen;

            document.getElementById('newsModal').classList.remove('hidden');
        }

        function closeNewsModal() {
            document.getElementById('newsModal').classList.hidden = true;
            document.getElementById('newsModal').classList.add('hidden');
        }
    </script>

    <?php require_once __DIR__ . '/../partials/footer.php'; ?>

</body>
</html>