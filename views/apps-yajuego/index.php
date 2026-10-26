<!DOCTYPE html>
<html lang="es">
<?php $pageTitle=$pageTitle??'Ya.Net | Aplicaciones Corporativas'; require_once __DIR__.'/../partials/head.php'; ?>
<body class="public-apps-page">
<?php require_once __DIR__.'/../partials/navbar.php'; ?>
<section class="public-apps-hero"><div class="public-apps-hero-inner"><span class="public-apps-eyebrow"><i></i> YA.NET</span><h1>Aplicaciones <em>Corporativas</em></h1><p>Accede de forma rápida y segura a las herramientas<br>que necesitas para tu trabajo diario.</p><div class="apps-hero-laptop" aria-hidden="true"><div class="laptop-screen"><span>YA.<b>NET</b></span><small>Un solo lugar, más cerca de todos.</small><i></i></div><div class="laptop-base"></div></div><div class="public-apps-slogan">Las herramientas<br>que nos conectan<br>cada día</div></div></section>
<main class="public-apps-main">
 <form class="public-apps-search" action="/yaconecta/public/apps" method="GET"><span>⌕</span><input type="search" name="q" value="<?= htmlspecialchars($busqueda??($_GET['q']??'')) ?>" placeholder="Buscar aplicación por nombre, descripción o categoría..."><button type="submit" aria-label="Buscar">Buscar</button></form>
 <nav class="public-app-categories" aria-label="Categorías de aplicaciones"><a class="<?= empty($categoria)?'selected':'' ?>" href="/yaconecta/public/apps<?= !empty($busqueda)?'?q='.urlencode($busqueda):'' ?>"><b>▦</b>Todas</a><?php $categoryIcons=['Comunicación'=>'◯','Gestión'=>'⚙','Talento Humano'=>'♧','Comercial'=>'▥','Analítica'=>'▤','Operaciones'=>'◷','Soporte'=>'◉','Legal'=>'▧','Financiera'=>'▱']; foreach($categoriasApps as $cat): ?><a class="<?= ($categoria??'')===$cat?'selected':'' ?>" href="/yaconecta/public/apps?cat=<?= urlencode($cat) ?><?= !empty($busqueda)?'&q='.urlencode($busqueda):'' ?>"><b><?= htmlspecialchars($categoryIcons[$cat]??'▱') ?></b><?= htmlspecialchars($cat) ?></a><?php endforeach; ?></nav>
 <section class="public-apps-list"><h2><i></i>Nuestras aplicaciones</h2><div class="public-apps-grid">
 <?php foreach($aplicativos as $app): $name=$app['name']??$app['nombre']??'Aplicación';$url=$app['url']??$app['url_acceso']??'#';$category=$app['category']??$app['categoria']??'General';$description=$app['description']??$app['descripcion']??'Herramienta corporativa de Yajuego.';$initials=mb_strtoupper(mb_substr($name,0,2,'UTF-8'),'UTF-8');$accessUrl=$url;if($url!=='#'&&!preg_match('~^https?://~i',$url)){if(str_starts_with($url,'/')){$accessUrl=$url;}else{$accessUrl='http://'.$url;}} ?>
 <article class="public-app-card"><div class="public-app-card-top"><span class="public-app-logo"><?= htmlspecialchars($initials) ?></span><button type="button" class="favorite-app" aria-label="Marcar como favorita" data-app-favorite="<?= (int)($app['id']??0) ?>">☆</button></div><h3><?= htmlspecialchars($name) ?></h3><p><?= htmlspecialchars($description) ?></p><span class="public-app-category"><?= htmlspecialchars($category) ?></span><a href="<?= htmlspecialchars($accessUrl) ?>" target="_blank" rel="noopener noreferrer" class="public-app-open">Abrir aplicación <b>↗</b></a></article>
 <?php endforeach; ?>
 <?php if(!$aplicativos): ?><div class="public-apps-empty">No encontramos aplicaciones activas con esos criterios de búsqueda.</div><?php endif; ?>
 </div></section>
</main>
<script>
const favoriteKey='yanet-favorite-apps';let favorites=[];try{favorites=JSON.parse(localStorage.getItem(favoriteKey)||'[]')}catch(e){}document.querySelectorAll('[data-app-favorite]').forEach(button=>{const id=button.dataset.appFavorite;const paint=()=>{const active=favorites.includes(id);button.classList.toggle('is-favorite',active);button.textContent=active?'★':'☆';button.setAttribute('aria-label',active?'Quitar de favoritas':'Marcar como favorita')};paint();button.addEventListener('click',()=>{favorites=favorites.includes(id)?favorites.filter(x=>x!==id):[...favorites,id];localStorage.setItem(favoriteKey,JSON.stringify(favorites));paint()})});
</script>
<?php require_once __DIR__.'/../partials/footer.php'; ?>
</body></html>
