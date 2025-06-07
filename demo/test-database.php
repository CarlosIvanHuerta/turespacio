<?php
// Mostrar errores (solo en desarrollo)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configuración de conexión
#$host     = 'turespacio.com:3306';
#$host     = '35.209.159.244:3306';
#$db_name  = 'dbonxlzrrzzd3g';
#$username = 'usmtcjiraflcy';
#$password = 'Su1t3Scr1pt@2025%';
#$charset  = 'utf8mb4';

$host     = '35.209.159.244:3306';
$db_name  = 'dbonxlzrrzzd3g';
$username = 'usmtcjiraflcy';
$password = 'Su1t3Scr1pt@2025%NOCORRECTO';
$charset  = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db_name;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
    echo "<h2>✅ Conexión exitosa a la base de datos.</h2>";
} catch (PDOException $e) {
    echo "<h2>❌ Error de conexión:</h2>";
    echo "<pre>" . $e->getMessage() . "</pre>";
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}