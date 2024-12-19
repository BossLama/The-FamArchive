<?php

use FamArchive\Models\Image;

require_once '../vendor/autoload.php';
header('Content-Type: application/json');

$offset = $_GET['offset'] ?? 0;
$limit = $_GET['limit'] ?? 500;

$images = Image::getAll();
$images = array_slice($images, $offset, $limit);

$imageArray = [];

foreach($images as $image)
{
    $imageArray[] = $image->toArray();
}

$response = array();
$response['status'] = 'success';
$response['message'] = 'Images retrieved successfully';
$response['data'] = $imageArray;

echo json_encode($response);

?>