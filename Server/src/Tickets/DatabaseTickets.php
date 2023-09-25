<?php

class DatabaseTickets
{
    public function __construct(private Security $sicher, private PDO $dbwrite, private PDO $dbreade)
    { 
    }

    public function getAlleSitzplatze() : array {
        $stmt = $this->dbreade->prepare(
            "SELECT *
             FROM sitzplatze;");

        $stmt->execute();

        $row = $stmt->fetchAll();
        
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