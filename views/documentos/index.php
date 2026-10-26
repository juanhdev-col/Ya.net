<!DOCTYPE html>
<html lang="es">
<?php require_once dirname(__DIR__) . '/partials/head.php'; ?>
<body class="documents-page">
<?php require_once dirname(__DIR__) . '/partials/navbar.php'; ?>
<main>
    <section class="documents-hero"><div class="documents-hero__image"></div><div class="documents-hero__shade"></div><div class="documents-hero__inner"><span class="documents-eyebrow"><i></i> YA CONECTA</span><h1>Gestión <em>Documental</em></h1><p>Encuentra aquí los documentos, políticas, procedimientos<br class="desktop-break"> y lineamientos corporativos de Yajuego.</p><div class="hero-note"><span class="file-outline">▤</span><strong>Información<br>que nos mantiene<br><em>conectados</em></strong></div></div></section>
    <?php $categories = ['' => ['Todos','Ver todos los documentos','▤'], 'Políticas' => ['Políticas','Lineamientos generales','◇'], 'Procesos' => ['Procesos','Procedimientos y guías','⚙'], 'Formatos' => ['Formatos','Plantillas y formatos','▧'], 'Manuales' => ['Manuales','Documentos de referencia','▣'], 'Otros' => ['Otros','Circulares, instructivos, etc.','⊞']]; ?>
    <section class="category-strip" aria-label="Categorías documentales">
        <?php foreach ($categories as $key => [$label,$description,$icon]): $count = $key === '' ? array_sum($resumen) : ($resumen[$key] ?? 0); ?>
            <a class="category-card <?= $categoria === $key ? 'selected' : '' ?>" href="/yaconecta/public/documentos<?= $key !== '' ? '?cat=' . urlencode($key) : '' ?>"><span class="category-icon"><?= $icon ?></span><span class="category-copy"><b><?= htmlspecialchars($label) ?></b><small><?= htmlspecialchars($description) ?></small></span><span class="category-count"><?= $count ?></span></a>
        <?php endforeach; ?>
    </section>
    <section class="documents-layout">
        <aside class="document-filters">
            <h2>Filtrar por categoría</h2>
            <?php foreach ($categories as $key => [$label]): $count = $key === '' ? array_sum($resumen) : ($resumen[$key] ?? 0); ?>
                <a class="filter-link <?= $categoria === $key ? 'active' : '' ?>" href="/yaconecta/public/documentos?<?= http_build_query(array_filter(['cat'=>$key,'search'=>$search,'area'=>$area,'orden'=>$orden], static fn($v) => $v !== '')) ?>"><?= htmlspecialchars($label) ?><span><?= $count ?></span></a>
            <?php endforeach; ?>
            <form class="area-filter" action="/yaconecta/public/documentos" method="GET"><input type="hidden" name="cat" value="<?= htmlspecialchars($categoria) ?>"><input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>"><label for="areaFilter">Filtrar por área</label><select id="areaFilter" name="area" onchange="this.form.submit()"><option value="">Todas las áreas</option><?php foreach ($departamentos as $department): ?><option value="<?= (int)$department['id'] ?>" <?= (string)$area === (string)$department['id'] ? 'selected' : '' ?>><?= htmlspecialchars($department['name']) ?></option><?php endforeach; ?></select></form>
            <form class="keyword-filter" action="/yaconecta/public/documentos" method="GET"><input type="hidden" name="cat" value="<?= htmlspecialchars($categoria) ?>"><input type="hidden" name="area" value="<?= htmlspecialchars($area) ?>"><label for="keywordSearch">Buscar por palabra clave</label><div><input id="keywordSearch" type="search" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Ej. procedimiento, política..."><button aria-label="Buscar">⌕</button></div></form>
        </aside>
        <div class="document-results"><form class="document-search" action="/yaconecta/public/documentos" method="GET"><input type="hidden" name="cat" value="<?= htmlspecialchars($categoria) ?>"><input type="hidden" name="area" value="<?= htmlspecialchars($area) ?>"><label class="search-input"><span>⌕</span><input type="search" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Buscar documentos por nombre, código o palabra clave..."></label><label class="sort-label">Ordenar por:<select name="orden" onchange="this.form.submit()"><option value="recentes" <?= $orden === 'recentes' ? 'selected' : '' ?>>Más recientes</option><option value="alfabetico" <?= $orden === 'alfabetico' ? 'selected' : '' ?>>Nombre A–Z</option></select></label></form>
            <div class="document-list">
                <?php foreach ($documentos as $doc): $extension = strtoupper(pathinfo($doc['file_path'], PATHINFO_EXTENSION)); $typeClass = $extension === 'PDF' ? 'pdf' : (in_array($extension, ['XLS','XLSX'], true) ? 'spreadsheet' : 'word'); ?>
                    <article class="document-row"><span class="file-badge <?= $typeClass ?>"><?= htmlspecialchars($extension ?: 'DOC') ?></span><span class="doc-code"><?= htmlspecialchars($doc['code']) ?></span><div class="doc-main"><h3><?= htmlspecialchars($doc['title']) ?></h3><p><?= htmlspecialchars($doc['process_owner']) ?> · <?= htmlspecialchars($doc['category']) ?></p></div><span class="doc-tag <?= strtolower($doc['category']) ?>"><?= htmlspecialchars(rtrim($doc['category'], 's')) ?></span><div class="doc-meta"><time><?= !empty($doc['created_at']) ? date('d M, Y', strtotime($doc['created_at'])) : date('d M, Y', strtotime($doc['updated_at'])) ?></time><?php $privateFile = dirname(__DIR__,2) . '/storage/documentos/' . basename($doc['file_path']); ?><small><?= number_format((is_file($privateFile) ? filesize($privateFile) : 0) / 1024, 0) ?> KB</small></div><div class="doc-actions"><?php if ($extension === 'PDF'): ?><a href="/yaconecta/public/documentos/archivo?id=<?= (int)$doc['id'] ?>&modo=ver" aria-label="Ver <?= htmlspecialchars($doc['title'], ENT_QUOTES) ?>" title="Vista previa">◎</a><?php endif; ?><?php if (!empty($doc['is_downloadable'])): ?><a href="/yaconecta/public/documentos/archivo?id=<?= (int)$doc['id'] ?>" aria-label="Descargar <?= htmlspecialchars($doc['title'], ENT_QUOTES) ?>" title="Descargar">↓</a><?php endif; ?></div></article>
                <?php endforeach; ?>
                <?php if (!$documentos): ?><div class="empty-results"><span>⌕</span><h3>No encontramos documentos</h3><p>Prueba otra palabra clave o cambia los filtros seleccionados.</p><a href="/yaconecta/public/documentos">Limpiar filtros</a></div><?php endif; ?>
            </div>
        </div>
        <aside class="document-aside"><section class="featured-docs"><h2><span>★</span> Documentos destacados</h2><?php foreach ($documentosDestacados as $doc): ?><a href="/yaconecta/public/documentos/archivo?id=<?= (int)$doc['id'] ?>&modo=ver"><span class="mini-file">PDF</span><span><b><?= htmlspecialchars($doc['title']) ?></b><small><?= htmlspecialchars($doc['code']) ?></small></span><i>›</i></a><?php endforeach; ?><?php if (!$documentosDestacados): ?><p class="aside-empty">Los documentos destacados aparecerán aquí.</p><?php endif; ?></section><section class="secure-note"><span class="secure-icon">▱✓</span><h2>Información actualizada y segura</h2><p>Los documentos vigentes se consultan desde esta intranet. Las descargas requieren iniciar sesión y los archivos se entregan desde una ruta controlada.</p></section></aside>
    </section>
    <?php if (!$usuarioAutenticado): ?><p class="login-hint">Para abrir o descargar archivos, <a href="/yaconecta/public/login">inicia sesión con tu cuenta corporativa</a>.</p><?php endif; ?>
</main>
<?php require_once dirname(__DIR__) . '/partials/footer.php'; ?>
</body></html>



