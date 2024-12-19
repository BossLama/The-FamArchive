<?php

use FamArchive\Models\Image;
use FamArchive\Models\Personident;

require_once '../vendor/autoload.php';
header('Content-Type: application/json');

$image          = $_FILES['image'] ?? null;
$day            = $_POST['day'] ?? null;
$month          = $_POST['month'] ?? null;
$year           = $_POST['year'] ?? null;
$description    = $_POST['description'] ?? null;
$location       = $_POST['location'] ?? null;
$latitude       = $_POST['latitude'] ?? null;
$longitude      = $_POST['longitude'] ?? null;
$identified     = $_POST['idenified_persons'] ?? null;

if(strlen($day) == 0) $day = null;
if(strlen($month) == 0) $month = null;
if(strlen($year) == 0) $year = null;
if(strlen($description) == 0) $description = null;
if(strlen($location) == 0) $location = null;
if(strlen($latitude) == 0) $latitude = null;
if(strlen($longitude) == 0) $longitude = null;

if($image == null)
{
    $response = array();
    $response['status'] = 'error';
    $response['message'] = 'Image is required';
    echo json_encode($response);
    exit;
}

if($identified != null)
{
    $identified = json_decode($identified, true);
}

$target_dir = './../storage/';
$random_name = uniqid() . '.' . pathinfo($image['name'], PATHINFO_EXTENSION);
$target_file = $target_dir . $random_name;

if(!move_uploaded_file($image['tmp_name'], $target_file))
{
    $response = array();
    $response['status'] = 'error';
    $response['message'] = 'Failed to upload image';
    echo json_encode($response);
    exit;
}   

$imageModel = new Image([
    'year' => $year,
    'month' => $month,
    'day' => $day,
    'description' => $description,
    'location' => $location,
    'latitude' => $latitude,
    'longitude' => $longitude,
    'url' => $random_name
]);
$imageModel->save();

foreach($identified as $identifiedPerson)
{
    $ident = new Personident([
        'person_id' => $identifiedPerson['person_id'],
        'image_id' => $imageModel->getID(),
        'x' => $identifiedPerson['x'],
        'y' => $identifiedPerson['y'],
        'width' => $identifiedPerson['width'],
        'height' => $identifiedPerson['height'],
    ]);
    $ident->save();
}

$response = array();
$response['status'] = 'success';
$response['message'] = 'Image uploaded successfully';
$response['data'] = $imageModel->toArray();
echo json_encode($response);
exit;
