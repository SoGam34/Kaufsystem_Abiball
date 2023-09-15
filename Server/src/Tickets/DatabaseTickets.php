<?php

class DatabaseTickets
{
    public function __construct(private Security $sicher, private PDO $dbwrite, private PDO $dbreade)
    { 
    }

    public function getAlleStizplaze() : array {
        $stmt = $this->dbreade->prepare(
            "SELECT *
             FROM sitzplatze;");

        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $row;
    }
}
?>