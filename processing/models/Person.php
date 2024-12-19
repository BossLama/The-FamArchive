<?php

namespace FamArchive\Models;

use FamArchive\DatabaseConnector;

class Person
{
    private $id;
    private $firstName;
    private $lastName;
    private $nutName;
    private $birthYear;
    private $birthMonth;
    private $birthDay;
    private $deathYear;
    private $deathMonth;
    private $deathDay;
    private $createdAt;

    public function __construct(array $parameters)
    {
        $this->id           = $parameters['id'] ?? null;
        $this->firstName    = $parameters['first_name'] ?? null;
        $this->lastName     = $parameters['last_name'] ?? null;
        $this->nutName      = $parameters['nut_name'] ?? null;
        $this->birthYear    = $parameters['birth_year'] ?? null;
        $this->birthMonth   = $parameters['birth_month'] ?? null;
        $this->birthDay     = $parameters['birth_day'] ?? null;
        $this->deathYear    = $parameters['death_year'] ?? null;
        $this->deathMonth   = $parameters['death_month'] ?? null;
        $this->deathDay     = $parameters['death_day'] ?? null;
        $this->createdAt    = $parameters['created_at'] ?? null;
    }

    // Returns person as an array
    public function toArray()
    {
        return [
            'id'            => $this->id,
            'first_name'    => $this->firstName,
            'last_name'     => $this->lastName,
            'nut_name'      => $this->nutName,
            'birth_year'    => $this->birthYear,
            'birth_month'   => $this->birthMonth,
            'birth_day'     => $this->birthDay,
            'death_year'    => $this->deathYear,
            'death_month'   => $this->deathMonth,
            'death_day'     => $this->deathDay,
            'created_at'    => $this->createdAt
        ];
    }

    // Save the person to the database
    public function save()
    {
        $dbConnector = new DatabaseConnector();
        $dbConnection = $dbConnector->getConnection();

        if(isset($this->id))
        {
            // Update the person
            $sql = "UPDATE persons SET  first_name = :first_name, 
                last_name = :last_name, 
                nut_name = :nut_name, 
                birth_year = :birth_year, 
                birth_month = :birth_month, 
                birth_day = :birth_day, 
                death_year = :death_year, 
                death_month = :death_month, 
                death_day = :death_day WHERE id = :id";
            $statement = $dbConnection->prepare($sql);
            $statement->execute([
                'id'            => $this->id,
                'first_name'    => $this->firstName,
                'last_name'     => $this->lastName,
                'nut_name'      => $this->nutName,
                'birth_year'    => $this->birthYear,
                'birth_month'   => $this->birthMonth,
                'birth_day'     => $this->birthDay,
                'death_year'    => $this->deathYear,
                'death_month'   => $this->deathMonth,
                'death_day'     => $this->deathDay
            ]);
        }
        else
        {   
            // Insert the person
            $sql = "INSERT INTO persons (first_name, last_name, nut_name, birth_year, birth_month, birth_day, death_year, death_month, death_day) VALUES 
                (:first_name, :last_name, :nut_name, :birth_year, :birth_month, :birth_day, :death_year, :death_month, :death_day)";
            $statement = $dbConnection->prepare($sql);
            $statement->execute([
                'first_name'    => $this->firstName,
                'last_name'     => $this->lastName,
                'nut_name'      => $this->nutName,
                'birth_year'    => $this->birthYear,
                'birth_month'   => $this->birthMonth,
                'birth_day'     => $this->birthDay,
                'death_year'    => $this->deathYear,
                'death_month'   => $this->deathMonth,
                'death_day'     => $this->deathDay
            ]);
            $this->id = $dbConnection->lastInsertId();
        }
    }

    // Delete the person from the database
    public function delete()
    {
        $dbConnector = new DatabaseConnector();
        $dbConnection = $dbConnector->getConnection();

        $sql = "DELETE FROM persons WHERE id = :id";
        $statement = $dbConnection->prepare($sql);
        $statement->execute(['id' => $this->id]);
    }

    // Get all persons from the database
    public static function getAll()
    {
        $dbConnector = new DatabaseConnector();
        $dbConnection = $dbConnector->getConnection();

        $sql = "SELECT * FROM persons ORDER BY first_name, last_name, birth_year";
        $statement = $dbConnection->prepare($sql);
        $statement->execute();

        $persons = [];
        while($row = $statement->fetch())
        {
            $persons[] = new Person($row);
        }

        return $persons;
    }

    // Get a person by id
    public static function getById($id)
    {
        $dbConnector = new DatabaseConnector();
        $dbConnection = $dbConnector->getConnection();

        $sql = "SELECT * FROM persons WHERE id = :id";
        $statement = $dbConnection->prepare($sql);
        $statement->execute(['id' => $id]);

        $row = $statement->fetch();
        if($row)
        {
            return new Person($row);
        }
        else
        {
            return null;
        }
    }

    // Get all persons with matching firstname, lastname or first and lastname
    public static function queryByName($query)
    {
        $dbConnector = new DatabaseConnector();
        $dbConnection = $dbConnector->getConnection();

        $sql = "SELECT * FROM persons WHERE first_name LIKE :query OR last_name LIKE :query OR CONCAT(first_name, ' ', last_name) LIKE :query
            OR CONCAT(last_name, ' ', first_name) LIKE :query ORDER BY first_name, last_name, birth_year";
        $statement = $dbConnection->prepare($sql);
        $statement->execute(['query' => "%$query%"]);

        $persons = [];
        while($row = $statement->fetch())
        {
            $persons[] = new Person($row);
        }

        return $persons;
    }

    // Getter
    public function getID(){ return $this->id; }
    public function getFirstName(){ return $this->firstName; }
    public function getLastName(){ return $this->lastName; }
    public function getNutName(){ return $this->nutName; }
    public function getBirthYear(){ return $this->birthYear; }
    public function getBirthMonth(){ return $this->birthMonth; }
    public function getBirthDay(){ return $this->birthDay; }
    public function getDeathYear(){ return $this->deathYear; }
    public function getDeathMonth(){ return $this->deathMonth; }
    public function getDeathDay(){ return $this->deathDay; }
    public function getCreatedAt(){ return $this->createdAt; }

    // Setter
    public function setFirstName($firstName){ $this->firstName = $firstName; }
    public function setLastName($lastName){ $this->lastName = $lastName; }
    public function setNutName($nutName){ $this->nutName = $nutName; }
    public function setBirthYear($birthYear){ $this->birthYear = $birthYear; }
    public function setBirthMonth($birthMonth){ $this->birthMonth = $birthMonth; }
    public function setBirthDay($birthDay){ $this->birthDay = $birthDay; }
    public function setDeathYear($deathYear){ $this->deathYear = $deathYear; }
    public function setDeathMonth($deathMonth){ $this->deathMonth = $deathMonth; }
    public function setDeathDay($deathDay){ $this->deathDay = $deathDay; }
    public function setCreatedAt($createdAt){ $this->createdAt = $createdAt; }
}

?>