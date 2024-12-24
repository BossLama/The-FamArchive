<?php

require_once '../vendor/autoload.php';

function handleImport($zipFilePath)
{
    $dbConnector = new FamArchive\DatabaseConnector();
    $dbConnection = $dbConnector->getConnection();

    $importFolder = '../imports/' . pathinfo($zipFilePath, PATHINFO_FILENAME);

    // Extract ZIP
    $zip = new ZipArchive();
    if ($zip->open($zipFilePath) === TRUE) {
        $zip->extractTo($importFolder);
        $zip->close();
    } else {
        echo "Fehler beim Entpacken der ZIP-Datei.";
        exit;
    }

    $importImageFolder = $importFolder . "/images";
    if (!file_exists($importImageFolder)) {
        echo "Der Import-Ordner enthält keinen images-Ordner <br>";
        exit;
    }

    $images = scandir($importImageFolder);
    foreach ($images as $image) {
        if ($image == '.' || $image == '..') {
            continue;
        }

        $imagePath = $importImageFolder . "/" . $image;
        $imageDest = "../storage/" . $image;
        copy($imagePath, $imageDest);
        echo "Kopiere Bild: {$image} <br>";
    }

    $dataFiles = [
        'persons.json' => "persons",
        'events.json' => "personevents",
        'trees.json' => "persontrees",
        'images.json' => "images",
        'idents.json' => "personidents"
    ];

    foreach ($dataFiles as $fileName => $tableName) {
        $filePath = $importFolder . "/" . $fileName;
        if (file_exists($filePath)) {
            $data = json_decode(file_get_contents($filePath), true);
            foreach ($data as $row) {
                $columns = implode(", ", array_keys($row));
                $placeholders = ":" . implode(", :", array_keys($row));

                $sql = "INSERT INTO {$tableName} ({$columns}) VALUES ({$placeholders})";
                $stmt = $dbConnection->prepare($sql);
                $stmt->execute($row);
                echo "Importiere {$tableName} Eintrag: " . json_encode($row) . " <br>";
            }
        }
    }

    echo "Import abgeschlossen <br>";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['import_file'])) {
    $uploadDir = '../uploads/';
    $uploadFile = $uploadDir . basename($_FILES['import_file']['name']);

    if (move_uploaded_file($_FILES['import_file']['tmp_name'], $uploadFile)) {
        echo "Datei erfolgreich hochgeladen: {$uploadFile} <br>";
        handleImport($uploadFile);
    } else {
        echo "Fehler beim Hochladen der Datei.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FamArchive Importer</title>
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
        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        input[type="file"] {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
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
        <h1>FamArchive Importer</h1>
        <form method="POST" enctype="multipart/form-data">
            <label for="import_file">ZIP-Datei hochladen:</label>
            <input type="file" name="import_file" id="import_file" accept=".zip" required>
            <button type="submit">Import starten</button>
        </form>
    </div>
</body>
</html>
