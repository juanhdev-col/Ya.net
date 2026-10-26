<?php
$slides = [];
try {
    if (class_exists('\App\Models\Banner')) {
        $slides = \App\Models\Banner::obtenerActivos();
    }
} catch (\Throwable $e) {
    $slides = [];
}

// Fallback por si la base de datos no tiene banners creados
if (empty($slides)) {
    $slides = [
        [
            'id' => 0,
            'titulo' => 'Mantente conectado',
            'subtitulo' => 'Noticias, novedades y todo lo que necesitas saber de nuestra organización.',
            'etiqueta' => 'Ya.Net',
            'tipo_media' => 'imagen',
            'url_media' => '',
            'enlace_boton' => '#',
            'texto_boton' => 'Explorar novedades'
        ]
    ];
}
?>

<div id="homeCarousel" class="home-hero relative w-full h-[360px] md:h-[440px] overflow-hidden bg-gray-950 text-white">
    <!-- Slides -->
    <div class="carousel-inner relative w-full h-full">
        <?php foreach ($slides as $index => $slide): ?>
            <?php 
                $tieneTextoHtml = !empty($slide['titulo']) || !empty($slide['subtitulo']);
            ?>
            <div class="carousel-slide absolute inset-0 w-full h-full transition-opacity duration-700 ease-in-out <?= $index === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0 pointer-events-none' ?>" data-index="<?= $index ?>">
                
                <?php if (!empty($slide['url_media'])): ?>
                    <?php if ($slide['tipo_media'] === 'video'): ?>
                        <!-- Video de Fondo (Relleno Desenfocado) -->
                        <video autoplay loop muted playsinline class="absolute inset-0 w-full h-full object-cover blur-xl opacity-40 scale-110 pointer-events-none">
                            <source src="<?= htmlspecialchars($slide['url_media']) ?>" type="video/mp4">
                        </video>
                        <!-- Video Principal (Sin Recortes) -->
                        <video autoplay loop muted playsinline class="relative z-10 w-full h-full object-contain mx-auto">
                            <source src="<?= htmlspecialchars($slide['url_media']) ?>" type="video/mp4">
                        </video>
                    <?php else: ?>
                        <!-- Imagen de Fondo (Relleno Desenfocado) -->
                        <img src="<?= htmlspecialchars($slide['url_media']) ?>" alt="" class="absolute inset-0 w-full h-full object-cover blur-xl opacity-40 scale-110 pointer-events-none">
                        <!-- Imagen Principal (Sin Recortes) -->
                        <img src="<?= htmlspecialchars($slide['url_media']) ?>" alt="<?= htmlspecialchars($slide['titulo']) ?>" class="relative z-10 w-full h-full object-contain mx-auto">
                    <?php endif; ?>

                    <!-- Overlay sutil de texto solo si hay título o subtítulo ingresado -->
                    <?php if ($tieneTextoHtml): ?>
                        <div class="absolute inset-0 z-20 bg-black/40 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="absolute inset-0 bg-gradient-to-r from-yjBlueDark via-yjBlue to-blue-800"></div>
                <?php endif; ?>

                <!-- Superposición de Texto HTML (Solo si se especificó título/descripción) -->
                <?php if ($tieneTextoHtml): ?>
                    <div class="home-hero-copy absolute inset-0 z-30 h-full flex flex-col justify-center items-center text-center px-6 md:px-16 max-w-4xl mx-auto space-y-3">
                        <?php if (!empty($slide['etiqueta'])): ?>
                            <span class="px-3 py-1 bg-yjBlue/90 backdrop-blur-md text-white text-[10px] font-black rounded-full uppercase tracking-widest border border-white/20 shadow-sm">
                                <?= htmlspecialchars($slide['etiqueta']) ?>
                            </span>
                        <?php endif; ?>

                        <?php if (!empty($slide['titulo'])): ?>
                            <h1 class="text-xl md:text-3xl lg:text-4xl font-extrabold tracking-tight drop-shadow-md">
                                <?= htmlspecialchars($slide['titulo']) ?>
                            </h1>
                        <?php endif; ?>

                        <?php if (!empty($slide['subtitulo'])): ?>
                            <p class="text-xs md:text-sm text-gray-100 max-w-2xl font-medium drop-shadow-sm">
                                <?= htmlspecialchars($slide['subtitulo']) ?>
                            </p>
                        <?php endif; ?>

                        <?php if (!empty($slide['enlace_boton'])): ?>
                            <div class="pt-2">
                                <a href="<?= htmlspecialchars($slide['enlace_boton']) ?>" class="px-5 py-2 bg-yjOrange hover:bg-orange-600 text-white font-bold text-xs rounded-xl shadow-lg transition transform hover:scale-105 inline-block">
                                    <?= htmlspecialchars($slide['texto_boton'] ?: 'Ver Más') ?> →
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Indicadores/Puntos de navegación -->
    <?php if (count($slides) > 1): ?>
        <div class="absolute bottom-3 left-1/2 transform -translate-x-1/2 z-40 flex space-x-2">
            <?php foreach ($slides as $index => $slide): ?>
                <button onclick="goToSlide(<?= $index ?>)" class="carousel-indicator w-2.5 h-2.5 rounded-full border border-white/50 transition <?= $index === 0 ? 'bg-yjOrange scale-110' : 'bg-white/40' ?>" data-slide="<?= $index ?>"></button>
            <?php endforeach; ?>
        </div>

        <!-- Botones Siguiente / Anterior -->
        <button onclick="prevSlide()" class="absolute left-3 top-1/2 -translate-y-1/2 z-40 p-2 rounded-full bg-black/50 hover:bg-black/80 text-white transition text-xs backdrop-blur-sm">❮</button>
        <button onclick="nextSlide()" class="absolute right-3 top-1/2 -translate-y-1/2 z-40 p-2 rounded-full bg-black/50 hover:bg-black/80 text-white transition text-xs backdrop-blur-sm">❯</button>
    <?php endif; ?>
</div>

<script>
let currentSlide = 0;
const slides = document.querySelectorAll('.carousel-slide');
const indicators = document.querySelectorAll('.carousel-indicator');
const totalSlides = slides.length;

function showSlide(index) {
    if (totalSlides <= 1) return;
    slides.forEach((slide, i) => {
        if (i === index) {
            slide.classList.remove('opacity-0', 'pointer-events-none', 'z-0');
            slide.classList.add('opacity-100', 'z-10');
        } else {
            slide.classList.remove('opacity-100', 'z-10');
            slide.classList.add('opacity-0', 'pointer-events-none', 'z-0');
        }
    });

    indicators.forEach((ind, i) => {
        if (i === index) {
            ind.classList.add('bg-yjOrange', 'scale-110');
            ind.classList.remove('bg-white/40');
        } else {
            ind.classList.remove('bg-yjOrange', 'scale-110');
            ind.classList.add('bg-white/40');
        }
    });
    currentSlide = index;
}

function nextSlide() {
    let next = (currentSlide + 1) % totalSlides;
    showSlide(next);
}

function prevSlide() {
    let prev = (currentSlide - 1 + totalSlides) % totalSlides;
    showSlide(prev);
}

function goToSlide(index) {
    showSlide(index);
}

if (totalSlides > 1) {
    setInterval(nextSlide, 6000);
}
</script>
