<?php
// 1. Obtener todas las noticias desde el modelo
$noticiasHome = [];
try {
    if (class_exists('\App\Models\Noticia')) {
        if (method_exists('\App\Models\Noticia', 'obtenerTodas')) {
            $noticiasHome = \App\Models\Noticia::obtenerTodas();
        } elseif (method_exists('\App\Models\Noticia', 'obtenerAprobadas')) {
            $noticiasHome = \App\Models\Noticia::obtenerAprobadas();
        } elseif (method_exists('\App\Models\Noticia', 'all')) {
            $noticiasHome = \App\Models\Noticia::all();
        }
    }
} catch (\Throwable $e) {
    $noticiasHome = [];
}

// 2. Ordenar por ID descendente (la más reciente creada primero)
usort($noticiasHome, function($a, $b) {
    $idA = (int)($a['id'] ?? 0);
    $idB = (int)($b['id'] ?? 0);
    return $idB <=> $idA;
});

// 3. Filtrar para EXCLUIR los eventos del slider de noticias
$noticiasFiltradas = array_filter($noticiasHome, function($item) {
    $cat = strtoupper(trim($item['categoria'] ?? $item['category'] ?? ''));
    $titulo = strtoupper(trim($item['titulo'] ?? $item['title'] ?? ''));
    
    // Si la categoría dice EVENTOS o el título contiene la palabra Torneo/Evento, enviarlo a Eventos
    if ($cat === 'EVENTOS' || $cat === 'EVENTO' || str_contains($titulo, 'TORNEO')) {
        return false;
    }
    return true;
});

// Tomar los primeros 3 resultados más recientes
$noticiasMostrar = array_slice($noticiasFiltradas, 0, 3);
?>

<section class="home-card home-news">
    <div class="home-card-heading">
        <h2><span>Información</span>Noticias</h2>
        <a href="/yaconecta/public/noticias">Ver todas <span aria-hidden="true">›</span></a>
    </div>

    <?php if (!empty($noticiasMostrar)): ?>
        <div class="home-news-list">
            <?php foreach ($noticiasMostrar as $index => $item): ?>
                <?php 
                    $id        = $item['id'] ?? '';
                    $titulo    = $item['titulo'] ?? $item['title'] ?? 'Sin título';
                    $resumen   = $item['resumen'] ?? $item['contenido'] ?? $item['descripcion'] ?? '';
                    $imagen    = $item['imagen'] ?? $item['image_url'] ?? $item['portada'] ?? '';
                    $categoria = $item['categoria'] ?? $item['category'] ?? 'Noticia';
                    
                    // Formateo flexible de la fecha
                    $fechaRaw  = $item['fecha'] ?? $item['created_at'] ?? '';
                    $fecha     = !empty($fechaRaw) ? date('d/m/Y', strtotime($fechaRaw)) : date('d/m/Y');
                ?>
                <a href="/yaconecta/public/noticias?id=<?= $id ?>" class="home-news-item <?= $index === 0 ? 'is-featured' : '' ?>">
                    <div class="home-news-image">
                        <?php if (!empty($imagen)): ?>
                            <img src="<?= htmlspecialchars($imagen) ?>" alt="<?= htmlspecialchars($titulo) ?>">
                        <?php else: ?><span>Ya.Net</span><?php endif; ?>
                    </div>
                    <div class="home-news-copy">
                        <?php if ($index === 0): ?><span class="home-featured-badge">Destacada</span><?php endif; ?>
                        <h3><?= htmlspecialchars($titulo) ?></h3>
                        <p><?= htmlspecialchars(strip_tags($resumen)) ?></p>
                        <time>▢ &nbsp;<?= htmlspecialchars($fecha) ?></time>
                    </div>
                    <?php if ($index > 0): ?><span class="home-news-arrow" aria-hidden="true">›</span><?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-8 text-xs text-gray-400 font-medium">
            No hay noticias o comunicados publicados por el momento.
        </div>
    <?php endif; ?>
</section>
