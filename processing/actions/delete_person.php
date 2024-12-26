<?php

use FamArchive\Models\Image;
use FamArchive\Models\Person;

require_once '../vendor/autoload.php';
header('Content-Type: application/json');

$id = $_GET['id'] ?? null;

if($id === null)
{
    $response = array();
    $response['status'] = 'error';
    $response['message'] = 'No person ID provided';
    echo json_encode($response);
    exit;
}

$person = Person::getById($id);

if($person === null)
{
    $response = array();
    $response['status'] = 'error';
    $response['message'] = 'Person not found';
    echo json_encode($response);
    exit;
}

$person->delete();

$response = array();
$response['status'] = 'success';
$response['message'] = 'Person deleted successfully';
$response['id'] = $id;

echo json_encode($response);

?>