<?php
// Carga directa para capturar los eventos
if (!isset($eventos) || empty($eventos)) {
    try {
        if (class_exists('\App\Models\Evento')) {
            $eventos = \App\Models\Evento::obtenerTodos();
        } elseif (class_exists('\App\Models\Noticia')) {
            $todas = \App\Models\Noticia::obtenerTodas();
            
            // Filtrar SOLO las publicaciones cuya categoría sea EVENTOS
            $eventos = array_filter($todas, function($item) {
                $cat = strtoupper(trim($item['categoria'] ?? $item['category'] ?? ''));
                return $cat === 'EVENTOS' || $cat === 'EVENTO';
            });
        }
    } catch (\Throwable $e) {
        $eventos = [];
    }
}

// Ordenar más recientes primero
usort($eventos, function($a, $b) {
    return ($b['id'] ?? 0) <=> ($a['id'] ?? 0);
});
?>

<div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-xs space-y-4">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-base font-black text-yjBlue flex items-center gap-2">
                <span class="w-2.5 h-6 bg-yjOrange rounded-full"></span> Eventos & Galería Multimedia
            </h2>
        </div>
        <span class="text-xs font-bold text-gray-400">Momentos Yajuego</span>
    </div>

    <?php if (!empty($eventos)): ?>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <?php foreach (array_slice($eventos, 0, 3) as $evento): ?>
                <?php 
                    $titulo    = $evento['titulo'] ?? $evento['title'] ?? 'Evento Corporativo';
                    $desc      = $evento['resumen'] ?? $evento['descripcion'] ?? $evento['contenido'] ?? '';
                    $imagen    = $evento['imagen'] ?? $evento['image_url'] ?? $evento['portada'] ?? '';
                    $tipo      = strtoupper($evento['categoria'] ?? 'EVENTOS');
                ?>
                <div class="border border-gray-100 rounded-2xl overflow-hidden bg-gray-50/50 hover:shadow-md transition group">
                    <div class="relative h-40 bg-gray-200 overflow-hidden">
                        <?php if (!empty($imagen)): ?>
                            <img src="<?= htmlspecialchars($imagen) ?>" alt="<?= htmlspecialchars($titulo) ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        <?php else: ?>
                            <div class="w-full h-full bg-yjBlue/10 flex items-center justify-center text-yjBlue font-bold text-xs p-2 text-center">
                                <?= htmlspecialchars($titulo) ?>
                            </div>
                        <?php endif; ?>
                        
                        <span class="absolute top-3 left-3 bg-yjOrange text-white text-[9px] font-black px-2 py-0.5 rounded-md tracking-wider">
                            📷 <?= htmlspecialchars($tipo) ?>
                        </span>
                    </div>
                    <div class="p-4">
                        <h3 class="font-bold text-xs text-gray-800 line-clamp-1 mb-1"><?= htmlspecialchars($titulo) ?></h3>
                        <p class="text-[11px] text-gray-500 line-clamp-2"><?= htmlspecialchars(strip_tags($desc)) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-8 text-xs text-gray-400 font-medium">
            No hay eventos o galerías publicadas por el momento.
        </div>
    <?php endif; ?>
</div>