<?php

namespace App\Controllers;

use Config\Database;
use PDO;

class AuthController
{
    public function showLogin(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!empty($_SESSION['usuario'])) {
            header('Location: /yaconecta/public/');
            exit;
        }

        $loginError = $_GET['error'] ?? '';
        $loginMessages = [
            'usuario_no_encontrado' => 'El usuario no se encontró en la base de datos. Verifica el correo o usuario ingresado.',
            'credenciales_invalidas' => 'La contraseña ingresada no es válida. Inténtalo nuevamente.',
            'campos_vacios' => 'Ingresa tu usuario y contraseña para continuar.',
        ];
        $error = $loginMessages[$loginError] ?? null;

        $ds = DIRECTORY_SEPARATOR;
        $vista = dirname(__DIR__, 2) . $ds . 'views' . $ds . 'auth' . $ds . 'login.php';
        require_once $vista;
    }

    public function login(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $inputUsuario = trim($_POST['username'] ?? $_POST['email'] ?? $_POST['usuario'] ?? '');
            $password     = trim($_POST['password'] ?? '');

            if (empty($inputUsuario) || empty($password)) {
                header('Location: /yaconecta/public/login?error=campos_vacios');
                exit;
            }

            $db = Database::getConnection();

            // Las instalaciones de Ya.Net pueden tener `users` o `usuarios`,
            // con email/usuario_ad/username. Consultar solo columnas existentes.
            $user = null;
            $userTable = null;
            $passwordColumn = null;
            foreach (['users', 'usuarios'] as $table) {
                try {
                    $columns = $db->query("SHOW COLUMNS FROM `{$table}`")->fetchAll(PDO::FETCH_COLUMN);
                    if (!$columns) continue;
                    $columnSet = array_fill_keys($columns, true);
                    $loginColumns = array_values(array_filter(['username', 'usuario_ad', 'email'], static fn(string $column): bool => isset($columnSet[$column])));
                    if (!$loginColumns || !isset($columnSet['password'])) continue;

                    $conditions = [];
                    $params = [];
                    foreach ($loginColumns as $index => $column) {
                        $placeholder = ':login' . $index;
                        $conditions[] = "`{$column}` = {$placeholder}";
                        $params[$placeholder] = $inputUsuario;
                    }
                    $stmt = $db->prepare('SELECT * FROM `' . $table . '` WHERE ' . implode(' OR ', $conditions) . ' LIMIT 1');
                    $stmt->execute($params);
                    $candidate = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($candidate) {
                        $user = $candidate;
                        $userTable = $table;
                        $passwordColumn = 'password';
                        break;
                    }
                } catch (\PDOException $e) {
                    // Esa tabla no está instalada; probar el siguiente esquema compatible.
                    continue;
                }
            }

            if ($user) {
                $passBD = $user[$passwordColumn] ?? '';

                // Acepta hashes existentes y actualiza contraseñas heredadas en texto plano al primer acceso.
                $esValida = password_verify($password, $passBD);
                $storedHashInfo = $passBD !== '' ? password_get_info($passBD) : ['algo' => null];
                if (!$esValida && $passBD !== '' && $storedHashInfo['algo'] === null && hash_equals($passBD, $password)) {
                    $newHash = password_hash($password, PASSWORD_BCRYPT);
                    $updateStmt = $db->prepare("UPDATE `{$userTable}` SET `{$passwordColumn}` = :p WHERE id = :id");
                    $updateStmt->execute([':p' => $newHash, ':id' => $user['id']]);
                    $esValida = true;
                }

                if ($esValida) {
                    session_regenerate_id(true);
                    $_SESSION['usuario'] = [
                        'id'        => $user['id'],
                        'nombre'    => trim(($user['name'] ?? $user['nombre'] ?? $user['usuario_ad'] ?? 'Administrador') . (empty($user['apellido']) ? '' : ' ' . $user['apellido'])),
                        'username'  => $user['username'] ?? $user['usuario_ad'] ?? $user['email'] ?? $inputUsuario,
                        'email'     => $user['email'] ?? '',
                        'role_id'   => (int)($user['role_id'] ?? $user['rol_id'] ?? 1),
                        'cargo'     => 'Administrador'
                    ];

                    header('Location: /yaconecta/public/');
                    exit;
                }
                header('Location: /yaconecta/public/login?error=credenciales_invalidas');
                exit;
            }

            header('Location: /yaconecta/public/login?error=usuario_no_encontrado');
            exit;
        }
    }

    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $_SESSION = array();
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();

        header('Location: /yaconecta/public/login');
        exit;
    }
}
