<?php
$totalFiles = count($allMedia);
$albums = [];
foreach ($allMedia as $item) {
    $albumName = trim((string)($item['album'] ?? '')) ?: 'Sin álbum';
    $albums[$albumName] = ($albums[$albumName] ?? 0) + 1;
}
$recentAlbums = array_slice($albums, 0, 5, true);
?>
<!DOCTYPE html>
<html lang="es">
<?php require dirname(__DIR__, 2) . '/partials/head.php'; ?>
<body class="gallery-admin-page">
<?php require dirname(__DIR__, 2) . '/partials/navbar.php'; ?>
<div class="gallery-admin-layout">
 <aside class="gallery-sidebar"><strong>YA.NET</strong>
  <a href="/yaconecta/public/admin">⌂ <span>Panel Administrativo</span></a><a href="/yaconecta/public/admin/directorio">♧ <span>Colaboradores</span></a><a href="/yaconecta/public/admin/apps">▦ <span>Aplicaciones</span></a><a href="/yaconecta/public/admin/agenda">▣ <span>Eventos</span></a><a href="/yaconecta/public/admin/noticias">▤ <span>Noticias</span></a><a href="#" class="active">▧ <span>Galería</span></a><a href="#">▥ <span>Encuestas</span></a><a href="#">⚙ <span>Configuración</span></a>
 </aside>
 <main class="gallery-admin-main">
  <section class="gallery-hero"><div><small><i></i>PANEL ADMINISTRATIVO</small><h1>Galería <em>Corporativa</em></h1><p>Gestiona y comparte las fotografías y videos de las actividades, eventos<br> y momentos importantes de YA.NET.</p></div><div class="gallery-hero-art" aria-hidden="true"><span>📷</span><b>Nuestros momentos<br>también nos conectan</b></div></section>
  <section class="gallery-stats">
   <article><span class="stat-icon blue">▧</span><div><small>Total de archivos</small><b><?= $totalFiles ?></b><small>Imágenes y videos</small></div></article>
   <article><span class="stat-icon green">▱</span><div><small>Álbumes</small><b><?= count($albums) ?></b><small>Según los datos disponibles</small></div></article>
   <article><span class="stat-icon purple">◎</span><div><small>Visualizaciones</small><b>—</b><small>No se registran actualmente</small></div></article>
   <article><span class="stat-icon sky">☁</span><div><small>Espacio utilizado</small><b><?= number_format($totalBytes / 1024 / 1024, 2) ?> MB</b><small>Archivos alojados localmente</small></div></article>
  </section>
  <div class="gallery-admin-columns"><section class="gallery-list-card">
   <header><h2>Galería de archivos</h2><div><button class="gallery-outline" onclick="document.getElementById('galleryUpload').scrollIntoView({behavior:'smooth'})">↑ Subir archivos</button><button class="gallery-primary" onclick="document.getElementById('galleryUpload').scrollIntoView({behavior:'smooth'})">＋ Nuevo álbum</button></div></header>
   <form class="gallery-filter" method="get" action="/yaconecta/public/admin/galeria"><label class="gallery-search">⌕ <input name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Buscar por nombre, descripción o etiqueta..."></label><div class="gallery-filter-row"><select name="album"><option>Todos los álbumes</option></select><select name="tipo"><option value="">Todos los tipos</option><option value="image" <?= $typeFilter==='image'?'selected':'' ?>>Imágenes</option><option value="video" <?= $typeFilter==='video'?'selected':'' ?>>Videos</option></select><select disabled><option>Todas las fechas</option></select><select disabled><option>Todos los estados</option></select><button>⌕ Filtrar</button><a href="/yaconecta/public/admin/galeria">⟳ Limpiar filtros</a></div></form>
   <div class="gallery-table-wrap"><table><thead><tr><th><input type="checkbox" aria-label="Seleccionar todos"></th><th>Vista previa</th><th>Nombre</th><th>Álbum</th><th>Tipo</th><th>Fecha de carga</th><th>Estado</th><th>Acciones</th></tr></thead><tbody>
   <?php foreach ($media as $item): $video = strtolower((string)($item['type'] ?? '')) === 'video'; $caption = trim((string)($item['caption'] ?? '')); $path = (string)($item['file_path'] ?? ''); $filename = basename(parse_url($path, PHP_URL_PATH) ?: $path); $date = $item['created_at'] ?? null; ?>
    <tr><td><input type="checkbox" aria-label="Seleccionar archivo"></td><td><?php if (!$video): ?><img class="gallery-thumb" src="/yaconecta/public<?= htmlspecialchars($path) ?>" alt="<?= htmlspecialchars($caption ?: $filename) ?>"><?php else: ?><span class="gallery-video-thumb">▶</span><?php endif; ?></td><td class="gallery-file-name"><b><?= htmlspecialchars($filename ?: 'Archivo multimedia') ?></b><small><?= htmlspecialchars($caption ?: 'Sin descripción') ?></small></td><td><?= htmlspecialchars($item['album'] ?? '—') ?></td><td><span class="gallery-type <?= $video?'video':'' ?>"><?= $video?'Video':'Imagen' ?></span></td><td><?= $date ? htmlspecialchars(date('d/m/Y H:i', strtotime($date))) : '—' ?></td><td><span class="gallery-status">Publicado</span></td><td class="gallery-actions"><a title="Ver archivo" target="_blank" href="/yaconecta/public<?= htmlspecialchars($path) ?>">◉</a><a title="Editar descripción" href="#galleryUpload">✎</a><a class="danger" title="Eliminar" href="/yaconecta/public/admin/quienes-somos/delete-media?id=<?= (int)($item['id'] ?? 0) ?>" onclick="return confirm('¿Eliminar este archivo?')">▣</a></td></tr>
   <?php endforeach; if (!$media): ?><tr><td colspan="8" class="gallery-empty">No hay archivos multimedia que coincidan con la búsqueda.</td></tr><?php endif; ?>
   </tbody></table></div><footer class="gallery-table-footer"><span>Mostrando <?= count($media) ?> de <?= $totalFiles ?> archivos</span><nav><button disabled>‹</button><button class="selected">1</button><button disabled>›</button></nav></footer>
   <form id="galleryUpload" class="gallery-upload-form" action="/yaconecta/public/admin/quienes-somos/add-media" method="post" enctype="multipart/form-data"><h3>Subir archivos a la galería</h3><div><select name="type"><option value="image">Imagen</option><option value="video">Video</option></select><input type="file" name="media_file" accept="image/*,video/*" required><input name="caption" placeholder="Nombre o descripción del archivo"><button class="gallery-primary">Subir archivo</button></div><small>La galería usa el almacenamiento multimedia existente de Quiénes Somos. La gestión de álbumes requiere ampliar el modelo de datos.</small></form>
  </section><aside class="gallery-right"><section><h3>⚡ Acciones rápidas</h3><a href="#galleryUpload">↑ <span>Subir archivos</span></a><a href="#galleryUpload">▱ <span>Crear álbum</span></a><a href="/yaconecta/public/admin/quienes-somos">▤ <span>Gestionar galería multimedia</span></a><a href="/yaconecta/public/quienes-somos" target="_blank">▧ <span>Ver galería pública</span></a><a href="#" onclick="window.print();return false">↓ <span>Imprimir listado</span></a></section><section><h3>▣ Álbumes recientes</h3><?php if ($recentAlbums): foreach($recentAlbums as $name=>$count): ?><div class="gallery-album"><span>▧</span><b><?= htmlspecialchars($name) ?><small><?= $count ?> archivos</small></b></div><?php endforeach; else: ?><p class="gallery-no-albums">Todavía no hay álbumes registrados. El modelo actual almacena archivos individuales.</p><?php endif; ?><a class="gallery-all-albums" href="/yaconecta/public/admin/quienes-somos">Ver archivos multimedia →</a></section></aside></div>
 </main>
</div>
</body></html>
