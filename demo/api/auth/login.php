<?php
require_once __DIR__ . '/../vendor/autoload.php'; // Autoload de Composer.
require_once __DIR__ . '/../config/propel-config.php'; // Configuración de Propel.
require_once __DIR__ . '/../middleware/auth.php'; // Verificar autenticación.

$data = json_decode(file_get_contents('php://input'));

// Verifica que los datos requeridos estén presentes.
if (empty($data->email) ) {
    http_response_code(400);
    echo json_encode(['error' => 'Email de acceso es obligatorio.']);
    exit;
}
if (empty($data->password)) {
    http_response_code(400);
    echo json_encode(['error' => 'La contraseña es obligatoria.']);
    exit;
}

// Busca al usuario en la base de datos.
$user = \WpUsersQuery::create()->filterByUserEmail($data->email)->findOne();

if (!$user) {
    http_response_code(404); // Código 404 para usuario no encontrado.
    echo json_encode(['error' => 'El email ingresado no existe, en nustros registros.']);
    exit;
}
// Verifica la contraseña con soporte para hashes de estilo WordPress.
if (password_verify($data->password, $user->getPassword())) {
    http_response_code(401); // Código 401 para credenciales no autorizadas.
    echo json_encode(['error' => 'La contraseña es incorrecta.']);
    exit;
}


// Genera un token de autenticación.
$token = Auth::generateToken($user->getId());

// Devuelve la respuesta.
echo json_encode([
    'message' => 'Login exitoso.',
    'token' => $token
]);