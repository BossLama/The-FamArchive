<?php

use FamArchive\Models\Person;
use FamArchive\Models\Persontree;

require_once '../vendor/autoload.php';
header('Content-Type: application/json');

$id = $_GET['id'] ?? null;

if(!$id) {
    $response['status']    = 'error';
    $response['message']   = 'Please provide a person id';
    echo json_encode($response, JSON_PRETTY_PRINT);
    exit;
}

$person = Person::getById($id);
$personArray = $person->toArray();
$personArray['father'] = null;
$personArray['mother'] = null;
$personArray['siblings'] = [];
$personArray['children'] = [];
$personArray['partners'] = [];
$personTree = Persontree::getByPersonId($id);

if($personTree)
{
    if($personTree->getFatherID()) {
        $father = Person::getById($personTree->getFatherID());
        $personArray['father'] = $father->toArray();
    }

    if($personTree->getMotherID()) {
        $mother = Person::getById($personTree->getMotherID());
        $personArray['mother'] = $mother->toArray();
    }
}

$siblingIds = Persontree::getSiblings($id);
foreach($siblingIds as $siblingId) {
    $sibling = Person::getById($siblingId);
    $personArray['siblings'][] = $sibling->toArray();
}

$childrenIds = Persontree::getChildren($id);
foreach($childrenIds as $childId) {
    $child = Person::getById($childId);
    $personArray['children'][] = $child->toArray();
}

$partnerIds = Persontree::getPartners($id);
foreach($partnerIds as $partnerId) {
    $partner = Person::getById($partnerId);
    $personArray['partners'][] = $partner->toArray();
}

$response['status']    = 'success';
$response['message']   = 'Person found';
$response['person']    = $personArray;
echo json_encode($response, JSON_PRETTY_PRINT);

?>