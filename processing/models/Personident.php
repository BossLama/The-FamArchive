<?php

namespace FamArchive\Models;

use FamArchive\DatabaseConnector;

class Personident
{
    private $id;
    private $person_id;
    private $image_id;
    private $x;
    private $y;
    private $width;
    private $height;

    public function __construct(array $parameters)
    {
        $this->id = $parameters['id'] ?? null;
        $this->person_id = $parameters['person_id'] ?? null;
        $this->image_id = $parameters['image_id'] ?? null;
        $this->x = $parameters['x'] ?? null;
        $this->y = $parameters['y'] ?? null;
        $this->width = $parameters['width'] ?? null;
        $this->height = $parameters['height'] ?? null;
    }

    // Returns personident as an array
    public function toArray()
    {
        return [
            'id' => $this->id,
            'person_id' => $this->person_id,
            'image_id' => $this->image_id,
            'x' => $this->x,
            'y' => $this->y,
            'width' => $this->width,
            'height' => $this->height
        ];
    }

    // Save the personident to the database
    public function save()
    {
        $dbConnector = new DatabaseConnector();
        $dbConnection = $dbConnector->getConnection();

        if(isset($this->id))
        {
            $sql = "UPDATE personidents SET person_id = :person_id, image_id = :image_id, x = :x, y = :y, width = :width, height = :height WHERE id = :id";
            $statement = $dbConnection->prepare($sql);
            $statement->execute([
                'id' => $this->id,
                'person_id' => $this->person_id,
                'image_id' => $this->image_id,
                'x' => $this->x,
                'y' => $this->y,
                'width' => $this->width,
                'height' => $this->height
            ]);
        }
        else
        {
            $sql = "INSERT INTO personidents (person_id, image_id, x, y, width, height) VALUES (:person_id, :image_id, :x, :y, :width, :height)";
            $statement = $dbConnection->prepare($sql);
            $statement->execute([
                'person_id' => $this->person_id,
                'image_id' => $this->image_id,
                'x' => $this->x,
                'y' => $this->y,
                'width' => $this->width,
                'height' => $this->height
            ]);
        }
    }

    // Delete the personident from the database
    public function delete()
    {
        $dbConnector = new DatabaseConnector();
        $dbConnection = $dbConnector->getConnection();

        $sql = "DELETE FROM personidents WHERE id = :id";
        $statement = $dbConnection->prepare($sql);
        $statement->execute([
            'id' => $this->id
        ]);
    }

    // Get the personident by id
    public static function getById(int $id)
    {
        $dbConnector = new DatabaseConnector();
        $dbConnection = $dbConnector->getConnection();

        $sql = "SELECT * FROM personidents WHERE id = :id";
        $statement = $dbConnection->prepare($sql);
        $statement->execute([
            'id' => $id
        ]);

        $personident = $statement->fetch();
        return new Personident($personident);
    }

    // Get all personidents by image id
    public static function getByImageId(int $image_id)
    {
        $dbConnector = new DatabaseConnector();
        $dbConnection = $dbConnector->getConnection();

        $sql = "SELECT * FROM personidents WHERE image_id = :image_id";
        $statement = $dbConnection->prepare($sql);
        $statement->execute([
            'image_id' => $image_id
        ]);

        $personidents = [];
        while($row = $statement->fetch())
        {
            $personidents[] = new Personident($row);
        }

        return $personidents;
    }

    // Get all personidents by person id
    public static function getByPersonId(int $person_id)
    {
        $dbConnector = new DatabaseConnector();
        $dbConnection = $dbConnector->getConnection();

        $sql = "SELECT * FROM personidents WHERE person_id = :person_id";
        $statement = $dbConnection->prepare($sql);
        $statement->execute([
            'person_id' => $person_id
        ]);

        $personidents = [];
        while($row = $statement->fetch())
        {
            $personidents[] = new Personident($row);
        }

        return $personidents;
    }

    // Get all personidents
    public static function getAll()
    {
        $dbConnector = new DatabaseConnector();
        $dbConnection = $dbConnector->getConnection();

        $sql = "SELECT * FROM personidents";
        $statement = $dbConnection->prepare($sql);
        $statement->execute();

        $personidents = [];
        while($row = $statement->fetch())
        {
            $personidents[] = new Personident($row);
        }

        return $personidents;
    }

    // Getter
    public function getID(){return $this->id;}
    public function getPersonId(){return $this->person_id;}
    public function getImageId(){return $this->image_id;}
    public function getX(){return $this->x;}
    public function getY(){return $this->y;}
    public function getWidth(){return $this->width;}
    public function getHeight(){return $this->height;}
    
    // Setter
    public function setPersonId($person_id){$this->person_id = $person_id;}
    public function setImageId($image_id){$this->image_id = $image_id;}
    public function setX($x){$this->x = $x;}
    public function setY($y){$this->y = $y;}
    public function setWidth($width){$this->width = $width;}
    public function setHeight($height){$this->height = $height;}
}

?>