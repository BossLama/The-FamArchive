<?php

use FamArchive\Models\Personevent;

require_once '../vendor/autoload.php';
header('Content-Type: application/json');

$request = json_decode(file_get_contents('php://input'), true);

$person_id      = $request['person_id'] ?? null;
$day            = $request['day'] ?? null;
$month          = $request['month'] ?? null;
$year           = $request['year'] ?? null;
$description    = $request['description'] ?? null;

if($person_id == null || strlen($person_id) <= 0)
{
    $response['status']    = 'error';
    $response['message']   = 'Please provide a valid person id';
    echo json_encode($response, JSON_PRETTY_PRINT);
    exit;
}

$event = new Personevent([
    'person_id'     => $person_id,
    'day'           => $day,
    'month'         => $month,
    'year'          => $year,
    'description'   => $description
]);

$event->save();

$response['status']    = 'success';
$response['message']   = 'Event created successfully';
$response['event']     = $event->toArray();
echo json_encode($response, JSON_PRETTY_PRINT);


?>