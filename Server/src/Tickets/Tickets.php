<?php
class Tickets
{
    public function __construct(private DatabaseTickets $database, private Security $sicher)
    {
    }

    public function AlleTickets() {
        $data = $database->getAlleStizplaze();

        echo $data; 

        if(empty($data))
        {
            echo json_encode(["Status" => "ERROR", "Message" => "Schwerwiegender interner Systemfehler, bitte kontaktieren Sie den Support mit dem Fehlercode 111."]);
            exit;
        } 
        
        settype($Message, "string");

        foreach($data as &$value)
        {
            if($data["PersonID"]==0)
            {
                $Message+= '"' . $data["sitzplatz_id"] . '"' . "=>" . "frei" . ",";
            }
            else
            {
                $Message+= '"' . $data["sitzplatz_id"] . '"' . "=>" . "belegt" . ",";
            }
        }

        echo json_encode(["Status" => "OK", $Message]);
    }
}
?>