<!DOCTYPE html>
<html lang="es">

<?php 
    $pageTitle = "Iniciar Sesión | Ya.Net";
    require_once __DIR__ . '/../partials/head.php'; 
?>

<body class="login-page">
    <main class="login-layout">
        <section class="login-visual" aria-label="Bienvenida a Ya.Net">
            <div class="login-brandline">
                <span>YA.NET</span>
                <span>INTRANET CORPORATIVA</span>
            </div>

            <div class="login-welcome">
                <p class="login-eyebrow">Bienvenido a</p>
                <h1>Ya.<span>Net</span></h1>
                <p class="login-intro">Un espacio para mantenernos informados, conectados y seguir construyendo juntos grandes resultados.</p>

                <ul class="login-benefits">
                    <li>
                        <span class="login-benefit-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm13 10v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg></span>
                        <span><strong>Comunicación</strong><small>Noticias y novedades</small></span>
                    </li>
                    <li>
                        <span class="login-benefit-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8zM14 2v6h6M8 13h8M8 17h8"/></svg></span>
                        <span><strong>Gestión documental</strong><small>Procesos y políticas</small></span>
                    </li>
                    <li>
                        <span class="login-benefit-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg></span>
                        <span><strong>Acceso a aplicativos</strong><small>Todas tus herramientas</small></span>
                    </li>
                </ul>
            </div>

            <p class="login-tagline"><span></span>ACTIVA TU SUERTE</p>
        </section>

        <section class="login-form-side" aria-label="Formulario de inicio de sesión">
            <div class="login-card">
                <div class="login-card-brand">
                    <img src="/yaconecta/public/assets/img/yajuego-logo.png" alt="Yajuego" class="login-card-logo">
                </div>
                <h2>Inicia sesión</h2>
                <p class="login-form-subtitle">Ingresa con tu cuenta corporativa</p>

                <form action="/yaconecta/public/login" method="POST" class="login-form">
                    <label class="login-input-wrap" for="username">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
                        <span class="sr-only">Correo institucional o usuario</span>
                        <input type="text" id="username" name="username" required placeholder="Correo institucional o usuario" autocomplete="username">
                    </label>

                    <label class="login-input-wrap login-password-wrap" for="password">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 1 1 8 0v3M12 14v3"/></svg>
                        <span class="sr-only">Contraseña</span>
                        <input type="password" id="password" name="password" required placeholder="Contraseña" autocomplete="current-password">
                        <button class="login-password-toggle" type="button" aria-label="Mostrar contraseña" aria-pressed="false">
                            <svg class="password-eye" viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </label>

                    <button type="submit" class="login-submit">Iniciar sesión <span aria-hidden="true">→</span></button>
                </form>

                <div class="login-help">
                    <p>¿Problemas para ingresar?</p>
                    <a href="/yaconecta/public/centro-ayuda">Contacta al área de TI</a>
                </div>
                <a href="/yaconecta/public/" class="login-home-link">Volver al inicio</a>
            </div>
        </section>
    </main>

    <?php if (!empty($error)): ?>
        <div class="login-error-overlay" id="loginErrorModal" role="alertdialog" aria-modal="true" aria-labelledby="loginErrorTitle" aria-describedby="loginErrorMessage">
            <div class="login-error-dialog">
                <button class="login-error-close" type="button" aria-label="Cerrar" onclick="closeLoginError()">×</button>
                <span class="login-error-symbol" aria-hidden="true">!</span>
                <h2 id="loginErrorTitle">No se pudo iniciar sesión</h2>
                <p id="loginErrorMessage"><?= htmlspecialchars($error) ?></p>
                <button class="login-error-confirm" type="button" onclick="closeLoginError()">Entendido</button>
            </div>
        </div>
    <?php endif; ?>

    <script>
        function closeLoginError() {
            const modal = document.getElementById('loginErrorModal');
            if (modal) modal.remove();
            document.getElementById('username')?.focus();
        }
        document.getElementById('loginErrorModal')?.addEventListener('click', function (event) {
            if (event.target === this) closeLoginError();
        });
        document.querySelector('.login-password-toggle')?.addEventListener('click', function () {
            const password = document.getElementById('password');
            const showing = password.type === 'text';
            password.type = showing ? 'password' : 'text';
            this.setAttribute('aria-pressed', String(!showing));
            this.setAttribute('aria-label', showing ? 'Mostrar contraseña' : 'Ocultar contraseña');
        });
    </script>

</body>
</html>
