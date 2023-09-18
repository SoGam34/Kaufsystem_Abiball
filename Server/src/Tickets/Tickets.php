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
        
        settype($Message, "string");

        foreach ($data as $k => $v) {
            $Message = $Message . $Message . '"' . $k . '"' . "=>" . $v . ",";
        }

        echo $Message;


        echo "\n\n\n";

        foreach ($data as $k => $v) {
            echo "\$data[$k] => $v.\n";
        }

        echo "\n\n\n";

        echo implode(" ", $data);
       /* for( $i = 0; $i<25; $i++)
        {
            if($data["PersonID"]==0)
            {
                $Message = $Message . '"' . $data["sitzplatz_id"] . '"' . "=>" . "frei" . ",";
            }
            else
            {
                $Message = $Message . '"' . $data["sitzplatz_id"] . '"' . "=>" . "belegt" . ",";
            }
        }*/

        //echo json_encode(["Status" => "OK", $Message]);
    }
}