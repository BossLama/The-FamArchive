<?php

namespace FamArchive;

use PDO;

class DatabaseConnector
{
    private $connection;
    private $host = 'localhost';
    private $user = 'root';
    private $password = '';
    private $database = 'famarchive';
    private $port = 3306;

    public function __construct()
    {
        $pdo = new PDO("mysql:host=$this->host;port=$this->port;dbname=$this->database", $this->user, $this->password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->connection = $pdo;
    }

    public function getConnection()
    {
        return $this->connection;
    }
}

?>