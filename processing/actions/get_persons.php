<?php

use FamArchive\Models\Person;

require_once '../vendor/autoload.php';
header('Content-Type: application/json');

$query                      = $_GET['query'] ?? null;

$persons                    = [];
if($query && strlen($query) > 0)      $persons    = Person::queryByName($query);
if(!$query)     $persons    = Person::getAll();

$result                     = [];
foreach($persons as $person) {
    $result[]               = $person->toArray();
}

$response                  = [];
$response['status']        = 'success';
$response['message']       = 'Persons fetched successfully';
$response['data']          = $result;

echo json_encode($response, JSON_PRETTY_PRINT);
?>