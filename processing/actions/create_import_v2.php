<?php

require_once '../vendor/autoload.php';

$dbConnector = new FamArchive\DatabaseConnector();
$dbConnection = $dbConnector->getConnection();

$response = array();

if (!isset($_FILES['import_zip'])) {
    $response['status'] = 'error';
    $response['message'] = 'No import ZIP file provided';
    echo json_encode($response);
    exit;
}

$import_zip = $_FILES['import_zip']['tmp_name'];
$zip = new ZipArchive();

if ($zip->open($import_zip) !== true) {
    $response['status'] = 'error';
    $response['message'] = 'Could not open ZIP archive';
    echo json_encode($response);
    exit;
}

$temp_dir = sys_get_temp_dir() . '/import_' . uniqid();
mkdir($temp_dir, 0777, true);

if (!$zip->extractTo($temp_dir)) {
    $response['status'] = 'error';
    $response['message'] = 'Could not extract ZIP archive';
    echo json_encode($response);
    exit;
}

$zip->close();

$import_image_folder = $temp_dir . "/images";
if (!file_exists($import_image_folder)) {
    $response['status'] = 'error';
    $response['message'] = 'Import folder does not contain an images folder';
    exit;
}

$images = scandir($import_image_folder);
foreach ($images as $image) {
    if ($image == '.' || $image == '..') {
        continue;
    }

    $image_path = $import_image_folder . "/" . $image;
    $image_dest = "../storage/" . $image;
    copy($image_path, $image_dest);
}

$persons_json = file_get_contents($temp_dir . "/persons.json");
$persons = json_decode($persons_json, true);

$events_json = file_get_contents($temp_dir . "/events.json");
$events = json_decode($events_json, true);

$trees_json = file_get_contents($temp_dir . "/trees.json");
$trees = json_decode($trees_json, true);

$images_json = file_get_contents($temp_dir . "/images.json");
$images = json_decode($images_json, true);

$ident_json = file_get_contents($temp_dir . "/idents.json");
$idents = json_decode($ident_json, true);

# Reset database
$sql = "TRUNCATE TABLE persons";
$stmt = $dbConnection->prepare($sql);
$stmt->execute();

$sql = "TRUNCATE TABLE personevents";
$stmt = $dbConnection->prepare($sql);
$stmt->execute();

$sql = "TRUNCATE TABLE persontrees";
$stmt = $dbConnection->prepare($sql);
$stmt->execute();

$sql = "TRUNCATE TABLE images";
$stmt = $dbConnection->prepare($sql);
$stmt->execute();

$sql = "TRUNCATE TABLE personidents";
$stmt = $dbConnection->prepare($sql);
$stmt->execute();

# Import persons
foreach ($persons as $person) {
    $sql = "INSERT INTO persons (id, first_name, last_name, nut_name, birth_year, birth_month, birth_day, death_year, death_month, death_day, created_at) VALUES (:id, :first_name, :last_name, :nut_name, :birth_year, :birth_month, :birth_day, :death_year, :death_month, :death_day, :created_at)";
    $stmt = $dbConnection->prepare($sql);
    $stmt->execute($person);
}

# Import events
foreach ($events as $event) {
    $sql = "INSERT INTO personevents (id, person_id, year, month, day, description, created_at) VALUES (:id, :person_id, :year, :month, :day, :description, :created_at)";
    $stmt = $dbConnection->prepare($sql);
    $stmt->execute($event);
}

# Import trees
foreach ($trees as $tree) {
    $sql = "INSERT INTO persontrees (person_id, mother_id, father_id, created_at) VALUES (:person_id, :mother_id, :father_id, :created_at)";
    $stmt = $dbConnection->prepare($sql);
    $stmt->execute($tree);
}

# Import images
foreach ($images as $image) {
    $sql = "INSERT INTO images (id, year, month, day, description, location, latitude, longitude, url, created_at) VALUES (:id, :year, :month, :day, :description, :location, :latitude, :longitude, :url, :created_at)";
    $stmt = $dbConnection->prepare($sql);
    $stmt->execute($image);
}

# Import idents
foreach ($idents as $ident) {
    $sql = "INSERT INTO personidents (id, person_id, image_id, x, y, width, height) VALUES (:id, :person_id, :image_id, :x, :y, :width, :height)";
    $stmt = $dbConnection->prepare($sql);
    $stmt->execute($ident);
}

# Cleanup
deleteTempDir($temp_dir);

function deleteTempDir($dir)
{
    foreach (scandir($dir) as $file) {
        if ($file !== '.' && $file !== '..') {
            $path = "$dir/$file";
            is_dir($path) ? deleteTempDir($path) : unlink($path);
        }
    }
    rmdir($dir);
}

$response['status'] = 'success';
$response['message'] = 'Import created successfully.';
echo json_encode($response);


?>
