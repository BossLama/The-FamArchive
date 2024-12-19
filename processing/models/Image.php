<?php

namespace FamArchive\Models;

use FamArchive\DatabaseConnector;

class Image
{
    private $id;
    private $year;
    private $month;
    private $day;
    private $description;
    private $location;
    private $latitude;
    private $longitude;
    private $url;
    private $created_at;

    public function __construct(array $parameters)
    {
        $this->id = $parameters['id'] ?? null;
        $this->year = $parameters['year'] ?? null;
        $this->month = $parameters['month'] ?? null;
        $this->day = $parameters['day'] ?? null;
        $this->description = $parameters['description'] ?? null;
        $this->location = $parameters['location'] ?? null;
        $this->latitude = $parameters['latitude'] ?? null;
        $this->longitude = $parameters['longitude'] ?? null;
        $this->url = $parameters['url'] ?? null;
        $this->created_at = $parameters['created_at'] ?? null;
    }

    // Returns image as an array
    public function toArray()
    {
        return [
            'id' => $this->id,
            'year' => $this->year,
            'month' => $this->month,
            'day' => $this->day,
            'description' => $this->description,
            'location' => $this->location,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'url' => $this->url,
            'created_at' => $this->created_at
        ];
    }

    // Save the image to the database
    public function save()
    {
        $dbConnector = new DatabaseConnector();
        $dbConnection = $dbConnector->getConnection();

        if(isset($this->id))
        {
            $sql = "UPDATE images SET year = :year, month = :month, day = :day, description = :description, location = :location, latitude = :latitude, longitude = :longitude, url = :url WHERE id = :id";
            $statement = $dbConnection->prepare($sql);
            $statement->execute([
                'id' => $this->id,
                'year' => $this->year,
                'month' => $this->month,
                'day' => $this->day,
                'description' => $this->description,
                'location' => $this->location,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'url' => $this->url
            ]);
        }
        else
        {
            $sql = "INSERT INTO images (year, month, day, description, location, latitude, longitude, url) VALUES (:year, :month, :day, :description, :location, :latitude, :longitude, :url)";
            $statement = $dbConnection->prepare($sql);
            $statement->execute([
                'year' => $this->year,
                'month' => $this->month,
                'day' => $this->day,
                'description' => $this->description,
                'location' => $this->location,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'url' => $this->url
            ]);
            $this->id = $dbConnection->lastInsertId();
        }
    }

    // Delete the image from the database
    public function delete()
    {
        $imageURL = "./../storage/" . $this->url;
        if(file_exists($imageURL))
        {
            unlink($imageURL);
        }

        $dbConnector = new DatabaseConnector();
        $dbConnection = $dbConnector->getConnection();

        $sql = "DELETE FROM images WHERE id = :id";
        $statement = $dbConnection->prepare($sql);
        $statement->execute([
            'id' => $this->id
        ]);
    }

    // Get image by id
    public static function getById($id)
    {
        $dbConnector = new DatabaseConnector();
        $dbConnection = $dbConnector->getConnection();

        $sql = "SELECT * FROM images WHERE id = :id";
        $statement = $dbConnection->prepare($sql);
        $statement->execute([
            'id' => $id
        ]);

        $image = $statement->fetch();

        if($image)
        {
            return new Image($image);
        }
        else
        {
            return null;
        }
    }

    // Get all images by year
    public static function getByYear($year)
    {
        $dbConnector = new DatabaseConnector();
        $dbConnection = $dbConnector->getConnection();

        $sql = "SELECT * FROM images WHERE year = :year";
        $statement = $dbConnection->prepare($sql);
        $statement->execute([
            'year' => $year
        ]);

        $images = [];
        while($row = $statement->fetch())
        {
            $images[] = new Image($row);
        }

        return $images;
    }

    // Get all images by month and year
    public static function getByMonthAndYear($month, $year)
    {
        $dbConnector = new DatabaseConnector();
        $dbConnection = $dbConnector->getConnection();

        $sql = "SELECT * FROM images WHERE month = :month AND year = :year";
        $statement = $dbConnection->prepare($sql);
        $statement->execute([
            'month' => $month,
            'year' => $year
        ]);

        $images = [];
        while($row = $statement->fetch())
        {
            $images[] = new Image($row);
        }

        return $images;
    }

    // Get all images by day, month and year
    public static function getByDayMonthAndYear($day, $month, $year)
    {
        $dbConnector = new DatabaseConnector();
        $dbConnection = $dbConnector->getConnection();

        $sql = "SELECT * FROM images WHERE day = :day AND month = :month AND year = :year";
        $statement = $dbConnection->prepare($sql);
        $statement->execute([
            'day' => $day,
            'month' => $month,
            'year' => $year
        ]);

        $images = [];
        while($row = $statement->fetch())
        {
            $images[] = new Image($row);
        }

        return $images;
    }

    // Get all images from the database
    public static function getAll()
    {
        $dbConnector = new DatabaseConnector();
        $dbConnection = $dbConnector->getConnection();

        $sql = "SELECT * FROM images ORDER BY year DESC, month DESC, day DESC";
        $statement = $dbConnection->prepare($sql);
        $statement->execute();

        $images = [];
        while($row = $statement->fetch())
        {
            $images[] = new Image($row);
        }

        return $images;
    }

    // Getter
    public function getID(){return $this->id;}
    public function getYear(){return $this->year;}
    public function getMonth(){return $this->month;}
    public function getDay(){return $this->day;}
    public function getDescription(){return $this->description;}
    public function getLocation(){return $this->location;}
    public function getLatitude(){return $this->latitude;}
    public function getLongitude(){return $this->longitude;}
    public function getUrl(){return $this->url;}
    public function getCreatedAt(){return $this->created_at;}

    // Setter
    public function setYear($year){$this->year = $year;}
    public function setMonth($month){$this->month = $month;}
    public function setDay($day){$this->day = $day;}
    public function setDescription($description){$this->description = $description;}
    public function setLocation($location){$this->location = $location;}
    public function setLatitude($latitude){$this->latitude = $latitude;}
    public function setLongitude($longitude){$this->longitude = $longitude;}
    public function setUrl($url){$this->url = $url;}
}