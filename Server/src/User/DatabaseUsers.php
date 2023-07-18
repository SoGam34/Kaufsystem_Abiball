<?php

class DatabaseUsers
{
    private PDO $conn;

    public function __construct(private Security $sicher)
    {
        $dsn = "mysql:host=" . SQL_SERVER_NAME . ";dbname=" . SQL_DB_NAME . ";charset=utf8";

        $this->conn = new PDO($dsn, SQL_DB_USER, SQL_DB_PSW, [
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_STRINGIFY_FETCHES => false
        ]);   
    }

    public function cleardb()
    {
        $stmt = $this->conn->prepare(
            "DELETE FROM registrierung;");
        $stmt->execute();

        $stmt = $this->conn->prepare(
            "DELETE FROM teilnehmer;");
        $stmt->execute();

        echo "erfolgreich";
    }

    public function insertRegister(string $vorname, string $nachname, string $klasse, string $email, string $passwort, string $salt) : int
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO registrierung (email, passwort, vorname, nachname, klasse)
             VALUES (:email,  :passwort, :vorname,  :nachname,  :klasse);"
        );

        $stmt->bindValue(":vorname",  $vorname, PDO::PARAM_STR);
        $stmt->bindValue(":nachname",  $nachname, PDO::PARAM_STR);
        $stmt->bindValue(":klasse",  $klasse, PDO::PARAM_STR);
        $stmt->bindValue(":email",  $email, PDO::PARAM_STR);
        $stmt->bindValue(":passwort",  $passwort, PDO::PARAM_STR);

        $stmt->execute();

        $id = $this->conn->lastInsertId();

        $stmt = $this->conn->prepare(
            "INSERT INTO salt (salt_id, salt)
             VALUES (:salt_id, :salt);"
        );
            
        $stmt->bindValue(":salt_id",  intval($id), PDO::PARAM_INT);
        $stmt->bindValue(":salt",  $salt, PDO::PARAM_STR);

        $stmt->execute();

        return intval($id);
    }

    public function insertTeilnehmer(string $vorname, string $nachname, string $email, string $passwort, int $salt_id) : int
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO teilnehmer (vorname, nachname, email, passwort, salt_id)
             VALUES ( :vorname,  :nachname,  :email,  :passwort, :salt_id);"
        );

        $stmt->bindValue(":vorname",  $vorname, PDO::PARAM_STR);
        $stmt->bindValue(":nachname",  $nachname, PDO::PARAM_STR);
        $stmt->bindValue(":email",  $email, PDO::PARAM_STR);
        $stmt->bindValue(":passwort",  $passwort, PDO::PARAM_STR);
        $stmt->bindValue(":salt_id",  $salt_id, PDO::PARAM_INT);

        $stmt->execute();

        return intval($this->conn->lastInsertId());
    }

    public function bestaetigen()
    {
        $data = (array)json_decode(file_get_contents("php://input"), true);

        if($this->sicher->check_id($data["id"]))
        {
            $stmt = $this->conn->prepare(
                "UPDATE registrierung
                 SET bearbeitungsstatus = :zustand
                 WHERE registrierungs_id = :id;");

            $stmt->bindValue(":zustand",  true, PDO::PARAM_BOOL);
            $stmt->bindValue(":id",  $data["id"], PDO::PARAM_INT);

            $stmt->execute();
            
            echo json_encode(["Status" => "OK"]);
        }

        else 
        {
           echo json_encode([["Status" => "ERROR"], ["Massage"=>"Ungultige Eingabe, bitte kontaktieren Sie den Support"]]);
        }
    }
    public function createRegistrierung()
    {
/*
        $sql = "DROP TABLE registrierung;
            CREATE TABLE registrierung(
            registrierungs_id int AUTO_INCREMENT PRIMARY KEY,
            email varchar (255) UNIQUE, 
            passwort varchar(255) NOT NULL,
            vorname varchar (255) NOT NULL, 
            nachname varchar (255) NOT NULL, 
            klasse varchar (5) NOT NULL, 
            bearbeitungsstatus boolean default false, 
            datum timestamp default CURRENT_TIMESTAMP());

            ";CREATE TABLE salt(
            salt_id int PRIMARY KEY, 
            salt varchar(5) UNIQUE
            );
        
            CREATE TABLE teilnehmer(
            teilnehmer_id int AUTO_INCREMENT PRIMARY KEY,
            email varchar(255) UNIQUE, 
            passwort varchar(255) NOT NULL,
            vorname varchar(255) NOT NULL, 
            nachname varchar(255) NOT NULL,
            salt_id int NOT NULL,
            FOREIGN KEY (salt_id) REFERENCES salt(salt_id));

            CREATE TABLE sitzplatze(
            sitzplatz_id int PRIMARY KEY,
            PersonID int DEFAULT NULL,
            FOREIGN KEY (PersonID) REFERENCES teilnehmer(teilnehmer_id));";
            

        try {
            $this->conn->exec($sql);
        } catch (PDOException $e) {
            echo "Connection failed in createRegistrierung(): \n" . $e->getMessage();
        } */
    }

    public function getFreischaltungsUebersicht()
    {
        $stmt = $this->conn->prepare(
            "SELECT vorname, nachname, klasse, email, registrierungs_id
             FROM registrierung
             WHERE bearbeitungsstatus = :zustand;");

        $stmt->bindValue(":zustand",  true, PDO::PARAM_BOOL);
        
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $row;
    }

    public function getFreischalten(int $data)
    {
        $stmt = $this->conn->prepare(
            "SELECT vorname, nachname, email, passwort
             FROM registrierung
             WHERE registrierungs_id = :registrierungs_id;");

        $stmt->bindValue(":registrierungs_id",  $data, PDO::PARAM_INT);

        $stmt->execute();

        if($stmt->rowCount() == 1) 
        {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            return $row;
        } 
        else if($stmt->rowCount() > 1){
            echo json_encode([["Status" => "ERROR"].["Message"=>"001"]]);
        }

        else if($stmt->rowCount() == 0){
            echo json_encode([["Status" => "ERROR"].["Message"=>"002"]]);
        }

        return false;
    }

    public function getSalt(int $id)
    {
        $stmt = $this->conn->prepare(
            "SELECT salt
             FROM salt
             WHERE salt_id = :id;");

        $stmt->bindValue(":id",  $id, PDO::PARAM_INT);
        
        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data;
    }
    public function getUser(string $email)
    {
        $stmt = $this->conn->prepare(
            "SELECT passwort, salt_id
             FROM teilnehmer
             WHERE email = :email;");

        $stmt->bindValue(":email",  $email, PDO::PARAM_STR);
       
        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data;
    }

    public function ResetPasswort(string $email, string $newPasswort): void
    {
        $stmt = $this->conn->prepare(
            "UPDATE teilnehmer
             SET passwort = :passwort
             WHERE email = :email;");

        $stmt->bindValue(":email", $email, PDO::PARAM_STR);
        $stmt->bindValue(":passwort", $newPasswort, PDO::PARAM_STR);

        $stmt->execute();
    }

    public function deleteRegistrierung(string $email)
    {
        $stmt = $this->conn->prepare(
            "DELETE FROM registrierung
             WHERE email = :email;");

        $stmt->bindValue(":email", $email, PDO::PARAM_STR);

        $stmt->execute();
    }
}
