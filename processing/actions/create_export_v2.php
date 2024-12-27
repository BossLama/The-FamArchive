<?php

use FamArchive\Models\Image;
use FamArchive\Models\Person;
use FamArchive\Models\Personevent;
use FamArchive\Models\Personident;
use FamArchive\Models\Persontree;

header('Content-Type: application/json');
require_once '../vendor/autoload.php';

$EXPORT_PATH = '../exports/';
$EXPORT_NAME = 'export_' . date('YmdHis');
$EXPORT_FILE = $EXPORT_PATH . $EXPORT_NAME . '.zip';
$zip = new ZipArchive();

if ($zip->open($EXPORT_FILE, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
    die("Could not create ZIP archive.");
}

$images_path = "../storage/";
$images = scandir($images_path);

# Export images
foreach ($images as $image) {
    if ($image == '.' || $image == '..') {
        continue;
    }

    $image_path = $images_path . $image;
    $zip->addFile($image_path, 'images/' . $image);
}

# Export persons
$persons = Person::getAll();
$persons_array = array();
foreach ($persons as $person) {
    $person_array = $person->toArray();
    $persons_array[] = $person_array;
}
$zip->addFromString('persons.json', json_encode($persons_array, JSON_PRETTY_PRINT));

# Export persons trees
$trees = Persontree::getAll();
$trees_array = array();
foreach ($trees as $tree) {
    $tree_array = $tree->toArray();
    $trees_array[] = $tree_array;
}
$zip->addFromString('trees.json', json_encode($trees_array, JSON_PRETTY_PRINT));

# Export events
$events = Personevent::getAll();
$events_array = array();
foreach ($events as $event) {
    $event_array = $event->toArray();
    $events_array[] = $event_array;
}
$zip->addFromString('events.json', json_encode($events_array, JSON_PRETTY_PRINT));

# Export images
$images = Image::getAll();
$images_array = array();
foreach ($images as $image) {
    $image_array = $image->toArray();
    $images_array[] = $image_array;
}
$zip->addFromString('images.json', json_encode($images_array, JSON_PRETTY_PRINT));

# Export idents
$idents = Personident::getAll();
$idents_array = array();
foreach ($idents as $ident) {
    $ident_array = $ident->toArray();
    $idents_array[] = $ident_array;
}
$zip->addFromString('idents.json', json_encode($idents_array, JSON_PRETTY_PRINT));
$zip->close();

$response = array();
$response['status'] = 'success';
$response['message'] = 'Export created successfully.';
$response['file'] = $EXPORT_NAME . '.zip';

echo json_encode($response);
?>
