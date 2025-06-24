<?php
require_once __DIR__ . '/../vendor/autoload.php'; // Autoload de Composer
require_once __DIR__ . '/../config/propel-config.php'; // Configuración de Propel
require_once __DIR__ . '/../middleware/auth.php'; // Verificar autenticación
require_once __DIR__ . '/../middleware/api-bootstrap.php'; // Encabezados CORS

// Capturar datos enviados
$data = json_decode(file_get_contents('php://input'));

// Preparar respuesta
$response = []; // Arreglo para respuesta final
$statusCode = 200; // Código HTTP de la respuesta

// Validación de entrada
if (empty($data->email)) {
    $response = ['Mensaje' => 'Email de acceso es obligatorio.', "Success" => false];
} elseif (empty($data->password)) {
    $response = ['Mensaje' => 'La contraseña es obligatoria.', "Success" => false];
}
else {
    // Buscar usuario en la base de datos
    $user = \WpUsersQuery::create()->filterByUserEmail($data->email)->findOne();

    if (!$user) {
        $statusCode = 200;
        $response = ['Mensaje' => 'El email ingresado no existe en nuestros registros.', "Success" => false];
    }
    elseif (!password_verify($data->password, $user->getPassword())) {
        $statusCode = 200;
        $response = ['Mensaje' => 'La contraseña es incorrecta.', "Success" => false];
    }
    else {
        // Generar token de autenticación
        $token = Auth::generateToken($user->getId());
        $role = $dataUserMeta = \WpUsermetaQuery::create()
            ->select('meta_value')
            ->filterByUserId($user->getId())
            ->filterByMetaKey('wp_capabilities')
            ->findOne();
        // Mapa de roles con traducción
        $roleMap = [
            "administrator" => "administrador",
            "editor" => "editor",
            "author" => "autor",
            "contributor" => "colaborador",
            "subscriber" => "suscriptor"
        ];
        $d = unserialize($role);
        // Obtener la llave principal del array (el rol)
        $roleKey = array_keys($d)[0];

        // Mapear el rol a un valor traducido
        $translatedRole = $roleMap[$roleKey] ?? "desconocido";

        // Resultado final
        $response = [
            "role" => $translatedRole
        ];

        $statusCode = 200;
        $response = [
            'Success' => true,
            'Mensaje' => 'Login exitoso.',
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getUserEmail(),
                'name' => $user->getUserNicename(),
                'nickname' => $user->getDisplayName(),
            ],
            'role' => $translatedRole,
            'token' => $token
        ];
    }
}

// Enviar los encabezados y la respuesta
http_response_code($statusCode); // Configura el código de estado HTTP
header('Content-Type: application/json; charset=utf-8'); // Configura tipo de contenido
echo json_encode($response); // Envía la respuesta en JSON