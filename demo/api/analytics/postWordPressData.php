<?php
require_once __DIR__ . '/../vendor/autoload.php'; // Autoload de Composer
require_once __DIR__ . '/../config/propel-config.php'; // Configuración de Propel
require_once __DIR__ . '/../middleware/auth.php'; // Verificar autenticación
require_once __DIR__ . '/../middleware/api-bootstrap.php'; // Encabezados CORS
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Propel;


// Capturar el header Authorization donde se encuentran los Bearer Tokens
function getBearerToken() {
    $headers = null;

    // Obtener todos los headers dependiendo del servidor
    if (isset($_SERVER['Authorization'])) {
        $headers = trim($_SERVER["Authorization"]);
    } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) { // Para servidores con "Authorization" en el header HTTP
        $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
    } elseif (function_exists('apache_request_headers')) {
        $requestHeaders = apache_request_headers();
        // Normaliza el formato del header
        $requestHeaders = array_change_key_case($requestHeaders, CASE_LOWER);
        if (isset($requestHeaders['authorization'])) {
            $headers = trim($requestHeaders['authorization']);
        }
    }

    // Si el header contiene "Bearer", retornar solo el token
    if (!empty($headers) && preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
        return $matches[1];
    }

    return null; // No se encontró el Bearer Token
}

// Obtener y validar el token del header
$token = getBearerToken();

if (!$token) {
    http_response_code(401); // Código de error no autorizado
    echo json_encode([
        'error' => 'Token no proporcionado.'
    ]);
    exit();
}

// Usar el método verifyToken para validar el token
$verifiedToken = Auth::verifyToken($token);

if (!$verifiedToken) {
    http_response_code(403); // Código de error prohibido
    echo json_encode([
        'error' => 'Token inválido o expirado.'
    ]);
    exit();
}
try {
    // Usamos Propel ORM para realizar la consulta
    $posts = \WpPostsQuery::create()
        ->filterByPostType('post') // Solo posts del tipo "post"
        ->orderByPostDate('desc') // Ordenados por fecha de creación
        ->limit(6) // Limitar a los últimos 6 posts
        // Realizar el JOIN manualmente
        ->addJoin('wp_posts.post_author', 'wp_users.ID', Criteria::INNER_JOIN) // Definir relación manual del JOIN
        ->addAsColumn('autor', 'wp_users.user_login') // Seleccionar el autor
        ->addAsColumn('id', 'wp_posts.ID') // ID del post
        ->addAsColumn('titulo', 'wp_posts.post_title') // Título del post
        ->addAsColumn('fecha', 'wp_posts.post_date') // Fecha de creación
        ->addAsColumn('contenido', 'wp_posts.post_content') // Contenido del post
        ->select(['id', 'titulo', 'autor', 'fecha', 'contenido']) // Columnas específicas para la salida
        ->find()->toArray();


    // Procesar el contenido para limitarlo a 300 palabras
    $resultado = array_map(function ($post) {
        return [
            'id' => $post['id'],
            'titulo' => $post['titulo'],
            'autor' => $post['autor'], // Nombre del autor
            'fecha_hora' => $post['fecha'], // Fecha y hora
            'contenido' => implode(' ', array_slice(explode(' ', strip_tags($post['contenido'])), 0, 60)),
        ];
    }, $posts);

    // Respuesta JSON
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'posts' => $resultado]);
} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
