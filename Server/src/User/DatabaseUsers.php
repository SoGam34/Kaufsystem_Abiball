<?php

class DatabaseUsers
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = $this->getConnection();
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

    public function insertRegister(string $vorname, string $nachname, string $klasse, string $email, string $passwort, string $salt)
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO registrierung (vorname, nachname, klasse, email, passwort)
             VALUES ( :vorname,  :nachname,  :klasse,  :email,  :passwort);"
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

        return $id;
    }

    public function insertTeilnehmer(string $vorname, string $nachname, string $email, string $passwort, string $salt_id)
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

        return $this->conn->lastInsertId();
    }

    public function bestaetigen()
    {
        $data = (array)json_decode(file_get_contents("php://input"), true);

        $stmt = $this->conn->prepare(
            "UPDATE registrierung
             SET bearbeitungsstatus = true
             WHERE registrierungs_id = :id;");

        $stmt->bindValue(":id",  $data["id"], PDO::PARAM_INT);

        $stmt->execute();

        echo json_encode(["Status" => "OK"]);
    }
    public function createRegistrierung()
    {
        /*
        $sql = "CREATE TABLE registrierung(
            registrierungs_id int AUTO_INCREMENT PRIMARY KEY,
            email varchar (40) UNIQUE, 
            passwort varchar(255) NOT NULL,
            vorname varchar (40) NOT NULL, 
            nachname varchar (40) NOT NULL, 
            klasse varchar (5) NOT NULL, 
            bearbeitungsstatus boolean default false, 
            datum timestamp default  CURRENT_TIMESTAMP);"

            "CREATE TABLE salt(
            salt_id int PRIMARY KEY, 
            salt varchar(5) UNIQUE
            );

            CREATE TABLE teilnehmer(
            teilnehmer_id int AUTO_INCREMENT PRIMARY KEY,
            email varchar(40) UNIQUE, 
            passwort varchar(255) NOT NULL,
            vorname varchar(40) NOT NULL, 
            nachname varchar(40) NOT NULL, 
            klasse varchar(5),
            salt_id int NOT NULL,
            FOREIGN KEY (salt_id) REFERENCES salt(salt_id));

            CREATE TABLE sitzplatze(
            sitzplatz_id int PRIMARY KEY,
            PersonID int DEFAULT 0,
            FOREIGN KEY (PersonID) REFERENCES teilnehmer(teilnehmer_id));";

            */
            $sql=
            "DROP TABEL teilnehmer;
             CREATE TABEL teilnehmer(
             teilnehmer_id int AUTO_INCREMENT PRIMARY KEY,
             email varchar(255) UNIQUE, 
             passwort varchar(255) NOT NULL,
             vorname varchar(255) NOT NULL, 
             nachname varchar(255) NOT NULL, 
             salt_id int NOT NULL,
             FOREIGN KEY (salt_id) REFERENCES salt(salt_id));
              ";
        try {
            $this->conn->exec($sql);
        } catch (PDOException $e) {
            echo "Connection failed in createRegistrierung(): \n" . $e->getMessage();
        }//*/




    }

    public function FreischaltungsUebersicht()
    {
        $stmt = $this->conn->prepare(
            "SELECT vorname, nachname, klasse, email, registrierungs_id
             FROM registrierung
             WHERE bearbeitungsstatus = :zustand;");

        $stmt->bindValue(":zustand",  true, PDO::PARAM_BOOL);
        
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $tabelle = '
          <table>
            <tr>
                <th>Vorname</th>
                <th>Nachname</th>
                <th>Klasse</th>
                <th>email</th>
                <th>Bestätigen</th>
            </tr>';
                // output data of each row
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $tabelle +=
                    "<tr>
                        <td>" . $this->sicher->decrypt($row["vorname"] ). "</td>
                        <td>" . $this->sicher->decrypt($row["nachname"] ). "</td>
                        <td>" . $this->sicher->decrypt($row["klasse"] ). "</td>
                        <td>" . $this->sicher->decrypt($row["email"] ). "</td>
                        <td>" . '<input type="button" value="Identitaet Bestaetigen" onclick="Identitaet_bestaetigt(' . $row["registrierungs_id"] . ')"></td>
                    </tr>';
                }
                $tabelle += "</table>";
                return $tabelle;
            } else {
                return " 0 rows affected";
            }
    }

    public function getFreischalten($data)
    {
        $stmt = $this->conn->prepare(
            "SELECT vorname, nachname, email, passwort
             FROM registrierung
             WHERE registrierungs_id = :registrierungs_id;");

        $stmt->bindValue(":registrierungs_id",  $data["registrierungs_id"], PDO::PARAM_STR);

        $stmt->execute();

        if($stmt->rowCount() == 1) 
        {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            }

            return $row;
        } 
        else if($stmt->rowCount() > 1){
            echo json_encode([["Status" => "ERROR"].["CODE"=>"001"]]);
        }

        else if($stmt->rowCount() == 0){
            echo json_encode([["Status" => "ERROR"].["CODE"=>"002"]]);
        }

        return false;
    }

    public function getSalt($id)
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

    public function ResetPasswort($email, string $newPasswort): void
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
            "DELETE FROM teilnehmer
             WHERE email = :email;");

        $stmt->bindValue(":email", password_hash($email, PASSWORD_DEFAULT), PDO::PARAM_STR);

        $stmt->execute();
    }
}
