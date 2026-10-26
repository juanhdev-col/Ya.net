<!DOCTYPE html>
<html lang="es">

<!-- HEAD GLOBAL -->
<?php 
    $pageTitle = "Ya.Net | Inicio";
    require_once __DIR__ . '/partials/head.php'; 
?>

<body class="home-page bg-gray-100 font-sans flex flex-col min-h-screen text-gray-800 antialiased">

    <!-- NAVEGACIÓN Y CONTENIDO -->
    <?php require_once __DIR__ . '/partials/navbar.php'; ?>
    <?php require_once __DIR__ . '/partials/carousel.php'; ?>

    <main class="home-content w-full">
        <div class="home-feature-grid">
            <?php require_once __DIR__ . '/partials/news_slider.php'; ?>
            <?php require_once __DIR__ . '/partials/calendar_widget.php'; ?>
        </div>

        <div class="home-apps">
            <?php require_once __DIR__ . '/partials/apps_grid.php'; ?>
        </div>
    </main>

    <?php require_once __DIR__ . '/partials/footer.php'; ?>

</body>
</html>
