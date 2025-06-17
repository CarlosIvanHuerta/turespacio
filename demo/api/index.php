<?php
require_once __DIR__ . '/vendor/autoload.php'; // Autoload de Composer.
require_once __DIR__ . '/config/propel-config.php'; // Configuración de Propel.

header('Content-Type: application/json'); // Devolveremos siempre JSON.

$requestMethod = $_SERVER['REQUEST_METHOD']; // Método HTTP.
$requestUri = explode('?', $_SERVER['REQUEST_URI'])[0]; // Ruta sin parámetros.
error_log($requestUri);
switch ($requestUri) {
    case '/api/login':
    case '/api/auth/login': // Agregamos la ruta correcta.
        require_once __DIR__ . '/auth/login.php'; // Ruta a login.php
        break;

    case '/api/posts': // CRUD de posts.
        require_once __DIR__ . '/../routes/posts.php';
        break;
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Ruta no encontrada']);
        break;
}