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
        ['name' => 'pageTitle'] // Agrupación por título
    ],
    'metrics' => [
        ['name' => 'screenPageViews']
    ],
    'dateRanges' => [[
        'startDate' => $startDate,
        'endDate' => $endDate
    ]],
    'limit' => 10,
    'orderBys' => [[
        'metric' => ['metricName' => 'screenPageViews'],
        'desc' => true
    ]]
]);

$response = $analytics->properties->runReport('properties/482504441', $request);

// Formatear respuesta
$datos = [];
foreach ($response->getRows() as $row) {
    $pagina = $row->getDimensionValues()[0]->getValue();
    $vistas = (int) $row->getMetricValues()[0]->getValue();
    $datos[] = ['pagina' => $pagina, 'vistas' => $vistas];
}

// Enviar JSON
header('Content-Type: application/json');
echo json_encode(['paginas' => $datos]);

