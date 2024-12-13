<?php
session_start();
error_reporting(E_ALL);
date_default_timezone_set('Asia/Bangkok');

class Database
{
    private $host = "localhost";
    private $dbname = "db_boxing";
    private $username = "root";
    private $password = "";
    private $conn = null;

    public function connect()
    {
        try {
            $this->conn = new PDO(
                'mysql:host=' . $this->host . '; 
                                dbname=' . $this->dbname . '; 
                                charset=utf8',
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            echo "ไม่สามารถเชื่อมต่อฐานข้อมูลได้: " . $exception->getMessage();
            exit();
        }
        return $this->conn;
    }
}
