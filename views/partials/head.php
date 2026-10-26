<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Ya.Net | Intranet Corporativa' ?></title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Estilos Globales Únicos -->
    <link rel="stylesheet" href="/yaconecta/public/assets/css/styles.css">
    <?php if (($pageTitle ?? '') === 'Ya.Net | Inicio'): ?>
        <link rel="stylesheet" href="/yaconecta/public/assets/css/home.css">
    <?php endif; ?>
    <?php if (($pageTitle ?? '') === 'Iniciar Sesión | Ya.Net'): ?>
        <link rel="stylesheet" href="/yaconecta/public/assets/css/login.css?v=2">
    <?php endif; ?>
    <?php if (($pageTitle ?? '') === 'Ya.Net | Quiénes Somos'): ?>
        <link rel="stylesheet" href="/yaconecta/public/assets/css/quienes-somos.css">
    <?php endif; ?>
    <?php if (($pageTitle ?? '') === 'Ya.Net | Directorio Corporativo'): ?>
        <link rel="stylesheet" href="/yaconecta/public/assets/css/directorio.css">
    <?php endif; ?>
    <?php if (($pageTitle ?? '') === 'Ya.Net | Administración Directorio Corporativo'): ?>
        <link rel="stylesheet" href="/yaconecta/public/assets/css/directorio-admin.css">
    <?php endif; ?>
    <?php if (($pageTitle ?? '') === 'Ya.Net | Administración Aplicaciones Corporativas'): ?>
        <link rel="stylesheet" href="/yaconecta/public/assets/css/apps-admin.css">
    <?php endif; ?>
    <?php if (($pageTitle ?? '') === 'Ya.Net | Administración de Eventos'): ?>
        <link rel="stylesheet" href="/yaconecta/public/assets/css/events-admin.css?v=2">
    <?php endif; ?>
    <?php if (($pageTitle ?? '') === 'Ya.Net | Administración de Galería Corporativa'): ?>
        <link rel="stylesheet" href="/yaconecta/public/assets/css/gallery-admin.css?v=1">
    <?php endif; ?>
    <?php if (($pageTitle ?? '') === 'Ya.Net | Administración de Noticias y Comunicaciones'): ?>
        <link rel="stylesheet" href="/yaconecta/public/assets/css/news-admin.css?v=1">
    <?php endif; ?>
    <?php if (($pageTitle ?? '') === 'Ya.Net | Administración de Clasificados Internos'): ?>
        <link rel="stylesheet" href="/yaconecta/public/assets/css/classifieds-admin.css?v=1">
    <?php endif; ?>
    <?php if (($pageTitle ?? '') === 'Ya.Net | Entre Nosotros (Clasificados)'): ?>
        <link rel="stylesheet" href="/yaconecta/public/assets/css/classifieds-public.css?v=1">
    <?php endif; ?>
    <?php if (($pageTitle ?? '') === 'Ya.Net | Aplicaciones Corporativas'): ?>
        <link rel="stylesheet" href="/yaconecta/public/assets/css/apps-public.css?v=1">
    <?php endif; ?>
    <?php if (str_contains(($pageTitle ?? ''), 'Gestión Documental') || str_contains(($pageTitle ?? ''), 'Administración Documental')): ?>
        <link rel="stylesheet" href="/yaconecta/public/assets/css/documentos.css">
    <?php endif; ?>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { yjBlue: '#10388B', yjOrange: '#ED8533', yjBlueDark: '#0A255C' },
                    fontFamily: { sans: ['Montserrat', 'sans-serif'] },
                    screens: { '3xl': '1920px' }
                }
            }
        }
    </script>
</head>
