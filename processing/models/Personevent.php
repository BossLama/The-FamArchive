<?php

namespace FamArchive\Models;

use FamArchive\DatabaseConnector;

class Personevent
{
    private $id;
    private $personId;
    private $year;
    private $month;
    private $day;
    private $description;
    private $createdAt;

    public function __construct(array $parameters)
    {
        $this->id           = $parameters['id'] ?? null;
        $this->personId     = $parameters['person_id'] ?? null;
        $this->year         = $parameters['year'] ?? null;
        $this->month        = $parameters['month'] ?? null;
        $this->day          = $parameters['day'] ?? null;
        $this->description  = $parameters['description'] ?? null;
        $this->createdAt    = $parameters['created_at'] ?? null;
    }

    // Returns person as an array
    public function toArray()
    {
        return [
            'id'            => $this->id,
            'person_id'     => $this->personId,
            'year'          => $this->year,
            'month'         => $this->month,
            'day'           => $this->day,
            'description'   => $this->description,
            'created_at'    => $this->createdAt
        ];
    }

    // Save the event to the database
    public function save()
    {
        $dbConnector = new DatabaseConnector();
        $dbConnection = $dbConnector->getConnection();

        if(isset($this->id))
        {
            $sql = "UPDATE personevents SET year = :year, month = :month, day = :day, description = :description WHERE id = :id";
            $statement = $dbConnection->prepare($sql);
            $statement->execute([
                'year'          => $this->year,
                'month'         => $this->month,
                'day'           => $this->day,
                'description'   => $this->description,
                'id'            => $this->id
            ]);
        }
        else
        {
            $sql = "INSERT INTO personevents (person_id, year, month, day, description) VALUES (:person_id, :year, :month, :day, :description)";
            $statement = $dbConnection->prepare($sql);
            $statement->execute([
                'person_id'     => $this->personId,
                'year'          => $this->year,
                'month'         => $this->month,
                'day'           => $this->day,
                'description'   => $this->description
            ]);

            $this->id = $dbConnection->lastInsertId();
        }
    }

    // Delete the event event from the database
    public function delete()
    {
        $dbConnector = new DatabaseConnector();
        $dbConnection = $dbConnector->getConnection();

        $sql = "DELETE FROM personevents WHERE id = :id";
        $statement = $dbConnection->prepare($sql);
        $statement->execute(['id' => $this->id]);
    }

    // Get all events for a person
    public static function getByPersonId($personId)
    {
        $dbConnector = new DatabaseConnector();
        $dbConnection = $dbConnector->getConnection();

        $sql = "SELECT * FROM personevents WHERE person_id = :person_id ORDER BY year, month, day";
        $statement = $dbConnection->prepare($sql);
        $statement->execute(['person_id' => $personId]);

        $events = [];
        while($row = $statement->fetch())
        {
            $events[] = new Personevent($row);
        }

        return $events;
    }

    // Get an event by id
    public static function getById($id)
    {
        $dbConnector = new DatabaseConnector();
        $dbConnection = $dbConnector->getConnection();

        $sql = "SELECT * FROM personevents WHERE id = :id";
        $statement = $dbConnection->prepare($sql);
        $statement->execute(['id' => $id]);

        $row = $statement->fetch();
        if($row)
        {
            return new Personevent($row);
        }
        else
        {
            return null;
        }
    }

    // Get all events
    public static function getAll()
    {
        $dbConnector = new DatabaseConnector();
        $dbConnection = $dbConnector->getConnection();

        $sql = "SELECT * FROM personevents ORDER BY year, month, day";
        $statement = $dbConnection->prepare($sql);
        $statement->execute();

        $events = [];
        while($row = $statement->fetch())
        {
            $events[] = new Personevent($row);
        }

        return $events;
    }

    // Getter
    public function getID() {return $this->id;}
    public function getPersonID() {return $this->personId;}
    public function getYear() {return $this->year;}
    public function getMonth() {return $this->month;}
    public function getDay() {return $this->day;}
    public function getDescription() {return $this->description;}
    public function getCreatedAt() {return $this->createdAt;}

    // Setter
    public function setPersonID($personId) {$this->personId = $personId;}
    public function setYear($year) {$this->year = $year;}
    public function setMonth($month) {$this->month = $month;}
    public function setDay($day) {$this->day = $day;}
    public function setDescription($description) {$this->description = $description;}
}

?>