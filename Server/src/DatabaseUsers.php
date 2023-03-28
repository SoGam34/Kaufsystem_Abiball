<?php

class Database
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

    public function insertSalt(string $id, string $salt)
    {
        $sql = "INSERT INTO salt (salt_id, salt)
        VALUES ({$id}, {$salt});";

        $this->conn->exec($sql);
        //$stmt = $this->conn->prepare($sql);

        //$stmt->bindValue(":email", $Email, PDO::PARAM_STR);
        //$stmt->bindValue(":passwort", $UserPassword, PDO::PARAM_STR);
        //$stmt->bindParam(":tabelle", $tabelle, PDO::PARAM_STR);

        //$stmt->execute();

        return $this->conn->lastInsertId();
    }

    public function insert(string $vorname, string $nachname, string $klasse, string $email, string $passwort, int $salt_id)
    {
        $sql = "INSERT INTO salt (vorname, nachname, klasse, email, passwort, salt_id)
        VALUES ( {$vorname},  {$nachname},  {$klasse},  {$email},  {$passwort},  {$salt_id});";

        $this->conn->exec($sql);

        return $this->conn->lastInsertId();
    }

    public function bestaetigen()
    {
        $data = (array)json_decode(file_get_contents("php://input"),true);

        $sql = "UPDATE registrierung
                SET bearbeitungsstatus = 1
                WHERE registrierungs_id = {$data["id"]};";
        
        try {
            $this->conn->exec($sql);
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }
    public function createRegistrierung()
    {
        $sql="DROP TABLE registrierung;
            DROP TABLE teilnehmer;

            CREATE TABLE registrierung(
            registrierungs_id int AUTO_INCREMENT PRIMARY KEY,
            email varchar(40) UNIQUE, 
            passwort varchar(40) UNIQUE,
            vorname varchar(40) NOT NULL, 
            nachname varchar(40) NOT NULL, 
            klasse varchar(5) NOT NULL, 
            bearbeitungsstatus BOOL DEFAULT 0, 
            datum DEFAULT DATE);

            CREATE TABELE salt(
            salt_id int PRIMARY KEY, 
            salt varchar(5) UNIQUE
            );

            CREATE TABLE teilnehmer(
            teilnehmer_id int AUTO_INCREMENT PRIMARY KEY,
            email varchar(40) UNIQUE, 
            passwort varchar(40) UNIQUE
            vorname varchar(40) NOT NULL, 
            nachname varchar(40) NOT NULL, 
            klasse varchar(5),
            salt_id int NOT NULL,
            FOREIGN KEY (salt_id) REFERENCES salt(salt_id));

            CREATE TABLE sitzplatze(
            sitzplatz_id int PRIMARY KEY,
            PersonID int DEFAULT 0,
            FOREIGN KEY (PersonID) REFERENCES teilnehmer(teilnehmer_id));";

            try {
                $this->conn->exec($sql);
            } catch (PDOException $e) {
                echo "Connection failed: " . $e->getMessage();
            }
        
    }

    public function getUser(string $email): array | false
    {
        $sql = "SELECT *
                FROM teilnehmer
                WHERE email = :email;";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":email",  $email, PDO::PARAM_STR);

        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data !== false) {
        }

        return $data;
    }

    public function ResetPasswort($email, string $newPasswort): void
    {
        $sql = "UPDATE teilnehmer
                SET passwort = :passwort
                WHERE email = :email;";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":email", $email, PDO::PARAM_STR);
        $stmt->bindValue(":passwort", $newPasswort, PDO::PARAM_STR);

        $stmt->execute();
    }

    public function delete($email): void
    {
        $sql = "DELETE FROM registrierung
                WHERE email = :email;";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":email", $email, PDO::PARAM_STR);

        $stmt->execute();
    }
}
