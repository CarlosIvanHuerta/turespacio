<?php
ini_set('display_errors', 0);
error_reporting(E_ALL);
date_default_timezone_set('America/Mexico_City');
header("Content-Type: application/json");
require_once "access-db.php";

$db = new Database();

// Leer el cuerpo del POST
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true);

// Validar si viene la variable 'click' y si es true
if (isset($input['click']) && $input['click'] === true && isset($input['id'])) {
    // Ejecutar lógica de actualización
    $idPublicidad = intval($input['id']);

    $actualizado = $db->registrarClickPublicidad($idPublicidad); // Esta función deberías tenerla implementada en tu clase Database

    echo json_encode([
        'status' => $actualizado ? 'ok' : 'error',
        'mensaje' => $actualizado ? 'Clic registrado exitosamente.' : 'Error al registrar clic.'
    ]);
} else {
    // Retornar la publicidad normalmente
    $result = $db->getPublicidad();
    echo json_encode($result);
}
