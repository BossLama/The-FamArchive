<?php

use FamArchive\Models\Image;

require_once '../vendor/autoload.php';
header('Content-Type: application/json');

$images = Image::getAll();
$results = array();

foreach($images as $image)
{
    if($image->getLatitude() !== null && $image->getLongitude() !== null)
    {
        $entry = array();
        $entry['id'] = $image->getId();
        $entry['latitude'] = $image->getLatitude();
        $entry['longitude'] = $image->getLongitude();
        $results[] = $entry;
    }
}

$response = array();
$response['status'] = 'success';
$response['message'] = 'Image locations retrieved successfully';
$response['data'] = $results;

echo json_encode($response);

?>