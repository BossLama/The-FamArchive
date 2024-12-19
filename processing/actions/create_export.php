<?php

use FamArchive\Models\Image;
use FamArchive\Models\Person;
use FamArchive\Models\Personevent;
use FamArchive\Models\Personident;
use FamArchive\Models\Persontree;

require_once '../vendor/autoload.php';

// Function to print messages to the console
function printMessage($msg)
{
    $timestamp = date('Y-m-d H:i:s');
    $prefix = "<span style='font-weight: bold; color: black;padding:0;margin:0'>[{$timestamp}]</span> ";
    $message = "<span style='color: black;padding:0;margin:0'>{$msg}</span>";
    echo "{$prefix}{$message}<br>";
}


$EXPORT_PATH = '../exports/';
$EXPORT_DIR = $EXPORT_PATH . 'export_' . date('YmdHis') . '/';
mkdir($EXPORT_DIR, 0777, true);
mkdir($EXPORT_DIR . 'images', 0777, true);

$images_path = "../storage/";
$images = scandir($images_path);

# Export images
foreach($images as $image)
{
    if($image == '.' || $image == '..')
    {
        continue;
    }

    $image_path = $images_path . $image;
    $image_dest = $EXPORT_DIR . 'images/' . $image;
    copy($image_path, $image_dest);
    printMessage("Copied image: {$image}");
}

# Export persons
$persons = Person::getAll();
$persons_array = array();
foreach($persons as $person)
{
    $person_array = $person->toArray();
    $persons_array[] = $person_array;
    printMessage("Exported person: {$person->getFirstname()} {$person->getLastname()}");
}
file_put_contents($EXPORT_DIR . 'persons.json', json_encode($persons_array, JSON_PRETTY_PRINT));

# Export persons trees
$trees = Persontree::getAll();
$trees_array = array();
foreach($trees as $tree)
{
    $tree_array = $tree->toArray();
    $trees_array[] = $tree_array;
    printMessage("Exported tree: {$tree->getPersonId()}");
}
file_put_contents($EXPORT_DIR . 'trees.json', json_encode($trees_array, JSON_PRETTY_PRINT));

# Export events
$events = Personevent::getAll();
$events_array = array();
foreach($events as $event)
{
    $event_array = $event->toArray();
    $events_array[] = $event_array;
    printMessage("Exported event: {$event->getDescription()}");
}
file_put_contents($EXPORT_DIR . 'events.json', json_encode($events_array, JSON_PRETTY_PRINT));

# Export images
$images = Image::getAll();
$images_array = array();
foreach($images as $image)
{
    $image_array = $image->toArray();
    $images_array[] = $image_array;
    printMessage("Exported image: {$image->getUrl()}");
}
file_put_contents($EXPORT_DIR . 'images.json', json_encode($images_array, JSON_PRETTY_PRINT));

# Export idents
$idents = Personident::getAll();
$idents_array = array();
foreach($idents as $ident)
{
    $ident_array = $ident->toArray();
    $idents_array[] = $ident_array;
    printMessage("Exported ident: {$ident->getPersonId()}");
}
file_put_contents($EXPORT_DIR . 'idents.json', json_encode($idents_array, JSON_PRETTY_PRINT));


printMessage("Export completed successfully");

?>