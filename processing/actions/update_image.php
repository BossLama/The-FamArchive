<?php

use FamArchive\Models\Image;
use FamArchive\Models\Personident;

require_once '../vendor/autoload.php';
header('Content-Type: application/json');

$request = json_decode(file_get_contents('php://input'), true);

$image_id       = $request['image_id'] ?? null;
$day            = $request['day'] ?? null;
$month          = $request['month'] ?? null;
$year           = $request['year'] ?? null;
$description    = $request['description'] ?? null;
$location       = $request['location'] ?? null;
$latitude       = $request['latitude'] ?? null;
$longitude      = $request['longitude'] ?? null;
$identified     = $request['created_idents'] ?? null;
$removed_idents = $request['removed_idents'] ?? null;

if(strlen($day) == 0) $day = null;
if(strlen($month) == 0) $month = null;
if(strlen($year) == 0) $year = null;
if(strlen($description) == 0) $description = null;
if(strlen($location) == 0) $location = null;
if(strlen($latitude) == 0) $latitude = null;
if(strlen($longitude) == 0) $longitude = null;

if($image_id == null)
{
    $response = array();
    $response['status'] = 'error';
    $response['message'] = 'Image is required';
    echo json_encode($response);
    exit;
}

$image = Image::getById($image_id);
$image->setDay($day);
$image->setMonth($month);
$image->setYear($year);
$image->setDescription($description);
$image->setLocation($location);
$image->setLatitude($latitude);
$image->setLongitude($longitude);
$image->save();

if($identified != null)
{
    foreach($identified as $ident)
    {
        $personIdent = new Personident([
            'image_id' => $image->getId(),
            'person_id' => $ident['person_id'],
            'x' => $ident['x'],
            'y' => $ident['y'],
            'width' => $ident['width'],
            'height' => $ident['height']
        ]);
        $personIdent->save();
    }
}

if($removed_idents != null)
{
    foreach($removed_idents as $ident)
    {
        $personIdent = Personident::getById($ident);
        $personIdent->delete();
    }
}

$response = array();
$response['status'] = 'success';
$response['message'] = 'Image updated successfully';
echo json_encode($response);
?>