<?php
class UserHandling
{
    public function __construct(private DatabaseUsers $database)
    {
    }

    public function createAcc()
    {
        //Ziehn aller benötigten daten 
        $data = (array)json_decode(file_get_contents("php://input"),true);
        //Erstellen aller benötigten Variablen für das generieren des Salts
        $salt = "";
        $abc = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
        //Salt generieren
        for($i=0;$i<5;$i++)
        {
            $salt .= $abc[rand(0, 52)];
        }
        //Anlegen eines neuen Eintrags und damit eines neuen accaunts
        $id=$this->database->insertRegister($data["vorname"], $data["nachname"], $data["klasse"], $data["email"], password_hash("AcFgP" . $data["passwort"] . $salt, PASSWORD_DEFAULT), $salt);
        //Generieren und senden der Bestätigungs email
        mail($data["email"], "Verifizierung ihrer Email-Adresse bei Abi24bws.de",
        
        "Sehr geehrte Abiturientinnen und Abiturienten, \n\n
        bitte bestaetigen Sie ihre Email-Adresse indem Sie auf den folgenden Link klicken: \n\nhttps://abi24bws.de/Bestaetigung.html?id={$id}\n
        Nachdem sie ihre Email bestaetigt haben, bitten wir Sie, um ein wenig Geduld bis Sie von unserem Admin-Team freigeschaltet sind. Sobald dies erfolgt ist, erhalten Sie Zugriff auf alle Dienste.
        \n\nWenn Sie sich nicht bei Abi24bws registriert haben, koennen Sie diese Email ignorieren und wir entschuldigen uns fuer die Stoerung\n\n\n
        Mit freundlichen Grueßen\n 
        Ihr Abi24bws Team",
        
        "From: johannes@abi24bws.de");
        //Bestätigen das alles erfolgreich war 
        echo json_encode(["Status" => "OK"]);
    }

    public function resetPSW()
    {   
        //Ziehn aller benötigten daten 
        $data = (array)json_decode(file_get_contents("php://input"),true);
        $user = $this->database->getUser($data["email"]);
        $salt = $this->database->getSalt($user["salt_id"]);
        
        //Das eigentliche zurücksetzen
        $this->database->ResetPasswort($data["email"], password_hash("AcFgP" . $data["passwort"] . $salt["salt"], PASSWORD_DEFAULT));
        
        //Bestätigen das alles erfolgreich war 
        echo json_encode(["Status" => "OK"]);
    }

    public function checkLogin()
    {
        //Ziehn aller benötigten daten 
        $data = (array)json_decode(file_get_contents("php://input"),true);
        $user = $this->database->getUser($data["email"]);
        $salt=$this->database->getSalt($user["salt_id"]);
        
        //Überprüfen des passwords 
        $passVerfy = password_verify("AcFgP" . $data["passwort"] . $salt["salt"],  $user["passwort"]);
        
        //Ausgeben des Überprüfungsergebnisses
        if (!$passVerfy) {
            echo json_encode([["Status" => "OK"].["Erfolgreich"=>false]]);
        } else {
            echo json_encode([["Status" => "OK"].["Erfolgreich"=>true]]);
        }
    }
}