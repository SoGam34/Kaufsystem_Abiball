<?php

class DatabaseUsers
{
    public function __construct(private Security $sicher, private PDO $dbwrite, private PDO $dbreade)
    { 
    }

    public function insertRegister(string $vorname, string $nachname, string $klasse, string $email, string $passwort, string $salt) : int
    {
        $stmt = $this->dbwrite->prepare(
            "INSERT INTO registrierung (email, passwort, vorname, nachname, klasse)
             VALUES (:email,  :passwort, :vorname,  :nachname,  :klasse);"
        );

        $stmt->bindValue(":vorname",  $vorname, PDO::PARAM_STR);
        $stmt->bindValue(":nachname",  $nachname, PDO::PARAM_STR);
        $stmt->bindValue(":klasse",  $klasse, PDO::PARAM_STR);
        $stmt->bindValue(":email",  $email, PDO::PARAM_STR);
        $stmt->bindValue(":passwort",  $passwort, PDO::PARAM_STR);

        $stmt->execute();

        $id = $this->dbwrite->lastInsertId();

        $stmt = $this->dbwrite->prepare(
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
        $stmt = $this->dbwrite->prepare(
            "INSERT INTO teilnehmer (vorname, nachname, email, passwort, salt_id)
             VALUES ( :vorname,  :nachname,  :email,  :passwort, :salt_id);"
        );

        $stmt->bindValue(":vorname",  $vorname, PDO::PARAM_STR);
        $stmt->bindValue(":nachname",  $nachname, PDO::PARAM_STR);
        $stmt->bindValue(":email",  $email, PDO::PARAM_STR);
        $stmt->bindValue(":passwort",  $passwort, PDO::PARAM_STR);
        $stmt->bindValue(":salt_id",  $salt_id, PDO::PARAM_INT);

        $stmt->execute();

        return intval($this->dbwrite->lastInsertId());
    }

    public function bestaetigen()
    {
        $data = (array)json_decode(file_get_contents("php://input"), true);
        $id = $this->sicher->decrypt($data["id"]);

        if($this->sicher->check_id($id))
        {
            $stmt = $this->dbwrite->prepare(
                "UPDATE registrierung
                 SET bearbeitungsstatus = :zustand
                 WHERE registrierungs_id = :id;");

            $stmt->bindValue(":zustand",  true, PDO::PARAM_BOOL);
            $stmt->bindValue(":id",  $id, PDO::PARAM_INT);

            $stmt->execute();
            
            echo json_encode(["Status" => "OK"]); 
            exit;
        }

        else 
        {
            echo json_encode(["Status" => "ERROR", "Message" => "Ungultige Eingabe, bitte kontaktieren Sie den Support"]);
           exit;
        }
    }

    public function getFreischaltungsUebersicht()
    {
        $stmt = $this->dbreade->prepare(
            "SELECT vorname, nachname, klasse, email, registrierungs_id
             FROM registrierung
             WHERE bearbeitungsstatus = :zustand;");

        $stmt->bindValue(":zustand",  true, PDO::PARAM_BOOL);
        
        $stmt->execute();

        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $row;
    }

    public function getFreischalten(int $data)
    {
        $stmt = $this->dbreade->prepare(
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
            echo json_encode(["Status" => "ERROR", "Message"=>"001"]);
            exit;
        }

        else if($stmt->rowCount() == 0){
            echo json_encode(["Status" => "ERROR", "Message"=>"002"]);
            exit;
        }

        return false;
    }

    public function getSalt(int $id)
    {
        $stmt = $this->dbreade->prepare(
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
        $stmt = $this->dbreade->prepare(
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
        $stmt = $this->dbwrite->prepare(
            "UPDATE teilnehmer
             SET passwort = :passwort
             WHERE email = :email;");

        $stmt->bindValue(":email", $email, PDO::PARAM_STR);
        $stmt->bindValue(":passwort", $newPasswort, PDO::PARAM_STR);

        $stmt->execute();
    }

    public function deleteRegistrierung(string $email)
    {
        $stmt = $this->dbwrite->prepare(
            "DELETE FROM registrierung
             WHERE email = :email;");

        $stmt->bindValue(":email", $email, PDO::PARAM_STR);

        $stmt->execute();
    }

    public function addsession(string $ID, string $temail)
    {
        try{
        $stmt = $this->dbwrite->prepare(
            "INSERT INTO loginsession (Temail, session_id)
             VALUES (:Temail, :ID);");

        $stmt->bindValue(":ID", $ID, PDO::PARAM_STR);

        $stmt->bindValue(":Temail", $temail, PDO::PARAM_STR);

        $stmt->execute();
    } catch (PDOException $e) {
        echo json_encode(["Status" => "ERROR", "Message" =>  $e->getMessage()]);
        exit;
    }
    }

    public function verifysession(string $ID) 
    {
        try{
        $stmt = $this->dbreade->prepare(
            "SELECT Temail
             FROM loginsession
             WHERE session_id = :session_id;");

        $stmt->bindValue(":session_id", $ID, PDO::PARAM_STR);

        $stmt->execute();

        if($stmt->rowCount() == 1) 
        {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            return $row;
        } 
        else if($stmt->rowCount() > 1){
            echo json_encode(["Status" => "ERROR", "Message" => "011"]);
            exit;
        }

        else if($stmt->rowCount() == 0){
            return false;
        }
    } catch (PDOException $e) {
        echo json_encode(["Status" => "ERROR", "Message" =>  $e->getMessage()]);
        exit;
    }
    }

    public function EndSession(string $ID) 
    {
        try{
        $stmt = $this->dbwrite->prepare(
            "DELETE FROM loginsession
             WHERE session_id = :session_id;");

        $stmt->bindValue(":session_id", $ID, PDO::PARAM_STR);

        $stmt->execute();
    } catch (PDOException $e) {
        echo json_encode(["Status" => "ERROR", "Message" =>  $e->getMessage()]);
        exit;
    }
    }

    public function setAbstimmung($email, $location)
    {
        try{
            $stmt = $this->dbwrite->prepare(
                "UPDATE teilnehmer
                 SET abstimung = :abstimung
                 WHERE email = :email;");
    
            $stmt->bindValue(":email", $email, PDO::PARAM_STR);
            $stmt->bindValue(":abstimung", $location, PDO::PARAM_STR);
    
            $stmt->execute();
        } catch (PDOException $e) {
            echo json_encode(["Status" => "ERROR", "Message" =>  $e->getMessage()]);
            exit;
        }
    }

    public function exist_COOCKIE(string $email) {
        try{
            $stmt = $this->dbreade->prepare(
                "SELECT Uid
                 FROM loginsession
                 WHERE email = :email;");
    
            $stmt->bindValue(":email", $email, PDO::PARAM_STR);
    
            $stmt->execute();
    
            if($stmt->rowCount() == 1) 
            {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
                return $row;
            } 
            else if($stmt->rowCount() > 1){
                echo json_encode(["Status" => "ERROR", "Message" => "011"]);
                exit;
            }
    
            else if($stmt->rowCount() == 0){
                return false;
            }
        } catch (PDOException $e) {
            echo json_encode(["Status" => "ERROR", "Message" =>  $e->getMessage()]);
            exit;
        }
    }
}
