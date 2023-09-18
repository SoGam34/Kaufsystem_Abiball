<?php

class DatabaseTickets
{
    private Security $sicher, private PDO $dbwrite, private PDO $dbreade;

    public function __construct(Security $msicher, PDO $mdbwrite, PDO $mdbreade)
    { 
        $this->sicher =$msicher;
        $this->dbwrite=$mdbreade;
        $this->dbreade=$mdbwrite;
    }

    public function getAlleSitzplatze() : array {
        $stmt = $this->dbreade->prepare(
            "SELECT *
             FROM sitzplatze;");

        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $row;
    }

    public function insertSitzplatze() : void {
       try{

        for( $i=0; $i<25; $i++)
        {
            
            $stmt = $this->dbwrite->prepare(
                "INSERT INTO sitzplatze (PersonID)
                 VALUES ( :PersonID);"
            );

            $stmt->bindValue(":PersonID",  0);

            $stmt->execute();
        }
    } catch (PDOException $e) {
        echo "Error creating entry: \n" . $e->getMessage();
    }
    }
}