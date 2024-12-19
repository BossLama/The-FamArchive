<?php

use FamArchive\Models\Person;
use FamArchive\Models\Persontree;

require_once '../vendor/autoload.php';
header('Content-Type: application/json');

$id = $_GET['id'] ?? null;

$person = Person::getById($id);

if(!$person) {
    $response['status']    = 'error';
    $response['message']   = 'Person not found';
    echo json_encode($response, JSON_PRETTY_PRINT);
    exit;
}

$personArray = $person->toArray();

$persontree = Persontree::getByPersonId($id);
if($persontree != null)
{    
    $personArray['persontree'] = true;
    $father = Person::getById($persontree->getFatherId());
    $mother = Person::getById($persontree->getMotherId());

    if($father) $personArray['father'] = $father->toArray();
    if($mother) $personArray['mother'] = $mother->toArray();
}


$response['status']        = 'success';
$response['message']       = 'Person fetched successfully';
$response['data']          =  $personArray;

echo json_encode($response, JSON_PRETTY_PRINT);
?>