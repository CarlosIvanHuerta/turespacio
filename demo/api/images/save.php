<?php
require_once __DIR__ . '/../vendor/autoload.php'; // Autoload de Composer
require_once __DIR__ . '/../config/propel-config.php'; // Configuración de Propel
require_once __DIR__ . '/../middleware/auth.php'; // Verificar autenticación
require_once __DIR__ . '/../middleware/api-bootstrap.php'; // Encabezados CORS

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
$data = json_decode(file_get_contents('php://input'));
$uploadDir = __DIR__ . '/../uploads/images/';

// Verificar si el directorio existe, si no, crearlo
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true); // Crear directorio con permisos adecuados
}

// Información del archivo subido
$image = $_FILES['file']; // 'image' es el nombre del input
$imageName = basename($image['name']); // Nombre del archivo
$imagePath = $uploadDir . $imageName; // Ruta completa al archivo

// Validaciones simples (tipo de archivo e integridad)
$validTypes = ['image/jpeg', 'image/png', 'image/gif'];
if (!in_array($image['type'], $validTypes)) {
    http_response_code(400); // Código de error por tipo no válido
    echo json_encode(['error' => 'Tipo de archivo no permitido (solo JPG, PNG o GIF).']);
    exit();
}

if ($image['size'] > 5 * 1024 * 1024) {
    http_response_code(400); // Código de error por tamaño de archivo
    echo json_encode(['error' => 'El archivo excede el tamaño máximo permitido (5MB).']);
    exit();
}

// Intentar mover el archivo a la carpeta destino
if (move_uploaded_file($image['tmp_name'], $imagePath)) {
    // Generar la URL accesible (asumiendo que la carpeta está dentro de tu servidor web)
    $baseURL = 'http://localhost/api/uploads/images/'; // Cambia "tu-dominio.com" según tu configuración
    $imageURL = $baseURL . $imageName;

    // Responder con la URL de la imagen
    http_response_code(200); // Código de éxito
    echo json_encode(['url' => $imageURL]);
} else {
    http_response_code(500); // Código de error de servidor
    echo json_encode(['error' => 'Hubo un problema subiendo la imagen.']);
}

