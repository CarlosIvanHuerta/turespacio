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
header('Content-Type: application/json'); // Configuración del header de respuesta

$data = json_decode(file_get_contents('php://input'), true);

// Parámetros básicos para paginación y filtro
$searchTerm = $data['query'] ?? ''; // El término a buscar en las columnas filtrables
$page = isset($data['page']) ? (int)$data['page'] : 1;
$perPage = isset($data['limit']) ? (int)$data['limit'] : 10;


// Determinar las columnas ordenadas
$orderBy = $data['orderBy']['column'] ?? 'PostDate';
$sortDirection = $data['orderBy']['direction'] ?? 'desc'; // Dirección de orden: asc/desc

try {
    // Crear consulta con Propel
    $query = \WpPostsQuery::create()
        ->filterByPostType('post') // Solo posts
        ->filterByPostStatus('publish') // Solo publicados
        ->orderBy($orderBy, $sortDirection === 'asc' ? Criteria::ASC : Criteria::DESC);

    // Aplicar filtro de búsqueda (si existe)
    if (!empty($searchTerm)) {
        $query->condition('c1', 'WpPosts.PostTitle LIKE ?', "%{$searchTerm}%")
            ->condition('c2', 'WpPosts.PostContent LIKE ?', "%{$searchTerm}%")
            ->combine(['c1', 'c2'], Criteria::LOGICAL_OR);
    }

    // Clonar la consulta para contar filas filtradas
    $totalQuery = clone $query;
    $totalRecords = $totalQuery->count();

    // Aplicar paginación
    $query->limit($perPage)
        ->offset(($page - 1) * $perPage);

    // Ejecutar la consulta principal
    $results = $query->select(['ID', 'PostTitle', 'PostDate', 'PostContent', 'LoadCarousel', 'PostStatus'])
        ->find();

    // Formatear resultados: limitar el contenido a las primeras 300 palabras
    $data = $results->toArray();
    $posts = array_map(function ($post) {
        return [
            'id' => $post['ID'],
            'Tile' => $post['PostTitle'],
            'PostDate' => $post['PostDate'],
            'Carousel' => $post['LoadCarousel'],
            'PostStatus' => $post['PostStatus'],
            'Content' => implode(' ', array_slice(explode(' ', strip_tags($post['PostContent'])), 0, 50)),
        ];
    }, $data);

    // Respuesta con los datos y metadatos de paginación
    echo json_encode([
        'data' => $posts, // Filas de posts paginadas
        'count' => $totalRecords, // Total de registros filtrados
        'pages' => ceil($totalRecords / $perPage), // Total de páginas
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error en la consulta.', 'error' => $e->getMessage()]);
}
