<?php
require __DIR__ .'/../vendor/autoload.php';
require_once __DIR__ . '/../middleware/api-bootstrap.php'; // CORS

use Google\Client;
use Google\Service\AnalyticsData;
use Google\Service\AnalyticsData\RunReportRequest;

// Obtener fechas del frontend
$startDate = isset($_GET['inicio']) ? $_GET['inicio'] : date('Y-m-d', strtotime('-7 days'));
$endDate = isset($_GET['fin']) ? $_GET['fin'] : date('Y-m-d');

// Inicializar cliente
$client = new Client();
$client->setAuthConfig(__DIR__ .'/../secure/analytics-key.json');
$client->addScope(AnalyticsData::ANALYTICS_READONLY);

// Instanciar servicio
$analytics = new AnalyticsData($client);

// Preparar Request
$request = new RunReportRequest([
    'dimensions' => [
        ['name' => 'date']
    ],
    'metrics' => [
        ['name' => 'screenPageViews']
    ],
    'dateRanges' => [[
        'startDate' => $startDate,
        'endDate' => $endDate
    ]]
]);

// Ejecutar reporte
$response = $analytics->properties->runReport('properties/482504441', $request);

// Construir respuesta
$datos = ['fechas' => [], 'pageViews' => []];

foreach ($response->getRows() as $fila) {
    $fecha = $fila->getDimensionValues()[0]->getValue();
    $vistas = (int) $fila->getMetricValues()[0]->getValue();

    $datos['fechas'][] = $fecha;
    $datos['pageViews'][] = $vistas;
}

// Enviar JSON
header('Content-Type: application/json');
echo json_encode($datos);

