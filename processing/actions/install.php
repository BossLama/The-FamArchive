<?php

use FamArchive\DatabaseConnector;

    require_once '../vendor/autoload.php';

    $dbConnector        = new DatabaseConnector();
    $dbConnection       = $dbConnector->getConnection();

    # Person
    # Persontree
    # Personevent
    # Adress
    # Image
    # Personident
    # Collection
    # Collectionentry

    # Persons
    echo 'Creating table for persons <br>';
    $sql = "CREATE TABLE IF NOT EXISTS persons (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        first_name VARCHAR(30),
        last_name VARCHAR(30),
        nut_name VARCHAR(30),
        birth_year INT,
        birth_month TINYINT,
        birth_day TINYINT,
        death_year INT,
        death_month TINYINT,
        death_day TINYINT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    $dbConnection->prepare($sql)->execute();

    # Person tree
    echo 'Creating table for person tree <br>';
    $sql = "CREATE TABLE IF NOT EXISTS persontrees (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        person_id INT UNSIGNED,
        mother_id INT UNSIGNED,
        father_id INT UNSIGNED,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

        FOREIGN KEY (person_id) REFERENCES persons(id) ON DELETE CASCADE,
        FOREIGN KEY (mother_id) REFERENCES persons(id) ON DELETE SET NULL,
        FOREIGN KEY (father_id) REFERENCES persons(id) ON DELETE SET NULL
    )";
    $dbConnection->prepare($sql)->execute();

    # Person event
    echo 'Creating table for person events <br>';
    $sql = "CREATE TABLE IF NOT EXISTS personevents (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        person_id INT UNSIGNED,
        year YEAR,
        month TINYINT,
        day TINYINT,
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (person_id) REFERENCES persons(id) ON DELETE CASCADE
    )";
    $dbConnection->prepare($sql)->execute();

    # Adress
    echo 'Creating table for adresses <br>';
    $sql = "CREATE TABLE IF NOT EXISTS adresses (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        person_id INT UNSIGNED,
        street VARCHAR(50),
        number VARCHAR(10),
        city VARCHAR(30),
        postal_code VARCHAR(10),
        country VARCHAR(30),
        year YEAR,
        month TINYINT,
        day TINYINT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

        FOREIGN KEY (person_id) REFERENCES persons(id) ON DELETE CASCADE
    )";
    $dbConnection->prepare($sql)->execute();

    # Image
    echo 'Creating table for images <br>';
    $sql = "CREATE TABLE IF NOT EXISTS images (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        year YEAR,
        month TINYINT,
        day TINYINT,
        description TEXT,
        location VARCHAR(50),
        latitude DECIMAL(10, 8),
        longitude DECIMAL(11, 8),
        url VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    $dbConnection->prepare($sql)->execute();

    # Person ident
    echo 'Creating table for person idents <br>';
    $sql = "CREATE TABLE IF NOT EXISTS personidents (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        person_id INT UNSIGNED,
        image_id INT UNSIGNED,
        x DECIMAL,
        y DECIMAL,
        width DECIMAL,
        height DECIMAL,

        FOREIGN KEY (person_id) REFERENCES persons(id) ON DELETE CASCADE,
        FOREIGN KEY (image_id) REFERENCES images(id) ON DELETE CASCADE
    )";
    $dbConnection->prepare($sql)->execute();

    # Collection
    echo 'Creating table for collections <br>';
    $sql = "CREATE TABLE IF NOT EXISTS collections (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50),
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    $dbConnection->prepare($sql)->execute();

    # Collection entry
    echo 'Creating table for collection entries <br>';
    $sql = "CREATE TABLE IF NOT EXISTS collectionentries (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        collection_id INT UNSIGNED,
        image_id INT UNSIGNED,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

        FOREIGN KEY (collection_id) REFERENCES collections(id) ON DELETE CASCADE,
        FOREIGN KEY (image_id) REFERENCES images(id) ON DELETE CASCADE
    )";
    $dbConnection->prepare($sql)->execute();


?>