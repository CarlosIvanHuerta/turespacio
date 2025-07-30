<?php
ini_set('display_errors', 0); // No mostrar errores PHP al usuario
error_reporting(E_ALL); // Registrar todos los errores
date_default_timezone_set('America/Mexico_City');
header("Content-Type: application/json");
require_once "access-db.php";

$db = new Database();

$result = $db->getPublicidad();
echo json_encode($result);