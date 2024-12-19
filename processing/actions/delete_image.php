<?php

use FamArchive\Models\Image;

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

$target_file = './../storage/' . $image->getUrl();

if(!unlink($target_file))
{
    $response = array();
    $response['status'] = 'error';
    $response['message'] = 'Failed to delete image';
    echo json_encode($response);
    exit;
}
$image->delete();

$response = array();
$response['status'] = 'success';
$response['message'] = 'Image deleted successfully';

echo json_encode($response);

?>