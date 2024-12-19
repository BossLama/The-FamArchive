<?php

use FamArchive\Models\Personevent;

require_once '../vendor/autoload.php';
header('Content-Type: application/json');

$id = $_GET['id'] ?? null;

if($id == null || strlen($id) <= 0)
{
    $response['status']    = 'error';
    $response['message']   = 'Please provide a valid person id';
    echo json_encode($response, JSON_PRETTY_PRINT);
    exit;
}

$events = Personevent::getByPersonId($id);
$eventsArray = [];

foreach($events as $event)
{
    $eventsArray[] = $event->toArray();
}

$response['status']    = 'success';
$response['message']   = 'Events fetched successfully';
$response['data']      = $eventsArray;

echo json_encode($response, JSON_PRETTY_PRINT);

?>