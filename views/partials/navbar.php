<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$usuario =$_SESSION['usuario'] ?? null;
$estaLogueado = !empty($usuario);
$esAdmin =$estaLogueado && in_array((int)($usuario['role_id'] ?? 0), [1, 2]);$nombreUsuario = $usuario['nombre'] ?? 'Invitado';$iniciales = $estaLogueado ? strtoupper(substr($nombreUsuario, 0, 2)) : 'IN';
?>

<!-- BARRA DE NAVEGACIÓN PRINCIPAL (SUPERIOR) -->
<header class="home-header bg-yjBlue text-white sticky top-0 z-40 shadow-md">
    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">
        
        <!-- SECCIÓN IZQUIERDA: BOTÓN DE MENÚ + LOGO + ENLACES -->
        <div class="flex items-center gap-4 sm:gap-6">
            <button onclick="toggleSidebar()" class="home-menu-button p-2 text-blue-100 hover:text-yjOrange hover:bg-white/10 rounded-xl transition flex items-center justify-center" title="Abrir Menú Principal">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            <a href="/yaconecta/public/" class="home-brand flex items-center gap-2 group shrink-0">
                <img src="/yaconecta/public/assets/img/yajuego-logo.png" alt="Yajuego" class="home-brand-logo w-20 h-12 sm:w-24 sm:h-14 object-contain transition-transform group-hover:scale-105">
                <div class="hidden sm:block">
                    <span class="font-black text-base tracking-wide block leading-none">Ya.Net</span>
                    <span class="text-[9px] text-blue-200 tracking-wider block font-semibold uppercase">Intranet corporativa</span>
                </div>
            </a>

            <nav class="home-nav hidden md:flex items-center gap-4 text-xs font-bold border-l border-blue-800/80 pl-4 sm:pl-6">
                <a href="/yaconecta/public/" class="text-white hover:text-yjOrange transition flex items-center gap-1.5 py-1">
                    Home
                </a>
                
                <div class="relative group">
                    <a href="/yaconecta/public/quienes-somos" class="text-blue-100 hover:text-yjOrange transition flex items-center gap-1 py-1">
                        Quiénes Somos <span class="text-[9px]">▼</span>
                    </a>
                    <div class="absolute left-0 mt-1 w-48 bg-white text-gray-800 rounded-xl shadow-xl border border-gray-100 py-2 hidden group-hover:block animate-fadeIn z-50">
                        <a href="/yaconecta/public/quienes-somos#historia" class="block px-4 py-2 text-xs font-semibold hover:bg-orange-50 hover:text-yjOrange transition">Nuestra Historia</a>
                        <a href="/yaconecta/public/quienes-somos#mision-vision" class="block px-4 py-2 text-xs font-semibold hover:bg-orange-50 hover:text-yjOrange transition">Misión y Visión</a>
                        <a href="/yaconecta/public/quienes-somos#valores" class="block px-4 py-2 text-xs font-semibold hover:bg-orange-50 hover:text-yjOrange transition">Valores Corporativos</a>
                    </div>
                </div>
                <a href="/yaconecta/public/documentos" class="text-blue-100 hover:text-yjOrange transition flex items-center gap-1 py-1">Gestión Documental <span class="text-[9px]">▼</span></a>
                <a href="/yaconecta/public/directorio" class="text-blue-100 hover:text-yjOrange transition flex items-center gap-1 py-1">Directorio Corporativo</a>
                <a href="/yaconecta/public/apps" class="text-blue-100 hover:text-yjOrange transition flex items-center gap-1 py-1 <?= ($pageTitle ?? '') === 'Ya.Net | Aplicaciones Corporativas' ? 'apps-nav-active' : '' ?>">Aplicaciones Corporativas</a>
            </nav>
        </div>

        <!-- SECCIÓN CENTRAL: BUSCADOR PREDICTIVO EN TIEMPO REAL -->
        <div class="home-search flex-1 max-w-xs sm:max-w-md mx-2 relative">
            <div class="relative flex items-center">
                <input type="text" 
                       id="globalSearchInput" 
                       autocomplete="off"
                       placeholder="Buscar página, persona o documento..." 
                       class="w-full bg-blue-900/60 text-white placeholder-blue-300 text-xs rounded-xl pl-8 pr-3 py-2 border border-blue-400/30 focus:outline-none focus:border-yjOrange focus:bg-blue-950 transition-all duration-300">
                <svg class="absolute left-2.5 w-3.5 h-3.5 text-blue-300 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <circle cx="11" cy="11" r="7" stroke-width="2"></circle>
                    <path stroke-width="2" stroke-linecap="round" d="m20 20-4-4"></path>
                </svg>
            </div>

            <!-- DESPLEGABLE DE RESULTADOS -->
            <div id="searchResultsDropdown" class="absolute left-0 right-0 mt-2 bg-white text-gray-800 rounded-2xl shadow-2xl border border-gray-100 py-2 hidden z-50 max-h-96 overflow-y-auto text-xs">
                <div id="searchResultsContent" class="divide-y divide-gray-100"></div>
            </div>
        </div>

        <!-- SECCIÓN DERECHA: LOGUEO Y USUARIO -->
        <div class="flex items-center gap-3 shrink-0">
            <?php if ($estaLogueado): ?>
                <div class="flex items-center gap-2.5 bg-blue-900/40 p-1.5 pl-3 rounded-xl border border-blue-400/20">
                    <div class="w-7 h-7 rounded-lg bg-yjOrange text-white font-black text-xs flex items-center justify-center shrink-0 shadow-xs">
                        <?= $iniciales ?>
                    </div>
                    <div class="hidden xl:block text-left">
                        <span class="block text-xs font-black leading-tight text-white"><?= htmlspecialchars($nombreUsuario) ?></span>
                        <span class="block text-[9px] text-blue-300 font-semibold"><?= $esAdmin ? 'Administrador' : 'Colaborador' ?></span>
                    </div>
                </div>

                <a href="/yaconecta/public/logout" class="bg-red-500/80 hover:bg-red-600 text-white p-2 sm:px-3 sm:py-2 rounded-xl text-xs font-bold transition flex items-center gap-1" title="Cerrar Sesión">
                    <span class="hidden sm:inline">Salir</span>
                </a>
            <?php else: ?>
                <a href="/yaconecta/public/login" class="bg-yjOrange hover:bg-orange-600 text-white px-4 py-2 rounded-xl text-xs font-bold transition shadow-sm flex items-center gap-1.5">
                    Iniciar Sesión
                </a>
            <?php endif; ?>
        </div>

    </div>
</header>


<!-- MENÚ LATERAL DESPLEGABLE (OFF-CANVAS DRAWER) -->
<div id="sidebarBackdrop" onclick="toggleSidebar()" class="fixed inset-0 bg-black/60 backdrop-blur-xs z-50 hidden transition-opacity duration-300"></div>

<aside id="sidebarMenu" class="fixed top-0 left-0 h-full w-80 bg-white z-50 shadow-2xl transform -translate-x-full transition-transform duration-300 ease-in-out flex flex-col justify-between overflow-y-auto">
    
    <div>
        <div class="p-5 bg-yjBlue text-white flex items-center justify-between border-b border-blue-900 sticky top-0 z-10">
            <div class="flex items-center gap-3">
                <img src="/yaconecta/public/assets/img/yajuego-logo.png" alt="Yajuego" class="w-14 h-9 object-contain">
                <div>
                    <h2 class="font-black text-sm leading-tight">Menú Principal</h2>
                    <p class="text-[10px] text-blue-200 uppercase font-semibold">Intranet Ya.Net</p>
                </div>
            </div>
            <button onclick="toggleSidebar()" class="text-blue-200 hover:text-white p-1 rounded-lg text-lg font-bold">✕</button>
        </div>

        <nav class="p-4 space-y-1.5 text-xs font-bold">
            <div class="text-[10px] uppercase font-black text-gray-400 px-3 pt-2 pb-1">Navegación General</div>

            <a href="/yaconecta/public/" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-gray-700 hover:bg-orange-50 hover:text-yjOrange transition">
                Inicio (Home)
            </a>

            <a href="/yaconecta/public/documentos" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-gray-700 hover:bg-orange-50 hover:text-yjOrange transition">
                Gestión Documental
            </a>

            <a href="/yaconecta/public/directorio" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-gray-700 hover:bg-orange-50 hover:text-yjOrange transition">
                Directorio Corporativo
            </a>

            <a href="/yaconecta/public/clasificados" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-gray-700 hover:bg-orange-50 hover:text-yjOrange transition">
                Clasificados — Entre Nosotros
            </a>

            <a href="/yaconecta/public/ideas" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-gray-700 hover:bg-orange-50 hover:text-yjOrange transition">
                Banco de Ideas (Innovamos)
            </a>

            <a href="/yaconecta/public/noticias" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-gray-700 hover:bg-orange-50 hover:text-yjOrange transition">
                Galería & Noticias
            </a>

            <a href="/yaconecta/public/apps" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-gray-700 hover:bg-orange-50 hover:text-yjOrange transition">
                Aplicativos Yajuego
            </a>

            <a href="/yaconecta/public/centro-ayuda" class="flex items-center gap-3 p-3 bg-slate-50 hover:bg-blue-50 text-slate-800 hover:text-blue-700 font-semibold text-xs rounded-xl transition border border-slate-100 shadow-sm">
                <span>Centro de Ayuda</span>
            </a>

            <?php if ($esAdmin): ?>
                <div class="pt-4 mt-3 border-t border-gray-100 space-y-1">
                    <div class="text-[10px] uppercase font-black text-amber-600 px-3 pb-1 tracking-wider">Gestión & Control (Admin)</div>
                    
                    <a href="/yaconecta/public/admin/banners" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-yjBlue bg-slate-50 hover:bg-yjBlue hover:text-white transition">
                        Gestor de Banners
                    </a>

                    <a href="/yaconecta/public/admin/quienes-somos" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-100 transition font-bold text-xs text-slate-700">
                        <span>Gestión Quiénes Somos</span>
                    </a>

                    
                    <a href="/yaconecta/public/admin/clasificados" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-yjBlue bg-slate-50 hover:bg-yjBlue hover:text-white transition">
                        Moderación Clasificados
                    </a>

                    <a href="/yaconecta/public/admin/documentos" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-yjBlue bg-slate-50 hover:bg-yjBlue hover:text-white transition">
                        Administrador Documental
                    </a>

                    <a href="/yaconecta/public/admin/colaboradores" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-yjBlue bg-slate-50 hover:bg-yjBlue hover:text-white transition">
                        Gestión Colaboradores
                    </a>

                    <a href="/yaconecta/public/admin/ideas" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-yjBlue bg-slate-50 hover:bg-yjBlue hover:text-white transition">
                        Evaluación Banco Ideas
                    </a>

                    <a href="/yaconecta/public/admin/noticias" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-yjBlue bg-slate-50 hover:bg-yjBlue hover:text-white transition">
                        Publicación Noticias
                    </a>

                    <a href="/yaconecta/public/admin/apps" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-yjBlue bg-slate-50 hover:bg-yjBlue hover:text-white transition">
                        Administrador Aplicativos
                    </a>
                    <a href="/yaconecta/public/admin/popups" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-yjBlue bg-slate-50 hover:bg-yjBlue hover:text-white transition">
                        Gestor de Pop-ups Informativos
                    </a>

                    <?php if (($_SESSION['usuario']['role_id'] ?? 3) <= 2): ?>
                        <a href="/yaconecta/public/admin/centro-ayuda" class="flex items-center gap-3 p-3 bg-slate-50 hover:bg-blue-50 text-blue-900 hover:text-blue-700 font-bold text-xs rounded-xl transition border border-slate-100 shadow-sm">
                            <span>Gestión Centro de Ayuda</span>
                        </a>
                    <?php endif; ?>

                </div>
            <?php endif; ?>
        </nav>
    </div>

    <div class="p-4 border-t border-gray-100 bg-gray-50 text-center text-[10px] text-gray-400 font-semibold shrink-0">
        Ya.Net v2.0 • Intranet Yajuego 2026
    </div>

</aside>

<!-- SCRIPT DE NAVEGACIÓN Y BUSCADOR LIVE SEARCH -->
<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebarMenu');
        const backdrop = document.getElementById('sidebarBackdrop');

        if (sidebar.classList.contains('-translate-x-full')) {
            sidebar.classList.remove('-translate-x-full');
            backdrop.classList.remove('hidden');
        } else {
            sidebar.classList.add('-translate-x-full');
            backdrop.classList.add('hidden');
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('globalSearchInput');
        const searchDropdown = document.getElementById('searchResultsDropdown');
        const searchContent = document.getElementById('searchResultsContent');
        let timeout = null;

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(timeout);
                const q = this.value.trim();

                if (q.length < 2) {
                    searchDropdown.classList.add('hidden');
                    return;
                }

                timeout = setTimeout(() => {
                    fetch(`/yaconecta/public/buscar?q=${encodeURIComponent(q)}`)
                        .then(res => res.json())
                        .then(data => {
                            let html = '';

                            // 1. Secciones o Páginas del Sitio
                            if (data.secciones && data.secciones.length > 0) {
                                html += `<div class="p-2 bg-gray-50 text-[10px] font-black uppercase text-gray-400 tracking-wider">Secciones de la Intranet</div>`;
                                data.secciones.forEach(s => {
                                    html += `
                                        <a href="${s.url}" class="flex items-center justify-between px-4 py-2.5 hover:bg-orange-50 hover:text-yjOrange transition font-bold text-gray-700">
                                            <div class="flex items-center gap-2.5">
                                                <span class="text-sm">${s.icono}</span>
                                                <span>${s.titulo}</span>
                                            </div>
                                            <span class="text-[9px] uppercase font-black px-2 py-0.5 rounded-full bg-orange-100 text-yjOrange">Ir →</span>
                                        </a>
                                    `;
                                });
                            }

                            // 2. Resultados en Base de Datos
                            if (data.resultados && data.resultados.length > 0) {
                                html += `<div class="p-2 bg-gray-50 text-[10px] font-black uppercase text-gray-400 tracking-wider">Coincidencias de Búsqueda</div>`;
                                data.resultados.forEach(r => {
                                    html += `
                                        <a href="${r.url}" class="flex items-center justify-between px-4 py-2.5 hover:bg-orange-50 transition">
                                            <div class="flex items-center gap-2.5 truncate">
                                                <span class="text-sm">${r.icono}</span>
                                                <div class="truncate">
                                                    <p class="font-bold text-gray-800 truncate">${r.titulo}</p>
                                                    <p class="text-[10px] text-gray-400 truncate">${r.subtitulo || ''}</p>
                                                </div>
                                            </div>
                                            <span class="text-[9px] uppercase font-black px-2 py-0.5 rounded-full bg-blue-50 text-yjBlue">Ver</span>
                                        </a>
                                    `;
                                });
                            }

                            if (html === '') {
                                html = `<div class="p-4 text-center text-gray-400 text-xs">No se encontraron coincidencia para <b>"${q}"</b></div>`;
                            }

                            searchContent.innerHTML = html;
                            searchDropdown.classList.remove('hidden');
                        })
                        .catch(() => {
                            searchDropdown.classList.add('hidden');
                        });
                }, 200);
            });

            document.addEventListener('click', (e) => {
                if (!searchInput.contains(e.target) && !searchDropdown.contains(e.target)) {
                    searchDropdown.classList.add('hidden');
                }
            });
        }
    });
</script>
