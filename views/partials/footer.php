<?php
// Consulta global de aplicativos directamente desde la base de datos para todas las páginas
if (!isset($appsFooterGlobal)) {
    try {
        $appsFooterGlobal = \App\Models\AppItem::obtenerTodos();
    } catch (\Throwable $e) {
        $appsFooterGlobal = [];
    }
}

// Si la base de datos no retorna nada o falla, mantiene las Apps Corporativas por defecto
if (empty($appsFooterGlobal)) {
    $appsFooterGlobal = [
        ['name' => 'BUK', 'url' => '#'],
        ['name' => 'Correo Corporativo', 'url' => '#'],
        ['name' => 'Plataforma de Auditoría', 'url' => '#'],
        ['name' => 'YATI', 'url' => '#']
    ];
}
?>

<footer class="bg-yjBlueDark text-white mt-12 py-10 border-t-4 border-yjOrange w-full">
    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
        
        <!-- REDES SOCIALES (IZQUIERDA) -->
        <div class="space-y-3 text-center md:text-left">
            <div class="flex items-center justify-center md:justify-start gap-2">
                <img src="/yaconecta/public/assets/img/logo.png" 
                    alt="Yajuego" 
                    class="h-10 sm:h-12 w-auto object-contain brightness-0 invert transition-all" 
                    onerror="this.style.display='none'"
                >
            </div>
            <p class="text-xs text-gray-300">Síguenos en nuestros canales oficiales para mantenerte conectado.</p>
            
            <!-- Iconos de Redes Sociales -->
            <div class="flex justify-center md:justify-start gap-3 pt-2">
                <a href="https://www.linkedin.com/company/yajuego" target="_blank" rel="noopener noreferrer" 
                   class="w-9 h-9 bg-white/10 hover:bg-yjOrange text-white rounded-full flex items-center justify-center transition shadow-sm hover:scale-110" 
                   title="LinkedIn">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.28 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.75M6.46 10.9v8.37H9.25V10.9H6.46M7.86 6.74a1.62 1.62 0 1 0 0 3.24 1.62 1.62 0 0 0 0-3.24z"/></svg>
                </a>

                <a href="https://www.instagram.com/yajuego" target="_blank" rel="noopener noreferrer" 
                   class="w-9 h-9 bg-white/10 hover:bg-yjOrange text-white rounded-full flex items-center justify-center transition shadow-sm hover:scale-110" 
                   title="Instagram">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                </a>

                <a href="https://www.facebook.com/yajuego" target="_blank" rel="noopener noreferrer" 
                   class="w-9 h-9 bg-white/10 hover:bg-yjOrange text-white rounded-full flex items-center justify-center transition shadow-sm hover:scale-110" 
                   title="Facebook">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                </a>
            </div>
        </div>

        <!-- APPLICATIVOS CORPORATIVOS (DERECHA) -->
        <div class="space-y-3 text-center md:text-right">
            <h4 class="font-black text-sm text-yjOrange uppercase tracking-wider">Aplicativos Corporativos</h4>
            <div class="flex flex-wrap justify-center md:justify-end gap-2 pt-1">
                <?php foreach ($appsFooterGlobal as $appItem): ?>
                    <?php 
                        $nombreApp = $appItem['name'] ?? $appItem['nombre'] ?? 'App';
                        $urlApp = $appItem['url'] ?? $appItem['url_acceso'] ?? '#';
                        $iniciales = strtoupper(substr($nombreApp, 0, 2));
                    ?>
                    <a href="<?= htmlspecialchars($urlApp) ?>" 
                       target="_blank" 
                       rel="noopener noreferrer"
                       class="inline-flex items-center gap-2 bg-white/10 hover:bg-yjOrange text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition hover:shadow-md group">
                        <span class="w-5 h-5 bg-yjOrange group-hover:bg-yjBlue text-white text-[9px] font-black rounded flex items-center justify-center uppercase transition-colors">
                            <?= htmlspecialchars($iniciales) ?>
                        </span>
                        <span><?= htmlspecialchars($nombreApp) ?></span>
                        <span class="text-[10px]">↗</span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

    </div>

    <!-- COPYRIGHT -->
    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 mt-8 pt-4 border-t border-white/10 text-center text-[11px] text-gray-400 font-medium">
        © <?= date('Y') ?> Yajuego. Todos los derechos reservados. | Ya.Net Intranet
    </div>
</footer>


<!-- MODAL POP-UP CORPORATIVO AUTOMÁTICO -->
<div id="modalPopupOverlay" class="fixed inset-0 bg-black/70 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full overflow-hidden transform transition-all scale-95 opacity-0 duration-300" id="modalPopupContainer">
        
        <!-- CABECERA CON BOTÓN X -->
        <div class="relative bg-yjBlue text-white p-6 pb-5">
            <button onclick="cerrarPopup()" class="absolute top-4 right-4 text-white/80 hover:text-white bg-white/10 hover:bg-white/20 w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm transition">
                ✕
            </button>
            <span class="bg-yjOrange text-white text-[10px] font-black uppercase tracking-widest px-2.5 py-0.5 rounded-full inline-block mb-2">
                Comunicado Oficial
            </span>
            <h3 id="popupTitulo" class="text-xl font-black leading-tight"></h3>
        </div>

        <!-- CUERPO Y CONTENIDO -->
        <div class="p-6 space-y-4">
            <div id="popupImagenContainer" class="hidden rounded-2xl overflow-hidden border border-gray-100 max-h-60">
                <img id="popupImagen" src="" alt="Banner Notificación" class="w-full h-full object-cover">
            </div>

            <p id="popupContenido" class="text-xs text-gray-600 leading-relaxed space-y-2"></p>
            
            <!-- BOTÓN DE ACCIÓN / ENLACE -->
            <div class="pt-2 flex items-center justify-end gap-3">
                <button onclick="cerrarPopup()" class="px-5 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs transition">
                    Cerrar
                </button>
                <a id="popupLink" href="#" target="_blank" class="hidden px-6 py-2.5 rounded-xl bg-yjOrange hover:bg-orange-600 text-white font-bold text-xs transition shadow-sm">
                    <span id="popupTextoBoton">Ver Más</span> →
                </a>
            </div>
        </div>

    </div>
</div>

<script>
let currentPopupId = null;

document.addEventListener('DOMContentLoaded', () => {
    fetch('/yaconecta/public/api/popup-activo')
        .then(res => res.json())
        .then(data => {
            // Dentro del fetch en views/partials/footer.php:
        if (data.success && data.popup) {
            const pop = data.popup;
            currentPopupId = pop.id;

            if (sessionStorage.getItem('popup_visto_' + currentPopupId)) {
                return;
            }

            // Muestra la categoría del requerimiento (ej: Eventos Próximos, Nuevos Documentos)
            document.getElementById('popupTipoBadge').textContent = pop.tipo || 'Comunicado Oficial';
            document.getElementById('popupTitulo').textContent = pop.titulo;
            document.getElementById('popupContenido').innerHTML = pop.contenido.replace(/\n/g, '<br>');

            if (pop.imagen_path) {
                document.getElementById('popupImagen').src = pop.imagen_path;
                document.getElementById('popupImagenContainer').classList.remove('hidden');
            }

            if (pop.link_destino) {
                const btnLink = document.getElementById('popupLink');
                btnLink.href = pop.link_destino;
                if (pop.texto_boton) {
                    document.getElementById('popupTextoBoton').textContent = pop.texto_boton;
                }
                btnLink.classList.remove('hidden');
            }

            const overlay = document.getElementById('modalPopupOverlay');
            const container = document.getElementById('modalPopupContainer');
            
            overlay.classList.remove('hidden');
            setTimeout(() => {
                container.classList.remove('scale-95', 'opacity-0');
                container.classList.add('scale-100', 'opacity-100');
            }, 50);
        }
        });
});

function cerrarPopup() {
    if (currentPopupId) {
        sessionStorage.setItem('popup_visto_' + currentPopupId, 'true');
    }
    const container = document.getElementById('modalPopupContainer');
    const overlay = document.getElementById('modalPopupOverlay');

    container.classList.remove('scale-100', 'opacity-100');
    container.classList.add('scale-95', 'opacity-0');
    setTimeout(() => {
        overlay.classList.add('hidden');
    }, 200);
}
</script>