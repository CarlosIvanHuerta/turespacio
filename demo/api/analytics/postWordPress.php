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

// Asegúrate de haber configurado correctamente Propel y de incluir tus modelos correctamente.
$body = file_get_contents('php://input');
$data = json_decode($body, true);
try {
    // Obtener las fechas recibidas (inicio y fin) desde el frontend
    $startDate = $data['inicio'].' 00:00:00';
    $endDate = $data['fin'].' 23:59:59';;

    // Consulta con Propel
    $postsCounts = \WpPostsQuery::create()
        // Filtro por fecha inicial y final
        ->filterByPostDate(array('min' => $startDate, 'max' => $endDate))
        ->filterByPostType('post') // Para asegurarte de que solo se incluyan posts
        ->withColumn('COUNT(*)', 'total')
        ->withColumn('SUM(CASE WHEN post_status = "publish" THEN 1 ELSE 0 END)', 'publish_count') // Publicados
        ->withColumn('SUM(CASE WHEN post_status = "pending" THEN 1 ELSE 0 END)', 'pending_count') // Pendientes
        ->withColumn('SUM(CASE WHEN post_status = "future" THEN 1 ELSE 0 END)', 'finished_count') // Programados
        ->select(array('total', 'publish_count', 'pending_count', 'finished_count'))
        ->findOne();

    // Respuesta JSON
    $estadisticas = [
        'total' => (int) $postsCounts['total'],
        'publicados' => (int) $postsCounts['publish_count'],
        'pendientes' => (int) $postsCounts['pending_count'],
        'programados' => (int) $postsCounts['finished_count'],
    ];

    // Mostrar los resultados
    header('Content-Type: application/json');
    echo json_encode($estadisticas);
} catch (\Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
