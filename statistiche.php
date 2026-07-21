<?php

include 'db.php';



if (ABILITA_CORS) {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: *');
    header('Access-Control-Expose-Headers: *');
    header('Access-Control-Max-Age: 86400');
    if (strtolower($_SERVER['REQUEST_METHOD']) == 'options')
        exit();
}

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);

    echo json_encode([
        'success' => false,
        'message' => 'Metodo non consentito'
    ]);

    exit;
}

$jsonBody = file_get_contents('php://input');
$body = json_decode($jsonBody, true);

if (!is_array($body) || empty($body['url']) || empty($body['eventType'])) {
    http_response_code(400);

    echo json_encode([
        'success' => false,
        'message' => 'Parametri mancanti'
    ]);

    exit;
}

$url = trim($body['url']);
$eventType = trim($body['eventType']);

insertEvent($url, $eventType);

$data = new DateTime();

$minuti = (int)$data->format('i');

if ($minuti < 30) {
    $data->setTime((int)$data->format('H'), 0, 0);
} else {
    $data->setTime((int)$data->format('H'), 30, 0);
}

$response = [
    'url' => $url,
    'eventType' => $eventType,
    'eventDate' => $data->format('Y-m-d H:i:s')
];

http_response_code(201);

echo json_encode($response);
