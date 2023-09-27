<?php
class Tickets
{
    public function __construct(private DatabaseTickets $database, private Security $sicher)
    {
    }

    public function AlleTickets() {
        $data = $this->database->getAlleSitzplatze();

        if(empty($data))
        {
            echo json_encode(["Status" => "ERROR", "Message" => "Schwerwiegender interner Systemfehler, bitte kontaktieren Sie den Support mit dem Fehlercode 111."]);
            exit;
        } 

       

        $a; 
        $b;
        settype($Message, "string");

        foreach ($data as list($a, $b)) {
            if($b==1)
            {
                $Message = $Message . '"' . $a . '":"frei",';
            }
            else
            {
                $Message = $Message . '"' . $a . '":"belegt",';
            }
        }

        echo json_encode(["Status" => "OK","Message" => $Message]);
    }

    public function Ticketgekauft() {
        
    }
}