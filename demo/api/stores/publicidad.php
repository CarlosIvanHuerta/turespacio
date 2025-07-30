<?php
require_once __DIR__ . '/../vendor/autoload.php'; // Autoload de Composer
require_once __DIR__ . '/../config/propel-config.php'; // Configuración de Propel
require_once __DIR__ . '/../middleware/auth.php'; // Verificar autenticación
require_once __DIR__ . '/../middleware/api-bootstrap.php'; // Encabezados CORS
// Importa la clase ayudasBasicas
use utils\ayudasBasicas;


// Capturar el header Authorization donde se encuentran los Bearer Tokens
function getBearerToken()
{
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
$helpers = new ayudasBasicas();

// Datos enviados en campos del FormData
$url = $_POST['url'] ?? null;
$cliente = $_POST['cliente'] ?? null;
$texto = $_POST['texto'] ?? null;
// Validar la imagen destacada enviada
$image = $_FILES['file'] ?? null;

// Validar datos requeridos
if (!$url || !$cliente || !$texto || !$image) {
    http_response_code(400);
    echo json_encode(['error' => 'Faltan datos requeridos.']);
    exit();
}

// Validar y procesar la imagen destacada
$uploadDir = __DIR__ . '/../uploads/images/banners/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$imageName = basename($image['name']);
$imagePath = $uploadDir . $imageName;
error_log($imagePath);

if (!move_uploaded_file($image['tmp_name'], $imagePath)) {
    http_response_code(500);
    echo json_encode(['error' => 'No se pudo guardar la imagen destacada.']);
    exit();
}

// Guardar los datos en la base de datos con Propel
try {
    try {


        // Crear el post
        $publi = new \Publicidad();
        $publi->setUrlExterna($url);
        $publi->setPathImg('http://localhost/api/uploads/images/banners/'.$imageName);
        $publi->setTextImportant($texto);
        $publi->setClicksPublicidad(0);
        $publi->setEstatus(1);
        $publi->setCliente($cliente);
        $publi->save();
    }
    catch (\Propel\Runtime\Exception\PropelException $e) {
        // Para capturar errores específicos de Propel
        error_log("PropelException: " . $e);
        http_response_code(500);
        error_log("PropelException: " . $e->getMessage());
        echo json_encode([
            'error' => 'Error al guardar el post: ' . $e->getMessage()
        ]);
        exit();
    }


    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'El post se guardó exitosamente.',
        'post_id' => $post->getId()
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Error al guardar el post: ' . $e->getMessage()
    ]);
}
