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
$title = $_POST['title'] ?? null;
$author = $_POST['ut'] ?? null;
$content = $_POST['content'] ?? null;
$category = $_POST['category'] ?? null;
$estatus = $_POST['estatus'] ?? null;
$labels = isset($_POST['labels']) ? json_decode($_POST['labels'], true) : null; // Aquí decodificamos etiquetas
$type = $_POST['type'] ?? null;

// Validar la imagen destacada enviada
$image = $_FILES['file'] ?? null;


if ($type === 'create') {

    // Validar datos requeridos
    if (!$title || !$content || !$category || !$image) {
        http_response_code(400);
        echo json_encode(['error' => 'Faltan datos requeridos.']);
        exit();
    }

    // Validar y procesar la imagen destacada
    $uploadDir = __DIR__ . '/../uploads/images/posts/';
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
            // Verificar si el estatus proporcionado tiene una traducción
            if (isset($translations[$estatus])) {
                $translatedStatus = $translations[$estatus]; // El valor traducido
            } else {
                $translatedStatus = 'trash'; // Valor por defecto si no existe la clave
            }

            // Crear el post
        $post = new \WpPosts();
        $post->setPostAuthor($author);
        $post->setPostTitle($title);
        $post->setPostName($helpers->generateUrlSlug($title));
        $post->setPostContent($content);
        $post->setPostStatus($translatedStatus);
        $post->setPostType('post');
        $post->setPostExcerpt('');
        $post->setToPing('');
        $post->setPinged('');
        $post->setPostContentFiltered('');
        $post->setPostDate(date('Y-m-d H:i:s')); // Fecha actual
        $post->setGuid(''); // URL de la imagen destacada
        $post->save();
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

        // Relacionar post con categoría
        $termRelationship = new \WpTermRelationships();
        $termRelationship->setObjectId($post->getId()); // ID del nuevo post
        $termRelationship->setTermTaxonomyId($category); // ID de la categoría
        $termRelationship->save();

        // Relacionar el post con etiquetas
        if (!empty($labels)) {
            foreach ($labels as $labelId) {
                $tagRelationship = new \WpTermRelationships();
                $tagRelationship->setObjectId($post->getId()); // ID del nuevo post
                $tagRelationship->setTermTaxonomyId($labelId['value']); // ID de la etiqueta
                $tagRelationship->save();
            }
        }
        try
        {
            $postAttach = new \WpPosts();
            $postAttach->setPostAuthor($author);
            $postAttach->setPostDate(date('Y-m-d H:i:s')); // Fecha actual
            $postAttach->setPostContent('');
            $postAttach->setPostTitle($title);
            $postAttach->setPostExcerpt('');
            $postAttach->setPostStatus('inherit');
            $postAttach->setCommentStatus('closed');
            $postAttach->setPingStatus('closed');
            $postAttach->setPostName($helpers->generateUrlSlug($title));
            $postAttach->setToPing('');
            $postAttach->setPinged('');
            $postAttach->setPostParent($post->getId());
            $postAttach->setPostType('attachment');
            $postAttach->setPostMimeType('image/jpeg');
            $postAttach->setPostContentFiltered('');
            $postAttach->setGuid('http://localhost/api/uploads/images/posts/' . $imageName); // URL de la imagen destacada
            $postAttach->save();
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
        $thumbnailImg = new \WpPostmeta();
        $thumbnailImg->setPostId($post->getId())
        ->setMetaKey('_thumbnail_id')
        ->setMetaValue($postAttach->getId())
        ->save();

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
}
