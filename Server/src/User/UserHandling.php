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
        
        if($this->sicher->PSW_is_safe($data["passwort"]))
        {
            if($this->sicher->EMail_is_safe($data["email"]))
            {
                //Erstellen aller benötigten Variablen für das generieren des Salts
                $salt = "";
                $abc = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";

                //Salt generieren
                for($i=0;$i<5;$i++)
                {
                    $salt .= $abc[rand(0, strlen($abc)-1)];
                }
                //Anlegen eines neuen Eintrags und damit eines neuen accaunts
                $id=$this->database->insertRegister(/*$this->sicher->encrypt(*/$data["vorname"]/*)*/, /*$this->sicher->encrypt(*/$data["nachname"], /*$this->sicher->encrypt(*/$data["klasse"], $data["email"], password_hash("AcFgP" . $data["passwort"] . $salt, PASSWORD_DEFAULT), $salt);
                //echo "versende Email";
                //Generieren und senden der Bestätigungs email
                mail($data["email"], "Verifizierung ihrer Email-Adresse bei Abi24bws.de",
                
                "Sehr geehrte Abiturientinnen und Abiturienten, \n\n
                bitte bestaetigen Sie ihre Email-Adresse indem Sie auf den folgenden Link klicken: \n\nhttps://abi24bws.de/Bestaetigung.html?id={$id}\n
                Nachdem sie ihre Email bestaetigt haben, bitten wir Sie, um ein wenig Geduld bis Sie von unserem Admin-Team freigeschaltet sind. Sobald dies erfolgt ist, erhalten Sie Zugriff auf alle Dienste.
                \n\nWenn Sie sich nicht bei Abi24bws registriert haben, koennen Sie diese Email ignorieren und wir entschuldigen uns fuer die Stoerung\n\n\n
                Mit freundlichen Grueßen\n 
                Ihr Abi24bws Team",
                
                
                "From: noreply@abi24bws.de");
                //Bestätigen das alles erfolgreich war 
                echo json_encode(["Status" => "OK"]);
            }
        }
    }


    public function UserFreischalten() 
    {
        $data = (array)json_decode(file_get_contents("php://input"), true);

        if($this->sicher->check_id($data["registrierungs_id"]))
        {
            $users = $this->database->getFreischalten($data["registrierungs_id"]);

            if($users!=false)
            {
                $this->database->insertTeilnehmer($users["vorname"], $users["nachname"], $users["email"], $users["passwort"], $data["registrierungs_id"]);
                $this->database->deleteRegistrierung($users["email"]);

                mail(/*$this->sicher->decrypt(*/$users["email"], "Sie wurden von ihrem abi24bws.de Team freigeschaltet!",
                "Sehr geehrte Abiturientinne und Abituriente, \n\n
                Es freut uns ihnen mitteilen zu können das Sie nun vollen Zugriff auf unsere Abiseite haben.
                Das bedeutet für Sie, das Sie bis zu vier Tickets an einem frei wählbaren Ort kaufen können und Sie Bilder und Viedeos vom Abiball hoch und Runterladen können. 
                Falls Sie Ideen, Verbesserungsvorschlage oder Probleme haben sagen Sie uns bitte Bescheid, wir versuchen diese so schnell wie möglich umzusetzen.\n\n\n
                Mit freundlichen Grueßen\n 
                Ihr Abi24bws Team",

                "From: noreply@abi24bws.de");

                echo json_encode(["Status" => "OK"]);
            }

            else
            {
                echo json_encode(["Status"=> "ERROR", "Message"=>"Schwerwiegender interner System fehler, bitte kontaktieren Sie den Support"]);
            }
        }

        else 
        {
           echo json_encode(["Status" => "ERROR", "Message"=>"Ungultige Eingabe, bitte kontaktieren Sie den Supprt"]);
        }
    }

    public function FreischaltenTabelle()
    {
        $input = (array)json_decode(file_get_contents("php://input"), true);

            if($input["Admin"]==AdminID && $input["AdminPSW"]==AdminPSW)
            {
                $data=$this->database->getFreischaltungsUebersicht();

                //echo $data;
                if($data!="")
                {
                
                    $tabelle = "
                    <!DOCTYPE html>
                    <html lang='de'>
                    <head>
                        <meta charset='UTF-8'>
                        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                        <title>Document</title>
                        <script type='text/javascript' src='Browser/Johannes.js'></script>
                    </head>
                    <body>

                    <table>
                      <tr>
                          <th>Vorname</th>
                          <th>Nachname</th>
                          <th>Klasse</th>
                          <th>email</th>
                          <th>Bestätigen</th>
                      </tr>";

                    foreach ($data as $value) 
                    {
                        $tabelle .=
                            "<tr>
                                <td>" . /*$this->sicher->decrypt(*/$value["vorname"] . "</td>
                                <td>" . /*$this->sicher->decrypt(*/$value["nachname"] . "</td>
                                <td>" . /*$this->sicher->decrypt(*/$value["klasse"] . "</td>
                                <td>" . $data["email"] . "</td>
                        <td>" . '<input type="button" value="Identitaet Bestaetigen" onclick="Identitaet_bestaetigt(' . $data["registrierungs_id"] . ')"></td>
                    </tr>';
                    }
                
                    $tabelle .= "</table> </body>
                    </html>";
                    unset($value); 
                
                    echo json_encode(["Status"=>"OK", "Message"=>$tabelle]);exit;
                    return $tabelle;
                }
                else
                {
                    return "0 rows affected";
                }
                   
            }

            else
            {
                echo "Wrong Input";
            }
    }
         

    public function resetingEmail(): void 
    {
        //Ziehn aller benötigten daten 
        $data = (array)json_decode(file_get_contents("php://input"),true);

        if($this->sicher->EMail_is_safe($data["email"]))
        {
            //$key = $this->sicher->encrypt($data["email"]);

            mail($data["email"], "Zurücksetzen ihres Passwords bei Abi24bws.de",
            
            "Sehr geehrte Abiturientinne und Abituriente,
            \n\nindem Sie auf den folgenden Link klicken können Sie ihr Passwort zurück setzen:
            \n\nhttps://abi24bws.de/passwortzuruck.html?id={$data['email']}
            \nNachdem sie ihr neues Passwort eingegeben haben können Sie sich wie gewont anmelden. 
            \n\nWenn Sie nicht bei Abi24bws ihr Passwort zurücksetzen wollen, koennen Sie diese Email ignorieren und wir entschuldigen uns fuer die Stoerung\n\n\n
            Mit freundlichen Grueßen\n 
            Ihr Abi24bws Team",

            "From: noreply@abi24bws.de");

            //Bestätigen das alles erfolgreich war 
            echo json_encode(["Status" => "OK"]);
        }
    }

    public function resetPSW()
    {   
        //Ziehn aller benötigten daten 
        $data = (array)json_decode(file_get_contents("php://input"),true);
        
        if($this->sicher->PSW_is_safe($data["passwort"]))
        {
            $email = /*$this->sicher->decrypt(*/$data["email"];

            if($this->sicher->EMail_is_safe($email))
            {
                $user = $this->database->getUser($email);

                if($user!="")
                {
                    $salt = $this->database->getSalt($user["salt_id"]);
                
                    //Das eigentliche zurücksetzen
                    $this->database->ResetPasswort($email, password_hash("AcFgP" . $data["passwort"] . $salt["salt"], PASSWORD_DEFAULT));
                
                    //Bestätigen das alles erfolgreich war 
                    echo json_encode(["Status" => "OK", "Erfolgreich"=>true]);
                }

                else 
                {
                   echo json_encode(["Status" => "OK", "Erfolgreich"=>false]);
                }
            }
        }
    }

    public function checkLogin()
    {
        //Ziehn aller benötigten daten 
        $data = (array)json_decode(file_get_contents("php://input"),true);
        
        if($this->sicher->PSW_is_safe($data["passwort"]))
        {
            if($this->sicher->EMail_is_safe($data["email"]))
            {
                $user = $this->database->getUser($data["email"]);

                if($user!="")
                {
                    $salt=$this->database->getSalt($user["salt_id"]);
                
                    //Überprüfen des passwords 
                    $passVerfy = password_verify("AcFgP" . $data["passwort"] . $salt["salt"],  $user["passwort"]);

                    //Ausgeben des Überprüfungsergebnisses
                    if (!$passVerfy)
                    {
                        echo json_encode(["Status" => "OK", "Erfolgreich"=>false]);
                    } 
                    else if($passVerfy)
                    {
                        session_create_id();
                        echo json_encode(["Status" => "OK", "Erfolgreich"=>true]);
                    }
                    else
                    {
                        echo json_encode(["Status" => "ERROR", "Message" => "004"]);
                    }
                }
                else 
                {
                   echo json_encode(["Status" => "OK", "Erfolgreich"=>false]);
                }
            }
        }  
    }
}
