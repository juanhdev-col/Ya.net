<?php
// Fallback para garantizar la carga de colaboradores/directorio desde el modelo de BD
if (!isset($colaboradores) || empty($colaboradores)) {
    try {
        if (class_exists('\App\Models\Directorio')) {
            if (method_exists('\App\Models\Directorio', 'obtenerTodos')) {
                $colaboradores = \App\Models\Directorio::obtenerTodos();
            } elseif (method_exists('\App\Models\Directorio', 'all')) {
                $colaboradores = \App\Models\Directorio::all();
            }
        } elseif (class_exists('\App\Models\Colaborador')) {
            if (method_exists('\App\Models\Colaborador', 'obtenerTodos')) {
                $colaboradores = \App\Models\Colaborador::obtenerTodos();
            } elseif (method_exists('\App\Models\Colaborador', 'all')) {
                $colaboradores = \App\Models\Colaborador::all();
            }
        }
    } catch (\Throwable $e) {
        $colaboradores = [];
    }
}

// Fallback de consulta directa a MySQL si aún no se ha obtenido
if (empty($colaboradores)) {
    try {
        $databasePath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'Database.php';
        if (!file_exists($databasePath)) {
            $databasePath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Services' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'Database.php';
        }
        if (file_exists($databasePath)) {
            require_once $databasePath;
            if (class_exists('\Config\Database')) {
                $db = \Config\Database::getConnection();
                
                // Intentar consultar la tabla de directorio/colaboradores
                try {
                    $stmt = $db->query("SELECT * FROM directorio ORDER BY id DESC LIMIT 5");
                    $colaboradores = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
                } catch (\Throwable $t) {
                    try {
                        $stmt = $db->query("SELECT * FROM colaboradores ORDER BY id DESC LIMIT 5");
                        $colaboradores = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
                    } catch (\Throwable $t2) {
                        $colaboradores = [];
                    }
                }
            }
        }
    } catch (\Throwable $e) {
        $colaboradores = [];
    }
}
?>

<div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-xs space-y-4">
    <div class="flex justify-between items-center">
        <h2 class="text-base font-black text-yjBlue flex items-center gap-2">
            <span class="w-2.5 h-6 bg-yjOrange rounded-full"></span> Directorio Corporativo
        </h2>
        <a href="/yaconecta/public/directorio" class="text-xs font-bold text-yjOrange hover:underline transition">Ver Todos →</a>
    </div>

    <?php if (!empty($colaboradores)): ?>
        <div class="divide-y divide-gray-100">
            <?php foreach (array_slice($colaboradores, 0, 4) as $persona): ?>
                <?php 
                    $nombre = $persona['nombre'] ?? $persona['name'] ?? 'Colaborador';
                    $cargo  = $persona['cargo'] ?? $persona['position'] ?? $persona['puesto'] ?? 'Equipo Yajuego';
                    $area   = $persona['area'] ?? $persona['department'] ?? $persona['departamento'] ?? '';
                    $email  = $persona['email'] ?? $persona['correo'] ?? '';
                    $foto   = $persona['foto'] ?? $persona['avatar'] ?? $persona['imagen'] ?? '';
                    $inicial = strtoupper(substr($nombre, 0, 1));
                ?>
                <div class="py-3 flex items-center justify-between first:pt-0 last:pb-0">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-yjBlue/10 text-yjBlue flex items-center justify-center font-bold text-sm overflow-hidden border border-gray-100 flex-shrink-0">
                            <?php if (!empty($foto)): ?>
                                <img src="<?= htmlspecialchars($foto) ?>" alt="<?= htmlspecialchars($nombre) ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <span><?= $inicial ?></span>
                            <?php endif; ?>
                        </div>
                        <div>
                            <h3 class="font-bold text-xs text-gray-800 leading-tight"><?= htmlspecialchars($nombre) ?></h3>
                            <p class="text-[11px] text-gray-500"><?= htmlspecialchars($cargo) ?> <?= !empty($area) ? '• ' . htmlspecialchars($area) : '' ?></p>
                        </div>
                    </div>
                    <?php if (!empty($email)): ?>
                        <a href="mailto:<?= htmlspecialchars($email) ?>" class="p-2 text-gray-400 hover:text-yjOrange hover:bg-orange-50 rounded-lg transition" title="Enviar correo">
                            ✉️
                        </a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-8 text-xs text-gray-400 font-medium">
            No hay colaboradores registrados.
        </div>
    <?php endif; ?>

    <form action="/yaconecta/public/directorio" method="GET" class="pt-2 flex gap-2">
        <input type="text" name="q" placeholder="Buscar por nombre, cargo o área..." class="w-full px-3 py-2 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-yjOrange">
        <button type="submit" class="px-4 py-2 bg-yjBlue text-white text-xs font-bold rounded-xl hover:bg-yjBlue/90 transition">Buscar</button>
    </form>
</div>