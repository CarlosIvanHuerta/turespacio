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

$idPost = $data['id'] ?? '';
$estatusChange = $data['number'] ?? '';
error_log($estatusChange);
error_log($idPost);
$dataPost = \WpPostsQuery::create()->findOneById($idPost);
error_log($dataPost);
$dataPost->setLoadCarousel($estatusChange === 1)->setPostModified(date('Y-m-d H:i:s'))->save();
error_log($dataPost);
echo json_encode([
    'success' => true,
    'message' => 'Cambio de estatus exitoso.'
]);