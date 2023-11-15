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
        
        $this->sicher->PSW_is_safe($data["passwort"]);
        
        $this->sicher->EMail_is_safe($data["email"]);
        
        //Erstellen aller benötigten Variablen für das generieren des Salts
        $salt = "";
        $abc = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";

        //Salt generieren
        for($i=0;$i<5;$i++)
        {
            $salt .= $abc[rand(0, strlen($abc)-1)];
        }
        
        $id=$this->database->insertRegister($this->sicher->encrypt($data["vorname"]), $this->sicher->encrypt($data["nachname"]), $this->sicher->encrypt($data["klasse"]), $data["email"], password_hash($this->sicher->decrypt(Pfeffer) . $data["passwort"] . $salt, PASSWORD_DEFAULT), $salt);
        
        $name = $data["vorname"] . " "  . $data["nachname"];

        $header = "MIME-Version: 1.0\r\n";
        $header .= "Content-type: text/html; charset=utf-8\r\n";
        $header .= "From: noreply@abi24bws.de";

        mail($data["email"], "Verifizierung deiner Email-Adresse bei Abi24bws.de",
                
        "<html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <title>Mail Adresse Bestätigen</title>
            <meta name='description' content='Kurzbeschreibung'>
            <link href='design.css' rel='stylesheet'>
        
            <body bgcolor='FFFFFF'></body>
            
            
            <body> 
                    <font color='black'>
                    <font size='5'><B>Verifizierung deiner Email-Adresse</B></font><br />
        <br /> 
        <br />        
                    Guten Tag {$name},<br />
        <br /> 
        <br />        
                    bitte bestätige <a href='https://abi24bws.de/Bestaetigung.html?id={$this->sicher->encrypt($id)}'>hier</a> deine Email-Adresse.<br />
        <br />
                    Nachdem du deine Email bestätigt hast, bitten<br />
                    wir dich um ein wenig Geduld, bis du von unserem<br />
                    Admin-Team freigeschaltet wirst.<br />
                    Sobald dies erfolgt ist, erhalst du Zugriff auf<br />
                    alle Dienste.<br />
        <br />
                    Wenn du dich nicht bei Abi24bws registriert hast,<br />
                    kannst du diese Email ignorieren und wir<br />
                    entschuldigen uns für die Störung.<br />
        <br />
                    Mit freundlichen Grüßen<br />
        <br />
                    Euer Abi24bws Team<br />
                    </font>
            </body>
            </html>",
        $header);
        //Bestätigen das alles erfolgreich war 
        echo json_encode(["Status" => "OK"]);
        exit;
    }


    public function UserFreischalten() 
    {
        $data = (array)json_decode(file_get_contents("php://input"), true);

        if(!$this->sicher->check_id($data["registrierungs_id"]))
        {
            echo (["Status" => "ERROR", "Message"=>"Ungultige Eingabe, bitte kontaktieren Sie den Supprt"]);
            exit;
        }
       
        $users = $this->database->getFreischalten($data["registrierungs_id"]);

        if($users==false)
        {
            echo (["Status"=> "ERROR", "Message"=>"Schwerwiegender interner System fehler, bitte kontaktieren Sie den Support mit dem Fehlercode 005."]);
            exit;
        }

        $this->database->insertTeilnehmer($users["vorname"], $users["nachname"], $users["email"], $users["passwort"], $data["registrierungs_id"]);
        $this->database->deleteRegistrierung($users["email"]);

        $header = "MIME-Version: 1.0\r\n";
        $header .= "Content-type: text/html; charset=utf-8\r\n";
        $header .= "From: noreply@abi24bws.de";

        mail($users["email"], "Du wurdest vom abi24bws.de Team freigeschaltet!",
        "<html>
         <html lang='en'>
         <head>
	     <meta charset='UTF-8'>
	     <title>Freigeschaltet</title>
	     <meta name='description' content='Kurzbeschreibung'>
	     <link href='design.css' rel='stylesheet'>
                     
         <body bgcolor='FFFFFF'></body>
                     
                     
         <body>
            <font color='black'>
            <font size='5'><B>Du wurdest vom Abi24bws Team freigeschaltet</B></font><br />
            <br />  
            <br />  
            Es freut uns dir mitteilen zu können, dass du nun vollen <br />
            Zugriff auf unsere Abi-Webseite hast.<br />
            <br />  
            Das bedeutet für dich, dass du bis zu vier Tickets an <br />
            einem frei wählbaren Ort kaufen kannst.<br /> 
            Zusätzlich kannst du Bilder und Videos vom Abiball <br />
            hoch- bzw. runterladen.<br />
            <br />
            Falls du noch Ideen, Verbesserungsvorschläge oder <br />
            Probleme hast, sag uns bitte bescheid, damit wir uns <br />
            schnellstmöglich darum kümmern können.<br />
            <br />
            <br />
            Mit freundlichen Grüßen<br />
            <br />
            Dein Abi24bws Team<br />
            </font>
         </body>
         </html>",
        $header);

        echo json_encode(["Status" => "OK"]);
        exit;
    }

    public function FreischaltenTabelle()
    {        
        if(isset($_POST["Admin"]))
        {
            if($_POST["Admin"]==$this->sicher->decrypt(AdminID) && $_POST["AdminPSW"]==$this->sicher->decrypt(AdminPSW))
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
                        <td>" . $this->sicher->decrypt($value["vorname"] ). "</td>
                        <td>" . $this->sicher->decrypt($value["nachname"]) . "</td>
                        <td>" . $this->sicher->decrypt($value["klasse"]) . "</td>
                        <td>" . $value["email"] . "</td>
                        <td>" . '<input type="button" value="Identitaet Bestaetigen" onclick="Identitaet_bestaetigt(' . $value["registrierungs_id"] . ')"></td>
                        </tr>';
                    }

                    $tabelle .= "</table> </body>
                    </html>";
                    unset($value); 
                
                   // echo json_encode(["Status"=>"OK", "Message"=>$tabelle]);exit;
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

        else
        {
            return
                "<!DOCTYPE html>
            <html lang='de'>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <title>Document</title>
            </head>
            <body>
            <form action='/Freischalten' method = 'POST'>
                <label for='fname'>First name:</label><br>
                <input type='text' id='fname' name='Admin'><br>
                <label for='lname'>Last name:</label><br>
                <input type='password' id='lname' name='AdminPSW'<br><br>
                <input type='submit' value='Submit'>
            </form> ";
        }
    }
         

    public function resetingEmail(): void 
    {
        //Ziehn aller benötigten daten 
        $data = (array)json_decode(file_get_contents("php://input"),true);

        $this->sicher->EMail_is_safe($data["email"]);

        //$key = $this->sicher->encrypt($data["email"]);
        $header = "MIME-Version: 1.0\r\n";
        $header .= "Content-type: text/html; charset=utf-8\r\n";
        $header .= "From: noreply@abi24bws.de";
        mail($data["email"], "Zurücksetzen ihres Passwords bei Abi24bws.de",
        
        "<!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <title>Passwort Zurücksetzen</title>
            <meta name='description' content='Kurzbeschreibung'>
            <link href='design.css' rel='stylesheet'>

            <body bgcolor='FFFFFF'></body>
            <body>
            <font color='black'>
            <font size='5'><B>Passwort zurücksetzen</B></font><br />
            <br />
            <br />
            Wenn du dein Passwort zurücksetzen möchtest, <br />
            kannst du dies <a href='https://abi24bws.de/passwortzuruck.html?{$this->sicher->encrypt($data['email'])}'>hier</a> tun.<br />
            <br />
            Nachdem du dein neues Passwort eingegeben hast, <br />
            kannst du dich wie gewohnt anmelden.<br />
            <br />
            <br />
            Mit freundlichen Grüßen<br />
            <br />
            Dein Abi24bws Team<br />
            </font>
            </body>
        </html>",
        $header);

        //Bestätigen das alles erfolgreich war 
        echo json_encode(["Status" => "OK"]);
        exit;
    }

    public function resetPSW()
    {   
        //Ziehn aller benötigten daten 
        $data = (array)json_decode(file_get_contents("php://input"),true);
        
        $this->sicher->PSW_is_safe($data["passwort"]);
        
        $email = $this->sicher->decrypt($data["email"]);

        $this->sicher->EMail_is_safe($email);
       
        $user = $this->database->getUser($email);

        if($user=="")
        {
            echo json_encode(["Status" => "OK", "Erfolgreich"=>false]);
            exit;
        }
        
        $salt = $this->database->getSalt($user["salt_id"]);
        
        //Das eigentliche zurücksetzen
        $this->database->ResetPasswort($email, password_hash("AcFgP" . $data["passwort"] . $salt["salt"], PASSWORD_DEFAULT));
        
        //Bestätigen das alles erfolgreich war 
        echo json_encode(["Status" => "OK", "Erfolgreich"=>true]);
        exit;
    }

    public function checkLogin()
    {
        //Ziehn aller benötigten daten 
        $data = (array)json_decode(file_get_contents("php://input"),true);
        
        $this->sicher->PSW_is_safe($data["passwort"]);
        
        $this->sicher->EMail_is_safe($data["email"]);
        
        $user = $this->database->getUser($data["email"]);

        if($user!="")
        {
            header("Access-Control-Allow-Origin: https://abi24bws.de");
            header("Access-Control-Allow-Methods: POST, GET");

            echo json_encode(["Status" => "OK", "Erfolgreich"=>false]);
            return false;
        }

        
        $salt=$this->database->getSalt($user["salt_id"]);
    
        //Überprüfen des passwords 
        $passVerfy = password_verify($this->sicher->decrypt(Pfeffer) . $data["passwort"] . $salt["salt"],  $user["passwort"]);

        //Ausgeben des Überprüfungsergebnisses 
        if (!$passVerfy)
        {
            header("Access-Control-Allow-Origin: https://abi24bws.de");
            header("Access-Control-Allow-Methods: POST, GET");
            echo json_encode(["Status" => "OK", "Erfolgreich"=>false]);
            return false;
        } 

        else if($passVerfy)
        {
            return $data["email"];
        }
            
        header("Access-Control-Allow-Origin: https://abi24bws.de");
        header("Access-Control-Allow-Methods: POST, GET");
            
        echo json_encode(["Status" => "ERROR", "Message" => "004"]);
        return false;
    }
}
