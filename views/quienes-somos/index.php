<?php
$pageTitle = 'Ya.Net | Quiénes Somos';
require_once dirname(__DIR__, 1) . '/partials/head.php';
$historia = $sections['historia'] ?? ['title' => 'Una plataforma que conecta más que juego', 'subtitle' => 'NUESTRA EMPRESA', 'content' => 'En Yajuego trabajamos para generar experiencias de entretenimiento seguras, innovadoras y responsables, conectando personas y oportunidades.'];
$cultura = $sections['cultura'] ?? ['title' => 'Cultura Organizacional', 'subtitle' => 'VIVE YAJUEGO', 'content' => 'Fomentamos una cultura de innovación, colaboración y crecimiento.'];
$mapaProcesos = $sections['mapa_procesos'] ?? ['title' => 'Mapa de Procesos SGC', 'subtitle' => 'ESTRATEGIA Y CALIDAD', 'content' => 'Estructura e interacción de nuestros procesos.'];
$organigrama = $sections['organigrama'] ?? ['title' => 'Organigrama Institucional', 'subtitle' => 'ESTRUCTURA ORGANIZACIONAL', 'content' => 'Estructura jerárquica de la compañía.'];
$misionCompleta = 'Transformamos el entretenimiento digital en experiencias seguras, emocionantes y responsables, integrando la innovación tecnológica con el talento, la pasión y el compromiso de nuestro equipo humano. Trabajamos cada día para ofrecer experiencias confiables, accesibles y memorables que conecten con nuestros usuarios, promuevan la diversión responsable y fortalezcan la confianza en cada interacción. A través de la mejora continua, la integridad y la evolución constante, buscamos generar valor sostenible para nuestros usuarios, colaboradores, aliados y para la sociedad.';
$visionCompleta = 'En 2028 seremos reconocidos como la compañía que redefinió el entretenimiento digital y las apuestas deportivas en Colombia, impulsando una nueva forma de vivir estas experiencias a través de la innovación, la seguridad y la excelencia. Aspiramos a ser la primera elección de nuestros usuarios, distinguiéndonos por crear experiencias únicas, confiables y memorables, respaldadas por la tecnología y el compromiso con el juego responsable. Al mismo tiempo, consolidaremos una cultura organizacional en la que el talento humano encuentre oportunidades para crecer, aportar, inspirar y transformar, construyendo juntos un futuro de crecimiento sostenible, confianza y evolución constante.';
$mediaUrl = static fn($path) => '/yaconecta/public' . htmlspecialchars($path, ENT_QUOTES, 'UTF-8');
?>
<body class="about-page">
<?php require_once dirname(__DIR__, 1) . '/partials/navbar.php'; ?>
<main>
    <section class="about-hero">
        <div class="about-hero__photo"></div><div class="about-hero__shade"></div>
        <div class="about-hero__content">
            <span class="eyebrow"><i></i> YA CONECTA</span>
            <h1>Quiénes <em>Somos</em></h1>
            <p>En Yajuego trabajamos para generar experiencias de entretenimiento seguras, innovadoras y responsables, conectando personas y oportunidades.</p>
            <a class="about-button" href="#empresa">Conoce nuestra historia <span aria-hidden="true">→</span></a>
        </div>
        <div class="about-hero__motto">JUEGOS<br>PERSONAS<br>RESULTADOS<br>SIEMPRE JUNTOS<i></i></div>
    </section>
    <nav class="about-tabs" aria-label="Secciones de Quiénes Somos">
        <a class="is-active" href="#empresa"><span>▦</span>Nuestra Empresa</a><a href="#mision-vision"><span>◎</span>Misión y Visión</a><a href="#valores"><span>◇</span>Valores</a><a href="#cultura"><span>♧</span>Cultura</a><a href="#procesos"><span>⌁</span>Procesos</a><a href="#organigrama"><span>▤</span>Organigrama</a>
    </nav>
    <div class="about-content">
        <section class="company-grid" id="empresa">
            <div class="company-copy">
                <span class="section-kicker">NUESTRA EMPRESA</span>
                <h2><?= htmlspecialchars($historia['title'] ?: 'Una plataforma que conecta más que juego') ?></h2>
                <p><?= htmlspecialchars($historia['content'] ?: 'En Yajuego trabajamos para generar experiencias seguras e innovadoras.') ?></p>
            </div>
            <div class="company-pillars">
                <article><b class="pillar-icon blue">♢</b><div><h3>Operación segura<br>y regulada</h3><p>Cumplimos con la normativa y los más altos estándares de seguridad.</p></div></article>
                <article><b class="pillar-icon orange">♧</b><div><h3>Entretenimiento<br>responsable</h3><p>Promovemos un juego consciente y el bienestar de nuestros usuarios.</p></div></article>
                <article><b class="pillar-icon amber">♧</b><div><h3>Innovación constante</h3><p>Desarrollamos soluciones digitales para una mejor experiencia.</p></div></article>
                <article><b class="pillar-icon green">⇄</b><div><h3>Alianzas estratégicas</h3><p>Construimos relaciones de largo plazo que impulsan nuestro crecimiento.</p></div></article>
            </div>
        </section>
        <section class="mission-grid" id="mision-vision">
            <article class="statement-card mission-card"><span class="statement-icon">◎</span><div class="statement-body"><span class="section-kicker">NUESTRO PROPÓSITO</span><h2>Nuestra Misión</h2><p class="statement-highlight">“Experiencias que conectan confianza, innovación y diversión responsable.”</p><h3>Declaración completa</h3><p><?= htmlspecialchars($misionCompleta) ?></p></div></article>
            <article class="statement-card vision-card"><span class="statement-icon">◉</span><div class="statement-body"><span class="section-kicker">HACIA DÓNDE VAMOS</span><h2>Nuestra Visión</h2><p class="statement-highlight">“Redefinir el entretenimiento digital en Colombia y crecer junto a nuestro talento.”</p><h3>Declaración completa</h3><p><?= htmlspecialchars($visionCompleta) ?></p></div></article>
        </section>
        <section class="values-section" id="valores">
            <div class="section-heading"><span class="section-kicker">LO QUE NOS GUÍA</span><h2>Nuestros Valores</h2><p>Los pilares que guían nuestro trabajo diario en Yajuego.</p></div>
            <div class="values-grid">
                <?php if (!empty($valores)): foreach ($valores as $val): ?>
                    <article class="value-card"><span><?= htmlspecialchars($val['icon'] ?? '◇') ?></span><h3><?= htmlspecialchars($val['title']) ?></h3><p><?= htmlspecialchars($val['description']) ?></p></article>
                <?php endforeach; else: ?>
                    <article class="value-card"><span>◎</span><h3>Integridad</h3><p>Actuamos con transparencia y responsabilidad en cada decisión.</p></article><article class="value-card"><span>♧</span><h3>Trabajo en equipo</h3><p>Sumamos talentos para alcanzar objetivos compartidos.</p></article><article class="value-card"><span>◇</span><h3>Excelencia</h3><p>Mejoramos continuamente lo que hacemos.</p></article><article class="value-card"><span>↗</span><h3>Innovación</h3><p>Impulsamos experiencias digitales confiables y memorables.</p></article>
                <?php endif; ?>
            </div>
        </section>
        <section class="purpose-feature" id="cultura">
            <div class="story-media">
                <?php if (!empty($media)): ?><div id="mediaCarousel" class="story-carousel">
                    <?php foreach ($media as $idx => $m): ?><div class="media-slide <?= $idx === 0 ? '' : 'hidden' ?>" data-index="<?= $idx ?>">
                        <?php if (($m['type'] ?? '') === 'video'): ?><?php if (strpos($m['file_path'], 'youtube') !== false || strpos($m['file_path'], 'youtu.be') !== false): ?><iframe src="<?= htmlspecialchars($m['file_path']) ?>" title="Historia de Yajuego" allowfullscreen></iframe><?php else: ?><video controls><source src="<?= $mediaUrl($m['file_path']) ?>" type="video/mp4"></video><?php endif; ?><?php else: ?><img src="<?= $mediaUrl($m['file_path']) ?>" alt="<?= htmlspecialchars($m['caption'] ?: 'Historia de Yajuego') ?>"><?php endif; ?>
                        <?php if (!empty($m['caption'])): ?><span class="media-caption"><?= htmlspecialchars($m['caption']) ?></span><?php endif; ?></div><?php endforeach; ?>
                    <?php if (count($media) > 1): ?><button class="carousel-prev" onclick="prevSlide()" aria-label="Anterior">‹</button><button class="carousel-next" onclick="nextSlide()" aria-label="Siguiente">›</button><?php endif; ?>
                </div><?php else: ?><img src="/yaconecta/public/assets/img/quienes-somos-hero.png" alt="Espacio corporativo de Yajuego"><?php endif; ?>
            </div>
            <div class="purpose-copy"><span class="section-kicker">NUESTRO PROPÓSITO</span><h2>Entretener hoy, construir un <em>mejor mañana</em></h2><p><?= htmlspecialchars($cultura['content'] ?: 'Creemos en el poder del entretenimiento como una fuerza positiva, que impulsa el desarrollo, genera oportunidades y contribuye a una sociedad más consciente y segura.') ?></p><div class="purpose-principles"><div><b>Usuario en el centro</b><span>Experiencias confiables.</span></div><div><b>Integridad</b><span>Transparencia en lo que hacemos.</span></div><div><b>Crecimiento sostenible</b><span>Valor para nuestros aliados.</span></div><div><b>Impacto positivo</b><span>Juego responsable.</span></div></div>
                <?php if (!empty($cultura['media_path'])): ?><img class="culture-image" src="<?= $mediaUrl($cultura['media_path']) ?>" alt="Cultura organizacional"><?php endif; ?>
            </div>
        </section>
        <section class="document-section" id="procesos"><div class="document-heading"><div><span class="section-kicker"><?= htmlspecialchars($mapaProcesos['subtitle'] ?? 'ESTRATEGIA Y CALIDAD') ?></span><h2><?= htmlspecialchars($mapaProcesos['title'] ?? 'Mapa de Procesos SGC') ?></h2><p><?= htmlspecialchars($mapaProcesos['content'] ?? '') ?></p></div><?php if (!empty($mapaProcesos['media_path'])): ?><button class="about-button compact" onclick="abrirModalImagen('<?= $mediaUrl($mapaProcesos['media_path']) ?>','Mapa de Procesos SGC')">Ver a pantalla completa →</button><?php endif; ?></div><?php if (!empty($mapaProcesos['media_path'])): ?><img class="document-image" src="<?= $mediaUrl($mapaProcesos['media_path']) ?>" alt="Mapa de Procesos" onclick="abrirModalImagen(this.src,'Mapa de Procesos SGC')"><?php else: ?><p class="empty-document">Mapa de procesos disponible próximamente.</p><?php endif; ?></section>
        <section class="document-section" id="organigrama"><div class="document-heading"><div><span class="section-kicker"><?= htmlspecialchars($organigrama['subtitle'] ?? 'ESTRUCTURA ORGANIZACIONAL') ?></span><h2><?= htmlspecialchars($organigrama['title'] ?? 'Organigrama Institucional') ?></h2><p><?= htmlspecialchars($organigrama['content'] ?? '') ?></p></div><?php if (!empty($organigrama['media_path'])): ?><a class="about-button compact" href="<?= $mediaUrl($organigrama['media_path']) ?>" target="_blank">Descargar organigrama →</a><?php endif; ?></div><?php if (!empty($organigrama['media_path'])): ?><img class="document-image" src="<?= $mediaUrl($organigrama['media_path']) ?>" alt="Organigrama institucional" onclick="abrirModalImagen(this.src,'Organigrama Institucional')"><?php else: ?><p class="empty-document">Organigrama disponible próximamente.</p><?php endif; ?></section>
    </div>
</main>
<div id="modalImage" class="image-modal" onclick="cerrarModalImagen()"><div class="image-modal__inner" onclick="event.stopPropagation()"><button onclick="cerrarModalImagen()" aria-label="Cerrar">×</button><h3 id="modalImageTitle"></h3><img id="modalImageSrc" src="" alt="Vista ampliada"></div></div>
<script>
let currentSlide=0;const slides=document.querySelectorAll('.media-slide');function showSlide(index){if(!slides.length)return;slides.forEach(s=>s.classList.add('hidden'));currentSlide=(index+slides.length)%slides.length;slides[currentSlide].classList.remove('hidden')}function nextSlide(){showSlide(currentSlide+1)}function prevSlide(){showSlide(currentSlide-1)}function abrirModalImagen(src,titulo){document.getElementById('modalImageSrc').src=src;document.getElementById('modalImageTitle').textContent=titulo;document.getElementById('modalImage').classList.add('is-open')}function cerrarModalImagen(){document.getElementById('modalImage').classList.remove('is-open')}
</script>
<?php require_once dirname(__DIR__, 1) . '/partials/footer.php'; ?>
</body>
</html>
