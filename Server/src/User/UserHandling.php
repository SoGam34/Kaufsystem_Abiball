<?php
class UserHandling
{
    public function __construct(private DatabaseUsers $database, private Security $sicher)
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
        $id=$this->database->insertRegister($this->sicher->encrypt($data["vorname"]), $this->sicher->encrypt($data["nachname"]), $this->sicher->encrypt($data["klasse"]), $data["email"], password_hash("AcFgP" . $data["passwort"] . $salt, PASSWORD_DEFAULT), $salt);
        echo "versende Email";
        //Generieren und senden der Bestätigungs email
        mail($data["email"], "Verifizierung ihrer Email-Adresse bei Abi24bws.de",
        
        "Sehr geehrte Abiturientinne und Abituriente, \n\n
        bitte bestaetigen Sie ihre Email-Adresse indem Sie auf den folgenden Link klicken: \n\nhttps://abi24bws.de/Bestaetigung.html?id={$id}\n
        Nachdem sie ihre Email bestaetigt haben, bitten wir Sie, um ein wenig Geduld bis Sie von unserem Admin-Team freigeschaltet sind. Sobald dies erfolgt ist, erhalten Sie Zugriff auf alle Dienste.
        \n\nWenn Sie sich nicht bei Abi24bws registriert haben, koennen Sie diese Email ignorieren und wir entschuldigen uns fuer die Stoerung\n\n\n
        Mit freundlichen Grueßen\n 
        Ihr Abi24bws Team",

        
        "From: noreplay@abi24bws.de");
        //Bestätigen das alles erfolgreich war 
        echo json_encode(["Status" => "OK"]);
    }


    public function UserFreischalten() 
    {
        $data = (array)json_decode(file_get_contents("php://input"), true);
        $users = $this->database->getFreischalten($data["registrierungs_id"]);

        if($users!=false)
        {
            $this->database->insertTeilnehmer($users["vorname"], $users["nachname"], $users["email"], $users["passwort"], $data["registrierungs_id"]);
            $this->database->deleteRegistrierung($users["email"]);

            mail($this->sicher->decrypt($users["email"]), "Sie wurden von ihrem abi24bws.de Team freigeschaltet!",
            "Sehr geehrte Abiturientinne und Abituriente, \n\n
            Es freut uns ihnen mitteilen zu können das Sie nun vollen Zugriff auf unsere Abiseite haben.
            Das bedeutet für Sie, das Sie bis zu vier Tickets an einem frei wählbaren Ort kaufen können und Sie Bilder und Viedeos vom Abiball hoch und Runterladen können. 
            Falls Sie Ideen, Verbesserungsvorschlage oder Probleme haben sagen Sie uns bitte Bescheid, wir versuchen diese so schnell wie möglich umzusetzen.\n\n\n
            Mit freundlichen Grueßen\n 
            Ihr Abi24bws Team",

            "From: noreplay@abi24bws.de");

            echo json_encode(["Status" => "OK"]);
        }
    }

    public function FreischaltenTabelle()
    {
        $data=$this->database->getFreischaltungsUebersicht();

        if($data!=false)
        {

            $tabelle = "
            <table>
              <tr>
                  <th>Vorname</th>
                  <th>Nachname</th>
                  <th>Klasse</th>
                  <th>email</th>
                  <th>Bestätigen</th>
              </tr>";
            
            foreach ($data as &$value) 
            {
                $tabelle .=
                    "<tr>
                        <td>" . $this->sicher->decrypt($data["vorname"] ). "</td>
                        <td>" . $this->sicher->decrypt($data["nachname"] ). "</td>
                        <td>" . $this->sicher->decrypt($data["klasse"] ). "</td>
                        <td>" . $data["email"] . "</td>
                        <td>" . '<input type="button" value="Identitaet Bestaetigen" onclick="Identitaet_bestaetigt(' . $data["registrierungs_id"] . ')"></td>
                    </tr>';
            }

            $tabelle += "</table>";
            unset($value); 
        }
        else
        {
            $tabelle = "0 rows affected";
        }
        return $tabelle;
    }

    public function resetingEmail(): void 
    {
        //Ziehn aller benötigten daten 
        $data = (array)json_decode(file_get_contents("php://input"),true);

        $key = $this->sicher->encrypt($data["email"]);

        mail($data["email"], "Zurücksetzen ihres Passwords bei Abi24bws.de",
        
        "Sehr geehrte Abiturientinne und Abituriente, \n\n
        indem Sie auf den folgenden Link klicken können Sie ihr Passwort zurück setzen: \n\nhttps://abi24bws.de/Bestaetigung.html?id={ $key }\n
        Nachdem sie ihr neues Passwort eingegeben haben können Sie sich wie gewont anmelden. 
        \n\nWenn Sie nicht bei Abi24bws ihr Passwort zurücksetzen wollen, koennen Sie diese Email ignorieren und wir entschuldigen uns fuer die Stoerung\n\n\n
        Mit freundlichen Grueßen\n 
        Ihr Abi24bws Team",

        "From: noreplay@abi24bws.de");
    }

    public function resetPSW()
    {   
        //Ziehn aller benötigten daten 
        $data = (array)json_decode(file_get_contents("php://input"),true);
        
        $user = $this->database->getUser($data["email"]);

        if($user!="")
        {
            $salt = $this->database->getSalt($user["salt_id"]);
        
            //Das eigentliche zurücksetzen
            $this->database->ResetPasswort($data["email"], password_hash("AcFgP" . $data["passwort"] . $salt["salt"], PASSWORD_DEFAULT));
        
            //Bestätigen das alles erfolgreich war 
            echo json_encode(["Status" => "OK"]);
        }
    }

    public function checkLogin()
    {
        //Ziehn aller benötigten daten 
        $data = (array)json_decode(file_get_contents("php://input"),true);
        
        $user = $this->database->getUser($data["email"]);
        
        if($user!="")
        {
            $salt=$this->database->getSalt($user["salt_id"]);
           
            //Überprüfen des passwords 
            $passVerfy = password_verify("AcFgP" . $data["passwort"] . $salt["salt"],  $user["passwort"]);
               
            //Ausgeben des Überprüfungsergebnisses
            if (!$passVerfy)
            {
                echo json_encode(["Erfolgreich"=>false]);
            } 
            else if($passVerfy)
            {
               echo json_encode(["Erfolgreich"=>true]);
            }
            else
            {
                echo json_encode(["Code" => "004"]);
            }
        }
        else 
        {
           echo json_encode(["Erfolgreich"=>false]);
        }
    }
}
