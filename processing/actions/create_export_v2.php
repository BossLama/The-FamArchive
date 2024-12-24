<?php

use FamArchive\Models\Image;
use FamArchive\Models\Person;
use FamArchive\Models\Personevent;
use FamArchive\Models\Personident;
use FamArchive\Models\Persontree;

require_once '../vendor/autoload.php';

function getExportStatistics()
{
    return [
        'total_images' => count(Image::getAll()),
        'total_persons' => count(Person::getAll()),
        'total_trees' => count(Persontree::getAll()),
        'total_events' => count(Personevent::getAll()),
        'total_idents' => count(Personident::getAll())
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['export'])) {
    $EXPORT_PATH = '../exports/';
    $EXPORT_DIR = $EXPORT_PATH . 'export_' . date('YmdHis') . '/';
    mkdir($EXPORT_DIR, 0777, true);
    mkdir($EXPORT_DIR . 'images', 0777, true);

    $images_path = "../storage/";
    $images = scandir($images_path);

    # Export images
    foreach ($images as $image) {
        if ($image == '.' || $image == '..') {
            continue;
        }

        $image_path = $images_path . $image;
        $image_dest = $EXPORT_DIR . 'images/' . $image;
        copy($image_path, $image_dest);
    }

    # Export persons
    $persons = Person::getAll();
    $persons_array = array_map(fn($person) => $person->toArray(), $persons);
    file_put_contents($EXPORT_DIR . 'persons.json', json_encode($persons_array, JSON_PRETTY_PRINT));

    # Export persons trees
    $trees = Persontree::getAll();
    $trees_array = array_map(fn($tree) => $tree->toArray(), $trees);
    file_put_contents($EXPORT_DIR . 'trees.json', json_encode($trees_array, JSON_PRETTY_PRINT));

    # Export events
    $events = Personevent::getAll();
    $events_array = array_map(fn($event) => $event->toArray(), $events);
    file_put_contents($EXPORT_DIR . 'events.json', json_encode($events_array, JSON_PRETTY_PRINT));

    # Export images
    $image_models = Image::getAll();
    $images_array = array_map(fn($image) => $image->toArray(), $image_models);
    file_put_contents($EXPORT_DIR . 'images.json', json_encode($images_array, JSON_PRETTY_PRINT));

    # Export idents
    $idents = Personident::getAll();
    $idents_array = array_map(fn($ident) => $ident->toArray(), $idents);
    file_put_contents($EXPORT_DIR . 'idents.json', json_encode($idents_array, JSON_PRETTY_PRINT));

    // Create ZIP archive
    $zip = new ZipArchive();
    $zip_file = $EXPORT_PATH . 'export_' . date('YmdHis') . '.zip';

    if ($zip->open($zip_file, ZipArchive::CREATE) === TRUE) {
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($EXPORT_DIR), RecursiveIteratorIterator::LEAVES_ONLY);

        foreach ($files as $file) {
            if (!$file->isDir()) {
                $file_path = $file->getRealPath();
                $relative_path = substr($file_path, strlen($EXPORT_DIR));
                $zip->addFile($file_path, $relative_path);
            }
        }
        $zip->close();
    }

    // Cleanup
    array_map('unlink', glob("$EXPORT_DIR/*"));
    rmdir($EXPORT_DIR);

    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="' . basename($zip_file) . '"');
    header('Content-Length: ' . filesize($zip_file));
    readfile($zip_file);
    unlink($zip_file);
    exit;
}

$stats = getExportStatistics();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FamArchive Exporter</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .stats {
            margin: 20px 0;
        }
        .stats div {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .stats div:last-child {
            border-bottom: none;
        }
        button {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #007BFF;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>FamArchive Exporter</h1>
        <div class="stats">
            <div>Total Images: <?php echo $stats['total_images']; ?></div>
            <div>Total Persons: <?php echo $stats['total_persons']; ?></div>
            <div>Total Trees: <?php echo $stats['total_trees']; ?></div>
            <div>Total Events: <?php echo $stats['total_events']; ?></div>
            <div>Total Idents: <?php echo $stats['total_idents']; ?></div>
        </div>
        <form method="POST">
            <button type="submit" name="export">Jetzt exportieren</button>
        </form>
    </div>
</body>
</html>
