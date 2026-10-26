<?php

// 1. Forzar visualización de errores PHP en pantalla
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. Inicialización de sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 3. Autoloader de clases
spl_autoload_register(function ($class) {
    $baseDir = dirname(__DIR__) . DIRECTORY_SEPARATOR;

    if (strpos($class, 'App\\') === 0) {
        $file = $baseDir . 'app' . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, substr($class, 4)) . '.php';
    } elseif (strpos($class, 'Config\\') === 0) {
        $file = $baseDir . 'config' . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, substr($class, 7)) . '.php';
    } else {
        $file = $baseDir . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
    }

    if (file_exists($file)) {
        require_once $file;
    }
});

use App\Core\Router;
use App\Controllers\InicioController;
use App\Controllers\AuthController;
use App\Controllers\DirectorioController;
use App\Controllers\QuienesSomosController;
use App\Controllers\DocumentoController;
use App\Controllers\AppController;
use App\Controllers\AdminController;
use App\Controllers\AdminDocumentController;
use App\Controllers\AdminDirectoryController;
use App\Controllers\NoticiaController;
use App\Controllers\IdeasController;
use App\Controllers\AdminIdeasController;
use App\Controllers\ClasificadoController;
use App\Controllers\AdminUserController;
use App\Controllers\BannerController;
use App\Controllers\AgendaController;
use App\Controllers\BuscadorController;
use App\Controllers\PopupController;
use App\Controllers\HelpCenterController;
use App\Controllers\HelpCenterAdminController; 
use App\Controllers\AdminCompanyController;


$router = new Router();

// --- Rutas Públicas e Inicio ---
$router->get('/', [InicioController::class, 'index']);
$router->get('/home', [InicioController::class, 'index']);

// --- Rutas de Autenticación ---
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/logout', [AuthController::class, 'logout']);

// --- Rutas de Módulos Públicos ---
$router->get('/directorio', [DirectorioController::class, 'index']);
$router->get('/quienes-somos', [QuienesSomosController::class, 'index']);
$router->get('/documentos', [DocumentoController::class, 'index']);
$router->get('/documents', [DocumentoController::class, 'index']); // Alias
$router->get('/documentos/archivo', [DocumentoController::class, 'archivo']);

// --- Módulo de Aplicativos Corporativos (Apps) ---
$router->get('/apps', [AppController::class, 'index']);

// --- Módulo de Noticias ---
$router->get('/noticias', [NoticiaController::class, 'index']);
$router->post('/noticias/solicitar', [NoticiaController::class, 'solicitar']);

// --- Módulo de Banco de Ideas ---
$router->get('/ideas', [IdeasController::class, 'index']);
$router->post('/ideas/store', [IdeasController::class, 'store']);

// --- Rutas del Módulo Entre Nosotros (Clasificados) ---
$router->get('/clasificados', [ClasificadoController::class, 'index']);
$router->post('/clasificados/store', [ClasificadoController::class, 'store']);

// Ruta de Búsqueda Global
$router->get('/buscar', [BuscadorController::class, 'index']);

// API Pop-up Activo
$router->get('/api/popup-activo', [PopupController::class, 'getActivo']);

// --- CENTRO DE AYUDA (Colaborador) ---
$router->get('/centro-ayuda', [HelpCenterController::class, 'index']);
$router->post('/centro-ayuda/enviar', [HelpCenterController::class, 'enviarSolicitud']);


// =========================================================================
// --- PANEL DE ADMINISTRACIÓN (ADMIN) ---
// =========================================================================

// --- Dashboard Principal ---
$router->get('/admin', [AdminController::class, 'index']);
$router->get('/admin/dashboard', [AdminController::class, 'index']);
$router->get('/admin/dashboard.php', [AdminController::class, 'index']);

// --- Admin: Banners / Carrusel Home ---
$router->get('/admin/banners', [BannerController::class, 'admin']);
$router->post('/admin/banners/store', [BannerController::class, 'store']);
$router->get('/admin/banners/delete', [BannerController::class, 'delete']);

// --- Admin: Aplicaciones Corporativas ---
$router->get('/admin/apps', [AppController::class, 'admin']);
$router->post('/admin/apps/store', [AppController::class, 'store']);
$router->post('/admin/apps/update', [AppController::class, 'store']);
$router->get('/admin/apps/delete', [AppController::class, 'delete']);
$router->post('/admin/apps/eliminar', [AppController::class, 'delete']);

// --- Admin: Documentos ---
$router->get('/admin/documentos', [AdminDocumentController::class, 'index']);
$router->get('/admin/documents', [AdminDocumentController::class, 'index']);
$router->post('/admin/documentos/store', [AdminDocumentController::class, 'store']);
$router->post('/admin/documents/store', [AdminDocumentController::class, 'store']);
$router->post('/admin/documentos/guardar', [AdminDocumentController::class, 'store']);
$router->post('/admin/documentos/update', [AdminDocumentController::class, 'update']);
$router->post('/admin/documentos/archive', [AdminDocumentController::class, 'archive']);
$router->post('/admin/documentos/delete', [AdminDocumentController::class, 'archive']); // Compatibilidad con formularios anteriores
$router->post('/admin/documents/delete', [AdminDocumentController::class, 'archive']);

// --- Admin: Directorio ---
$router->get('/admin/directorio', [AdminDirectoryController::class, 'index']);
$router->post('/admin/directorio/store', [AdminDirectoryController::class, 'store']);
$router->post('/admin/directorio/update', [AdminDirectoryController::class, 'update']);
$router->get('/admin/directorio/delete', [AdminDirectoryController::class, 'delete']);

// --- Admin: Noticias ---
$router->get('/admin/noticias', [NoticiaController::class, 'admin']);
$router->post('/admin/noticias/store', [NoticiaController::class, 'store']);
$router->post('/admin/noticias/aprobar', [NoticiaController::class, 'approve']);
$router->post('/admin/noticias/rechazar', [NoticiaController::class, 'reject']);
$router->get('/admin/noticias/delete', [NoticiaController::class, 'delete']);

// --- Admin: Banco de Ideas ---
$router->get('/admin/ideas', [AdminIdeasController::class, 'index']);
$router->post('/admin/ideas/update-status', [AdminIdeasController::class, 'updateStatus']);
$router->get('/admin/ideas/delete', [AdminIdeasController::class, 'delete']);

// Rutas de Administración / Moderación
$router->get('/admin/clasificados', [ClasificadoController::class, 'admin']);
$router->get('/admin/clasificados/status', [ClasificadoController::class, 'updateStatus']);
$router->get('/admin/clasificados/delete', [ClasificadoController::class, 'delete']);

// --- Admin: Usuarios y Permisos ---
$router->get('/admin/usuarios', [AdminUserController::class, 'index']);
$router->post('/admin/usuarios/store', [AdminUserController::class, 'store']);
$router->post('/admin/usuarios/update', [AdminUserController::class, 'update']);
$router->get('/admin/usuarios/delete', [AdminUserController::class, 'delete']);


// --- Admin: Agenda / Calendario Corporativo ---
$router->get('/admin/agenda', [AgendaController::class, 'admin']);
$router->post('/admin/agenda/store', [AgendaController::class, 'store']);
$router->post('/admin/agenda/update', [AgendaController::class, 'update']);
$router->get('/admin/agenda/delete', [AgendaController::class, 'delete']);

// Administración de Pop-ups
$router->get('/admin/popups', [PopupController::class, 'indexAdmin']);
$router->post('/admin/popups/guardar', [PopupController::class, 'guardar']);
$router->get('/admin/popups/estado', [PopupController::class, 'cambiarEstado']);

// --- PANEL ADMINISTRATIVO CENTRO DE AYUDA ---
$router->get('/admin/centro-ayuda', [HelpCenterController::class, 'admin']);
$router->get('/admin/centro-ayuda/', [HelpCenterController::class, 'admin']); // Alias con barra final
$router->post('/admin/centro-ayuda/faq/save', [HelpCenterController::class, 'saveFaq']);
$router->get('/admin/centro-ayuda/faq/delete', [HelpCenterController::class, 'deleteFaq']);
$router->post('/admin/centro-ayuda/responder', [HelpCenterController::class, 'responderSolicitud']);

// --- PANEL ADMINISTRATIVO CENTRO DE AYUDA (Admin) ---
$router->get('/admin/centro-ayuda', [HelpCenterAdminController::class, 'index']);
$router->post('/admin/centro-ayuda/faq/save', [HelpCenterAdminController::class, 'saveFaq']);
$router->get('/admin/centro-ayuda/faq/delete', [HelpCenterAdminController::class, 'deleteFaq']);
$router->post('/admin/centro-ayuda/responder', [HelpCenterAdminController::class, 'responderSolicitud']);

// --- Rutas del Panel Admin - Quiénes Somos ---
$router->get('/admin/quienes-somos', [AdminCompanyController::class, 'index']);
$router->get('/admin/galeria', [AdminCompanyController::class, 'gallery']);
$router->post('/admin/quienes-somos/update-section', [AdminCompanyController::class, 'updateSection']);
$router->post('/admin/quienes-somos/add-media', [AdminCompanyController::class, 'addMedia']);
$router->get('/admin/quienes-somos/delete-media', [AdminCompanyController::class, 'deleteMedia']);
$router->post('/admin/quienes-somos/save-value', [AdminCompanyController::class, 'saveValue']);
$router->get('/admin/quienes-somos/delete-value', [AdminCompanyController::class, 'deleteValue']);


// --- Procesamiento y Dispatch de URI ---
$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Limpiar parámetros GET primero (?q=buscar...)
$uri = parse_url($uri, PHP_URL_PATH);

// Limpiar prefijos del proyecto
$uri = str_replace(['/yaconecta/public', '/yaconecta'], '', $uri);

// Remover barra final (excepto si la ruta es sólo '/')
if ($uri !== '/') {
    $uri = rtrim($uri, '/');
}

if (empty($uri)) {
    $uri = '/';
}

$router->dispatch($uri, $method);
