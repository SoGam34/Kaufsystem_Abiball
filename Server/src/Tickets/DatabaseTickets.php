<?php

class DatabaseTickets
{
    private PDO $conn;

    public function __construct()
    {
        try {
            $this->conn = $this->getConnection();
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }

    public function __destruct()
    {
    }

    public function getConnection(): PDO
    {
        $dsn = "mysql:host=rdbms.strato.de;dbname=dbs10190475;charset=utf8";

        return new PDO($dsn, 'dbu2898798', '&%wz65DQ_Ht/D!g', [
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_STRINGIFY_FETCHES => false
        ]);
    }
}
?>