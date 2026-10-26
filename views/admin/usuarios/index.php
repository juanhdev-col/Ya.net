<!DOCTYPE html>
<html lang="es">

<?php 
    $pageTitle = "Ya.Net | Administración de Usuarios y Permisos";
    require_once __DIR__ . '/../../partials/head.php'; 
?>

<body class="bg-gray-100 font-sans flex flex-col min-h-screen text-gray-800">

    <?php require_once __DIR__ . '/../../partials/navbar.php'; ?>

    <main class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full flex-grow space-y-8">
        
        <!-- Encabezado Principal -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <a href="/yaconecta/public/admin" class="text-xs font-bold text-gray-400 hover:text-yjBlue transition">← Volver al Dashboard</a>
                </div>
                <h1 class="text-2xl font-black text-yjBlue mt-1 flex items-center gap-2">
                    <span class="w-3 h-7 bg-yjOrange rounded-full"></span> Gestión de Usuarios y Permisos
                </h1>
                <p class="text-xs text-gray-500 mt-0.5">Control de credenciales, roles institucionales y niveles de acceso a la Intranet.</p>
            </div>

            <button onclick="document.getElementById('modalNuevoUsuario').classList.remove('hidden')" class="bg-yjOrange hover:bg-orange-600 text-white font-bold text-xs px-5 py-2.5 rounded-xl transition flex items-center gap-2 shadow-sm cursor-pointer">
                <span>👤+</span> Crear Nuevo Usuario
            </button>
        </div>

        <!-- Tarjetas de Resumen de Roles -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <?php foreach ($roles as $nombreRol => $descRol): ?>
                <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-2xs">
                    <span class="text-[9px] font-black uppercase tracking-wider text-yjOrange bg-orange-50 px-2 py-0.5 rounded-md">
                        PERFIL
                    </span>
                    <h3 class="text-xs font-black text-yjBlue mt-2"><?= htmlspecialchars($nombreRol) ?></h3>
                    <p class="text-[10px] text-gray-400 mt-1 leading-tight"><?= htmlspecialchars($descRol) ?></p>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Filtros y Búsqueda -->
        <div class="bg-white p-4 rounded-2xl border border-gray-200 shadow-2xs flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                <input type="text" placeholder="Buscar por nombre, correo o cargo..." class="px-4 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-yjOrange outline-none w-full sm:w-72">
                
                <select class="px-3 py-2 text-xs border border-gray-300 rounded-xl bg-white focus:ring-2 focus:ring-yjOrange outline-none font-medium">
                    <option value="">Todos los Roles</option>
                    <?php foreach (array_keys($roles) as $rolOpt): ?>
                        <option value="<?= htmlspecialchars($rolOpt) ?>"><?= htmlspecialchars($rolOpt) ?></option>
                    <?php endforeach; ?>
                </select>

                <select class="px-3 py-2 text-xs border border-gray-300 rounded-xl bg-white focus:ring-2 focus:ring-yjOrange outline-none font-medium">
                    <option value="">Todas las Áreas</option>
                    <?php foreach ($areas as $areaOpt): ?>
                        <option value="<?= htmlspecialchars($areaOpt) ?>"><?= htmlspecialchars($areaOpt) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="text-xs text-gray-400 font-medium">
                Total Registrados: <strong class="text-gray-700"><?= count($usuarios) ?></strong>
            </div>
        </div>

        <!-- Tabla de Usuarios -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-2xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-gray-600">
                    <thead class="bg-gray-50 text-gray-700 font-bold border-b border-gray-200 uppercase text-[10px]">
                        <tr>
                            <th class="p-4">Colaborador</th>
                            <th class="p-4">Área / Cargo</th>
                            <th class="p-4">Rol Asignado</th>
                            <th class="p-4">Estado</th>
                            <th class="p-4">Último Ingreso</th>
                            <th class="p-4 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 font-medium">
                        <?php foreach ($usuarios as $usr): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="p-4">
                                    <div class="font-bold text-gray-800"><?= htmlspecialchars($usr['nombre']) ?></div>
                                    <div class="text-[10px] text-gray-400 font-mono"><?= htmlspecialchars($usr['email']) ?></div>
                                </td>
                                <td class="p-4">
                                    <div class="font-bold text-gray-700"><?= htmlspecialchars($usr['area']) ?></div>
                                    <div class="text-[10px] text-gray-400"><?= htmlspecialchars($usr['cargo']) ?></div>
                                </td>
                                <td class="p-4">
                                    <span class="bg-blue-50 text-yjBlue font-bold text-[10px] px-2.5 py-1 rounded-lg inline-block border border-blue-100">
                                        <?= htmlspecialchars($usr['rol']) ?>
                                    </span>
                                </td>
                                <td class="p-4">
                                    <?php if ($usr['estado'] === 'Activo'): ?>
                                        <span class="bg-green-50 text-green-700 font-bold text-[10px] px-2 py-0.5 rounded-md">Activo</span>
                                    <?php else: ?>
                                        <span class="bg-gray-100 text-gray-500 font-bold text-[10px] px-2 py-0.5 rounded-md">Inactivo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-4 text-gray-400 text-[11px]">
                                    <?= htmlspecialchars($usr['ultimo_ingreso']) ?>
                                </td>
                                <td class="p-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button class="p-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition" title="Editar Permisos">
                                            ✏️
                                        </button>
                                        <a href="/yaconecta/public/admin/usuarios/delete?id=<?= $usr['id'] ?>" onclick="return confirm('¿Desactivar acceso a este usuario?')" class="p-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg transition" title="Inactivar / Eliminar">
                                            🚫
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <!-- Modal Formulario: Vinculación con Active Directory por Username -->
    <div id="modalNuevoUsuario" class="fixed inset-0 bg-black/40 backdrop-blur-xs flex items-center justify-center p-4 z-50 hidden">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 w-full max-w-lg p-6 space-y-6">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <div>
                    <h3 class="text-base font-black text-yjBlue flex items-center gap-2">
                        <span>🔑</span> Asignar Permisos a Usuario de Active Directory
                    </h3>
                    <p class="text-[11px] text-gray-400">Autenticación por LDAP usando el usuario de red (Username).</p>
                </div>
                <button onclick="document.getElementById('modalNuevoUsuario').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 text-lg font-bold">✕</button>
            </div>

            <form action="/yaconecta/public/admin/usuarios/store" method="POST" class="space-y-4 text-xs">
                
                <!-- Usuario de Red (Username) -->
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Usuario de Red / Username (AD) *</label>
                    <div class="flex items-center">
                        <span class="bg-gray-100 border border-r-0 border-gray-300 rounded-l-xl px-3 py-2 text-gray-500 font-mono text-xs">YAJUEGO\</span>
                        <input type="text" name="username" placeholder="ej. yjohana" required class="w-full px-3 py-2 border border-gray-300 rounded-r-xl focus:ring-2 focus:ring-yjOrange outline-none font-mono">
                    </div>
                    <span class="text-[10px] text-gray-400 mt-0.5 block">Nombre de usuario con el que inicia sesión en Windows / Red.</span>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Nombre Completo *</label>
                        <input type="text" name="nombre" placeholder="Ej: Yeimy Johana" required class="w-full px-3 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-yjOrange outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Correo Corporativo</label>
                        <input type="email" name="email" placeholder="yeimy.johana@yajuego.co" class="w-full px-3 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-yjOrange outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Área Perteneciente *</label>
                        <select name="area" required class="w-full px-3 py-2 border border-gray-300 rounded-xl bg-white focus:ring-2 focus:ring-yjOrange outline-none">
                            <?php foreach ($areas as $a): ?>
                                <option value="<?= htmlspecialchars($a) ?>"><?= htmlspecialchars($a) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Cargo *</label>
                        <input type="text" name="cargo" placeholder="Ej: Líder de Control Interno" required class="w-full px-3 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-yjOrange outline-none">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">Rol / Nivel de Acceso en Intranet *</label>
                    <select name="rol" required class="w-full px-3 py-2 border border-gray-300 rounded-xl bg-white focus:ring-2 focus:ring-yjOrange outline-none font-bold text-yjBlue">
                        <?php foreach (array_keys($roles) as $r): ?>
                            <option value="<?= htmlspecialchars($r) ?>"><?= htmlspecialchars($r) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="p-3 bg-blue-50 border border-blue-100 rounded-xl text-[11px] text-yjBlue">
                    ℹ️ <strong>Nota:</strong> Al iniciar sesión, la Intranet validará el <strong class="font-mono">username</strong> y contraseña contra el Active Directory.
                </div>

                <div class="flex items-center justify-end gap-2 pt-4 border-t border-gray-100">
                    <button type="button" onclick="document.getElementById('modalNuevoUsuario').classList.add('hidden')" class="px-4 py-2 font-bold text-gray-500 hover:bg-gray-100 rounded-xl transition">Cancelar</button>
                    <button type="submit" class="px-5 py-2 bg-yjOrange hover:bg-orange-600 text-white font-bold rounded-xl transition shadow-xs">Vincular Usuario</button>
                </div>
            </form>
        </div>
    </div>

    <?php require_once __DIR__ . '/../../partials/footer.php'; ?>

</body>
</html>