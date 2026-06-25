<?php
/**
 * SCAP - Front Controller
 * Punto de entrada único de la aplicación.
 */

// ── Constantes de ruta ────────────────────────────────────
define('ROOT_PATH',   __DIR__);
define('APP_PATH',    ROOT_PATH . '/app');
define('VIEWS_PATH',  APP_PATH  . '/Views');
define('CORE_PATH',   ROOT_PATH . '/core');

// ── Carga de configuración ────────────────────────────────
require_once ROOT_PATH . '/config/app.php';
require_once ROOT_PATH . '/config/database.php';

// ── Configuración de sesión segura ────────────────────────
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_samesite', 'Lax');
session_set_cookie_params(['lifetime' => SESSION_LIFETIME, 'path' => BASE_URL . '/']);
session_start();

// ── Autoloader simple ─────────────────────────────────────
spl_autoload_register(function (string $class): void {
    $locations = [
        CORE_PATH  . '/' . $class . '.php',
        APP_PATH   . '/Models/'      . $class . '.php',
        APP_PATH   . '/Controllers/' . $class . '.php',
    ];
    foreach ($locations as $file) {
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// ── Despacho de rutas ─────────────────────────────────────
require_once CORE_PATH . '/Router.php';
(new Router())->dispatch();
