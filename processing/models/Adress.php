<?php

namespace FamArchive\Models;

use FamArchive\DatabaseConnector;

class Adress
{
    private $id;
    private $person_id;
    private $street;
    private $number;
    private $city;
    private $postal_code;
    private $country;
    private $year;
    private $month;
    private $day;
    private $created_at;

    public function __construct(array $parameters)
    {
        $this->id = $parameters['id'] ?? null;
        $this->person_id = $parameters['person_id'] ?? null;
        $this->street = $parameters['street'] ?? null;
        $this->number = $parameters['number'] ?? null;
        $this->city = $parameters['city'] ?? null;
        $this->postal_code = $parameters['postal_code'] ?? null;
        $this->country = $parameters['country'] ?? null;
        $this->year = $parameters['year'] ?? null;
        $this->month = $parameters['month'] ?? null;
        $this->day = $parameters['day'] ?? null;
        $this->created_at = $parameters['created_at'] ?? null;
    }

    // Returns person as an array
    public function toArray()
    {
        return [
            'id' => $this->id,
            'person_id' => $this->person_id,
            'street' => $this->street,
            'number' => $this->number,
            'city' => $this->city,
            'postal_code' => $this->postal_code,
            'country' => $this->country,
            'year' => $this->year,
            'month' => $this->month,
            'day' => $this->day,
            'created_at' => $this->created_at
        ];
    }

    // Save the adress to the database
    public function save()
    {
        $dbConnector = new DatabaseConnector();
        $dbConnection = $dbConnector->getConnection();

        if(isset($this->id))
        {
            $sql = "UPDATE adresses SET street = :street, number = :number, city = :city, postal_code = :postal_code, country = :country, year = :year, month = :month, day = :day WHERE id = :id";
            $statement = $dbConnection->prepare($sql);
            $statement->execute([
                'street' => $this->street,
                'number' => $this->number,
                'city' => $this->city,
                'postal_code' => $this->postal_code,
                'country' => $this->country,
                'year' => $this->year,
                'month' => $this->month,
                'day' => $this->day,
                'id' => $this->id
            ]);
        }
        else
        {
            $sql = "INSERT INTO adresses (person_id, street, number, city, postal_code, country, year, month, day) VALUES (:person_id, :street, :number, :city, :postal_code, :country, :year, :month, :day)";
            $statement = $dbConnection->prepare($sql);
            $statement->execute([
                'person_id' => $this->person_id,
                'street' => $this->street,
                'number' => $this->number,
                'city' => $this->city,
                'postal_code' => $this->postal_code,
                'country' => $this->country,
                'year' => $this->year,
                'month' => $this->month,
                'day' => $this->day
            ]);
        }
    }

    // Delete a address from the database
    public function delete()
    {
        $dbConnector = new DatabaseConnector();
        $dbConnection = $dbConnector->getConnection();

        $sql = "DELETE FROM adresses WHERE id = :id";
        $statement = $dbConnection->prepare($sql);
        $statement->execute(['id' => $this->id]);
    }

    // Get a adress by id
    public static function getById($id)
    {
        $dbConnector = new DatabaseConnector();
        $dbConnection = $dbConnector->getConnection();

        $sql = "SELECT * FROM adresses WHERE id = :id";
        $statement = $dbConnection->prepare($sql);
        $statement->execute(['id' => $id]);

        $adress = $statement->fetch();

        if($adress)
        {
            return new Adress($adress);
        }
        else
        {
            return null;
        }
    }

    // Get all adresses for a person
    public static function getByPersonId($personId)
    {
        $dbConnector = new DatabaseConnector();
        $dbConnection = $dbConnector->getConnection();

        $sql = "SELECT * FROM adresses WHERE person_id = :person_id";
        $statement = $dbConnection->prepare($sql);
        $statement->execute(['person_id' => $personId]);

        $adresses = [];
        while($row = $statement->fetch())
        {
            $adresses[] = new Adress($row);
        }

        return $adresses;
    }

    // Getter
    public function getID() {return $this->id;}
    public function getPersonID() {return $this->person_id;}
    public function getStreet() {return $this->street;}
    public function getNumber() {return $this->number;}
    public function getCity() {return $this->city;}
    public function getPostalCode() {return $this->postal_code;}
    public function getCountry() {return $this->country;}
    public function getYear() {return $this->year;}
    public function getMonth() {return $this->month;}
    public function getDay() {return $this->day;}
    public function getCreatedAt() {return $this->created_at;}

    // Setter
    public function setPersonID($person_id) {$this->person_id = $person_id;}
    public function setStreet($street) {$this->street = $street;}
    public function setNumber($number) {$this->number = $number;}
    public function setCity($city) {$this->city = $city;}
    public function setPostalCode($postal_code) {$this->postal_code = $postal_code;}
    public function setCountry($country) {$this->country = $country;}
    public function setYear($year) {$this->year = $year;}
    public function setMonth($month) {$this->month = $month;}
    public function setDay($day) {$this->day = $day;}
}

?>