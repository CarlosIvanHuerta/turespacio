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

if ($data->select == 'categoriasPost'){
    $dataR = \WpTermsQuery::create()
        ->select(['value', 'label'])
        ->addJoin(
            'wp_terms.term_id', // Columna de la tabla principal
            'wp_term_taxonomy.term_id', // Columna de la tabla secundaria
            \Propel\Runtime\ActiveQuery\Criteria::INNER_JOIN // Tipo de unión
        )
        ->where("wp_term_taxonomy.taxonomy = 'category'") // Filtrar por taxonomía
        ->withColumn('wp_term_taxonomy.term_taxonomy_id', 'value') // Asignar a "value"
        ->withColumn('wp_terms.name', 'label') // Asignar a "label"
        ->find()
        ->toArray();
    // Enviar los encabezados y la respuesta
    http_response_code(200); // Configura el código de estado HTTP
    header('Content-Type: application/json; charset=utf-8'); // Configura tipo de contenido
    echo json_encode(['data' => $dataR]); // Envía la respuesta en JSON
}
if ($data->select == 'postStatusAndVisibility') {
    $dataR = \WpPostsQuery::create()
        ->select(['post_status'])
        ->distinct() // Solo valores únicos
        ->find()
        ->toArray();
    // Diccionario de traducciones
    $translations = [
        'publish' => 'Publicado',
        'draft' => 'Borrador',
        'pending' => 'Pendiente de revisión',
        'private' => 'Privado',
        'trash' => 'Papelera',
        'inherit' => 'Herencia',
        'auto-draft' => 'Borrador automático',
    ];

    // Traducir los resultados antes de enviarlos
    $translatedData = array_map(function ($item) use ($translations) {
        return [
            'label' => $translations[$item] ?? $item,
            'value' => $translations[$item] ?? $item,
        ];
    }, $dataR);

    http_response_code(200);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['data' => $translatedData]); // Envía la respuesta en JSON
}
if ($data->select == 'tagsPost') {
    $dataR = \WpTermsQuery::create()
        ->select(['value', 'label'])
        ->addJoin(
            'wp_terms.term_id', // Columna de la tabla principal
            'wp_term_taxonomy.term_id', // Columna de la tabla secundaria
            \Propel\Runtime\ActiveQuery\Criteria::INNER_JOIN // Tipo de unión
        )
        ->where("wp_term_taxonomy.taxonomy = 'post_tag'") // Filtrar por taxonomía
        ->withColumn('wp_terms.term_id', 'value') // Asignar el ID como value
        ->withColumn('wp_terms.name', 'label') // Asignar el nombre como label
        ->find()
        ->toArray();

    // Retornar las etiquetas como respuesta JSON
    http_response_code(200);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['data' => $dataR]);
}
