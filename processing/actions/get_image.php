<?php

use FamArchive\Models\Image;
use FamArchive\Models\Person;
use FamArchive\Models\Personident;

require_once '../vendor/autoload.php';
header('Content-Type: application/json');

$id = $_GET['id'] ?? null;

if($id === null)
{
    $response = array();
    $response['status'] = 'error';
    $response['message'] = 'No image ID provided';
    echo json_encode($response);
    exit;
}

$image = Image::getById($id);
if($image === null)
{
    $response = array();
    $response['status'] = 'error';
    $response['message'] = 'Image not found';
    echo json_encode($response);
    exit;
}

$imageArray = $image->toArray();
$imageArray['idents'] = array();
$personIdents = Personident::getByImageId($id);
foreach($personIdents as $personIdent)
{
    $identArray = $personIdent->toArray();
    $person = Person::getById($personIdent->getPersonId());
    $identArray['person'] = $person->toArray();
    $imageArray['idents'][] = $identArray;
}

$response = array();
$response['status'] = 'success';
$response['message'] = 'Image retrieved successfully';
$response['data'] = $imageArray;

echo json_encode($response);
?>