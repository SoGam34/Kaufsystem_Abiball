<?php

class DatabaseTickets
{
    public function __construct(private Security $sicher, private PDO $dbwrite, private PDO $dbreade)
    { 
    }
    
    public function setTicket(string $email, int $anzahl) {
        try{
            $stmt = $this->dbwrite->prepare(
                "UPDATE teilnehmer
                 SET AnzahlTickets = :AnzahlTickets
                 WHERE email = :email;");
    
            $stmt->bindValue(":email", $email, PDO::PARAM_STR);
            $stmt->bindValue(":AnzahlTickets", $anzahl, PDO::PARAM_INT);
    
            $stmt->execute();
        } catch (PDOException $e) {
            echo json_encode(["Status" => "ERROR", "Message" =>  $e->getMessage()]);
            exit;
        }
    }
}