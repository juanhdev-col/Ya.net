<?php
// Fallback de carga para asegurar que el widget siempre tenga aplicativos
if (!isset($aplicativosWidget) || empty($aplicativosWidget)) {
    if (isset($apps) && !empty($apps)) {
        $aplicativosWidget = $apps;
    } elseif (isset($aplicativos) && !empty($aplicativos)) {
        $aplicativosWidget = $aplicativos;
    } else {
        try {
            if (class_exists('\App\Models\AppItem')) {
                $aplicativosWidget = \App\Models\AppItem::obtenerTodos();
            } else {
                $aplicativosWidget = [];
            }
        } catch (\Throwable $e) {
            $aplicativosWidget = [];
        }
    }
}
?>

<section class="home-card home-app-card">
    <div>
        <div class="home-card-heading">
            <h2><span>Accesos rápidos</span>Herramientas y servicios</h2>
            <a href="/yaconecta/public/apps">Ver todos los aplicativos <span aria-hidden="true">›</span></a>
        </div>

        <div class="home-app-list">
            <?php if (!empty($aplicativosWidget)): ?>
                <?php foreach (array_slice($aplicativosWidget, 0, 8) as $app): ?>
                    <?php 
                        // Normalización de claves (inglés / español)
                        $nombreApp = $app['name'] ?? $app['nombre'] ?? 'App';
                        $urlApp    = $app['url'] ?? $app['url_acceso'] ?? '#';
                        $badge     = strtoupper(substr(trim($nombreApp), 0, 2));
                    ?>
                    <a href="<?= htmlspecialchars($urlApp) ?>" 
                       target="_blank" 
                       rel="noopener noreferrer"
                       class="home-app-item">
                        
                        <!-- Icono Semiredondo -->
                        <div class="home-app-icon">
                            <?= htmlspecialchars($badge) ?>
                        </div>
                        
                        <span class="home-app-copy"><strong><?= htmlspecialchars($nombreApp) ?></strong><small><?= htmlspecialchars($app['description'] ?? $app['descripcion'] ?? 'Aplicativo corporativo') ?></small></span>
                        <span class="home-app-arrow" aria-hidden="true">›</span>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-xs text-gray-400 font-medium col-span-full text-center py-6">No hay aplicativos configurados.</p>
            <?php endif; ?>
        </div>
    </div>
</section>
