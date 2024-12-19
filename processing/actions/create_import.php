<?php

require_once '../vendor/autoload.php';

$dbConnector = new FamArchive\DatabaseConnector();
$dbConnection = $dbConnector->getConnection();

$import_name = $_GET["import_name"] ?? null;
if($import_name == null)
{
    echo "Bitte gebe einen Import-Ordner-Namen an <br>";
    exit;
}

$import_folder = "../imports/" . $import_name;
echo "Importiere aus: {$import_folder} <br>";
if(!file_exists($import_folder))
{
    echo "Der Import-Ordner existiert nicht <br>";
    exit;
}

$import_image_folder = $import_folder . "/images";
if(!file_exists($import_image_folder))
{
    echo "Der Import-Ordner enthält keinen images-Ordner <br>";
    exit;
}

$images = scandir($import_image_folder);
foreach($images as $image)
{
    if($image == '.' || $image == '..')
    {
        continue;
    }

    $image_path = $import_image_folder . "/" . $image;
    $image_dest = "../storage/" . $image;
    copy($image_path, $image_dest);
    echo "Kopiere Bild: {$image} <br>";
}

$persons_json = file_get_contents($import_folder . "/persons.json");
$persons = json_decode($persons_json, true);

$events_json = file_get_contents($import_folder . "/events.json");
$events = json_decode($events_json, true);

$trees_json = file_get_contents($import_folder . "/trees.json");
$trees = json_decode($trees_json, true);

$images_json = file_get_contents($import_folder . "/images.json");
$images = json_decode($images_json, true);

$ident_json = file_get_contents($import_folder . "/idents.json");
$idents = json_decode($ident_json, true);

# Import persons
foreach($persons as $person)
{
    $sql = "INSERT INTO persons (id, first_name, last_name, nut_name, birth_year, birth_month, birth_day, death_year, death_month, death_day) VALUES (:id, :first_name, :last_name, :nut_name, :birth_year, :birth_month, :birth_day, :death_year, :death_month, :death_day)";
    $stmt = $dbConnection->prepare($sql);
    $stmt->execute($person);
    echo "Importiere Person: {$person['first_name']} {$person['last_name']} <br>";
}

# Import events
foreach($events as $event)
{
    $sql = "INSERT INTO personevents (id, person_id, year, month, day, description) VALUES (:id, :person_id, :year, :month, :day, :description)";
    $stmt = $dbConnection->prepare($sql);
    $stmt->execute($event);
    echo "Importiere Event: {$event['description']} <br>";
}

# Import trees
foreach($trees as $tree)
{
    $sql = "INSERT INTO persontrees (id, person_id, mother_id, father_id) VALUES (:id, :person_id, :mother_id, :father_id)";
    $stmt = $dbConnection->prepare($sql);
    $stmt->execute($tree);
    echo "Importiere Tree: {$tree['person_id']} <br>";
}

# Import images
foreach($images as $image)
{
    $sql = "INSERT INTO images (id, person_id, path) VALUES (:id, :person_id, :path)";
    $stmt = $dbConnection->prepare($sql);
    $stmt->execute($image);
    echo "Importiere Image: {$image['path']} <br>";
}

# Import idents
foreach($idents as $ident)
{
    $sql = "INSERT INTO personidents (id, person_id, ident) VALUES (:id, :person_id, :ident)";
    $stmt = $dbConnection->prepare($sql);
    $stmt->execute($ident);
    echo "Importiere Ident: {$ident['ident']} <br>";
}

echo "Import abgeschlossen <br>";