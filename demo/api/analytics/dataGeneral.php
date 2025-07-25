<?php
require __DIR__ .'/../vendor/autoload.php';
require_once __DIR__ . '/../middleware/api-bootstrap.php'; // CORS

use Google\Client;
use Google\Service\AnalyticsData;
use Google\Service\AnalyticsData\RunReportRequest;

// Configuración del cliente
$client = new Client();
$client->setAuthConfig(__DIR__ .'/../secure/analytics-key.json');
$client->addScope(AnalyticsData::ANALYTICS_READONLY);

$analytics = new AnalyticsData($client);
$body = file_get_contents('php://input');
$data = json_decode($body, true);
// Fechas desde el frontend
$startDate = isset($data['inicio']) ? $data['inicio'] : date('Y-m-d', strtotime('-7 days'));
$endDate   = isset($data['fin']) ? $data['fin'] : date('Y-m-d');
$propertyId = 'properties/482504441'; // ← Reemplaza por tu ID real

$request = new Google\Service\AnalyticsData\RunReportRequest([
    'dimensions' => [['name' => 'eventName']],
    'metrics' => [['name' => 'eventCount']],
    'dateRanges' => [[
        'startDate' => $startDate,
        'endDate' => $endDate
    ]]
]);

$response = $analytics->properties->runReport($propertyId, $request);

// Formatear datos
$eventos = [];
foreach ($response->getRows() as $row) {
    $evento = $row->getDimensionValues()[0]->getValue();
    $conteo = (int)$row->getMetricValues()[0]->getValue();
    $eventos[] = ['nombre' => $evento, 'conteo' => $conteo];
}

// Devolver como JSON
header('Content-Type: application/json');
echo json_encode(['eventos' => $eventos]);
