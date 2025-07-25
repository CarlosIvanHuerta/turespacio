<?php
require __DIR__ .'/../vendor/autoload.php';
require_once __DIR__ . '/../middleware/api-bootstrap.php'; // Encabezados CORS

use Google\Client;
use Google\Service\AnalyticsData;
use Google\Service\AnalyticsData\RunReportRequest;

// Inicializar cliente
$client = new Client();
$client->setAuthConfig(__DIR__ .'/../secure/analytics-key.json');
$client->addScope(AnalyticsData::ANALYTICS_READONLY);

// Instanciar servicio Analytics
$analytics = new AnalyticsData($client);

// Calcular fechas dinámicas
$startDate = date('Y-m-d', strtotime('-7 days'));
$endDate = date('Y-m-d');

$request = new RunReportRequest([
    'dimensions' => [
        ['name' => 'date']
    ],
    'metrics' => [
        ['name' => 'sessions']
    ],
    'dateRanges' => [[
        'startDate' => $startDate,
        'endDate' => $endDate
    ]]
]);

$response = $analytics->properties->runReport('properties/482504441', $request);

// Construir arreglo para frontend
$datos = ['fechas' => [], 'sesiones' => []];

foreach ($response->getRows() as $fila) {
    $fecha = $fila->getDimensionValues()[0]->getValue();
    $sesion = (int) $fila->getMetricValues()[0]->getValue();

    $datos['fechas'][] = $fecha;
    $datos['sesiones'][] = $sesion;
}

// Responder como JSON
header('Content-Type: application/json');
echo json_encode($datos);
