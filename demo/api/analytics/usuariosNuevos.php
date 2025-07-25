<?php
require __DIR__ .'/../vendor/autoload.php';
require_once __DIR__ . '/../middleware/api-bootstrap.php'; // CORS

use Google\Client;
use Google\Service\AnalyticsData;
use Google\Service\AnalyticsData\RunReportRequest;
// Leer el cuerpo JSON de la solicitud POST
$body = file_get_contents('php://input');
$data = json_decode($body, true);
// Recibir fechas del frontend
// Usar los datos
$startDate = isset($data['inicio']) ? $data['inicio'] : date('Y-m-d', strtotime('-7 days'));
$endDate   = isset($data['fin']) ? $data['fin'] : date('Y-m-d');

// Configurar cliente
$client = new Client();
$client->setAuthConfig(__DIR__ .'/../secure/analytics-key.json');
$client->addScope(AnalyticsData::ANALYTICS_READONLY);

// Servicio Analytics
$analytics = new AnalyticsData($client);

// Crear solicitud
$request = new RunReportRequest([
    'metrics' => [
        ['name' => 'activeUsers'],
        ['name' => 'newUsers'],
        ['name' => 'userEngagementDuration']
    ],
    'dimensions' => [
        ['name' => 'date']
    ],
    'dateRanges' => [[
        'startDate' => $startDate,
        'endDate' => $endDate
    ]]
]);

$response = $analytics->properties->runReport('properties/482504441', $request);

// Armar respuesta
$datos = ['fechas' => [],
    'activeUsers' => [],
    'newUsers' => [],
    'engagement' => []];

foreach ($response->getRows() as $fila) {
    $fecha = $fila->getDimensionValues()[0]->getValue();
    $active = (int) $fila->getMetricValues()[0]->getValue();
    $new = (int) $fila->getMetricValues()[1]->getValue();
    $engaged = (float) $fila->getMetricValues()[2]->getValue(); // segundos
    $datos['fechas'][] = $fecha;
    $datos['activeUsers'][] = $active;
    $datos['newUsers'][] = $new;
    $datos['engagement'][] = round($engaged / 60, 2); // convertir a minutos si gustas
}

// JSON para el frontend
header('Content-Type: application/json');
echo json_encode($datos);
