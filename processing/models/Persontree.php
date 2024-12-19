<?php

namespace FamArchive\Models;

use FamArchive\DatabaseConnector;

class Persontree
{
    private $personId;
    private $motherId;
    private $fatherId;
    private $createdAt;

    public function __construct(array $parameters)
    {
        $this->personId = $parameters['person_id'] ?? null;
        $this->motherId = $parameters['mother_id'] ?? null;
        $this->fatherId = $parameters['father_id'] ?? null;
        $this->createdAt = $parameters['created_at'] ?? null;
    }

    // Returns persontree as an array
    public function toArray()
    {
        return [
            'person_id' => $this->personId,
            'mother_id' => $this->motherId,
            'father_id' => $this->fatherId,
            'created_at' => $this->createdAt
        ];
    }

    // Save the persontree to the database
    public function save()
    {
        $dbConnector = new DatabaseConnector();
        $dbConnection = $dbConnector->getConnection();

        $query = "SELECT * FROM persontrees WHERE person_id = :person_id";
        $statement = $dbConnection->prepare($query);
        $statement->execute(['person_id' => $this->personId]);

        if($statement->rowCount() == 0)
        {
            $sql = "INSERT INTO persontrees (person_id, mother_id, father_id) VALUES (:person_id, :mother_id, :father_id)";
            $statement = $dbConnection->prepare($sql);
            $statement->execute([
                'person_id' => $this->personId,
                'mother_id' => $this->motherId,
                'father_id' => $this->fatherId
            ]);
        }
        else
        {
            $sql = "UPDATE persontrees SET mother_id = :mother_id, father_id = :father_id WHERE person_id = :person_id";
            $statement = $dbConnection->prepare($sql);
            $statement->execute([
                'person_id' => $this->personId,
                'mother_id' => $this->motherId,
                'father_id' => $this->fatherId
            ]);
        }
    }

    // Delete a persontree from the database
    public function delete()
    {
        $dbConnector = new DatabaseConnector();
        $dbConnection = $dbConnector->getConnection();

        $sql = "DELETE FROM persontrees WHERE person_id = :person_id";
        $statement = $dbConnection->prepare($sql);
        $statement->execute(['person_id' => $this->personId]);
    }

    // Get a persontree by person id
    public static function getByPersonId($personId)
    {
        $dbConnector = new DatabaseConnector();
        $dbConnection = $dbConnector->getConnection();

        $sql = "SELECT * FROM persontrees WHERE person_id = :person_id";
        $statement = $dbConnection->prepare($sql);
        $statement->execute(['person_id' => $personId]);

        $row = $statement->fetch();
        if($row)
        {
            return new Persontree($row);
        }
        else
        {
            return null;
        }
    }

    // Get all children id's of a person
    public static function getChildren($personId)
    {
        $dbConnector = new DatabaseConnector();
        $dbConnection = $dbConnector->getConnection();

        $sql = "SELECT * FROM persontrees WHERE mother_id = :person_id OR father_id = :person_id";
        $statement = $dbConnection->prepare($sql);
        $statement->execute(['person_id' => $personId]);

        $children = [];
        while($row = $statement->fetch())
        {
            $children[] = $row['person_id'];
        }

        return $children;
    }

    // Get all siblings id's of a person
    public static function getSiblings($personId)
    {
        $dbConnector = new DatabaseConnector();
        $dbConnection = $dbConnector->getConnection();

        $personTree = Persontree::getByPersonId($personId);
        if(!$personTree)
        {
            return [];
        }	

        $sql = "SELECT * FROM persontrees WHERE (mother_id = :mother_id OR father_id = :father_id) AND person_id != :person_id";
        
        $statement = $dbConnection->prepare($sql);
        $statement->execute([
            'mother_id' => $personTree->getMotherID(),
            'father_id' => $personTree->getFatherID(),
            'person_id' => $personId
        ]);

        $siblings = [];
        while($row = $statement->fetch())
        {
            $siblings[] = $row['person_id'];
        }

        return $siblings;
    }

    // Get all partners id's of a person
    public static function getPartners($personId)
    {
        $dbConnector = new DatabaseConnector();
        $dbConnection = $dbConnector->getConnection();

        $sql = "SELECT * FROM persontrees WHERE mother_id = :person_id OR father_id = :person_id";
        $statement = $dbConnection->prepare($sql);
        $statement->execute(['person_id' => $personId]);

        $partners = [];
        while($row = $statement->fetch())
        {
            if($row['mother_id'] == $personId && !in_array($row['father_id'], $partners))
            {
                $partners[] = $row['father_id'];
            }
            else if($row['father_id'] == $personId && !in_array($row['mother_id'], $partners))
            {
                $partners[] = $row['mother_id'];
            }
        }

        return $partners;
    }

    // Get all persontrees
    public static function getAll()
    {
        $dbConnector = new DatabaseConnector();
        $dbConnection = $dbConnector->getConnection();

        $sql = "SELECT * FROM persontrees";
        $statement = $dbConnection->prepare($sql);
        $statement->execute();

        $persontrees = [];
        while($row = $statement->fetch())
        {
            $persontrees[] = new Persontree($row);
        }

        return $persontrees;
    }

    // Getter 
    public function getPersonID() { return $this->personId; }
    public function getMotherID() { return $this->motherId; }
    public function getFatherID() { return $this->fatherId; }
    public function getCreatedAt() { return $this->createdAt; }

    // Setter
    public function setPersonID($personId) { $this->personId = $personId; }
    public function setMotherID($motherId) { $this->motherId = $motherId; }
    public function setFatherID($fatherId) { $this->fatherId = $fatherId; }
    public function setCreatedAt($createdAt) { $this->createdAt = $createdAt; }
}

?>