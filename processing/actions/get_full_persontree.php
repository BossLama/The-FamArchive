<?php

use FamArchive\Models\Person;
use FamArchive\Models\Persontree;

require_once '../vendor/autoload.php';
header('Content-Type: application/json');

function getPersonTree($person_id)
{
    $person = Person::getById($person_id);
    if($person == null){
        return null;
    }

    $relation = Persontree::getByPersonId($person_id);
    if($relation == null){
        $array = $person->toArray();
        $array['father'] = null;
        $array['mother'] = null;
        return $array;
    }

    $father = getPersonTree($relation->getFatherId());
    $mother = getPersonTree($relation->getMotherId());

    $array = $person->toArray();
    $array['father'] = $father;
    $array['mother'] = $mother;

    return $array;
}

$person_id = $_GET['id'] ?? null;
if(!$person_id) {
    $response['status']    = 'error';
    $response['message']   = 'Please provide a person id';
    echo json_encode($response, JSON_PRETTY_PRINT);
    exit;
}

$personArray = getPersonTree($person_id);

$response['status']        = 'success';
$response['message']       = 'Person fetched successfully';
$response['data']          =  $personArray;

echo json_encode($response, JSON_PRETTY_PRINT);
?>