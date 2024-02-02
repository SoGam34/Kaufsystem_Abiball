<?php

class UserHandling
{
    public function __construct(private DatabaseUsers $database, private Security $sicher)
    {
    }

    public function createAcc($data)
    {        
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

	$options = [ 
    	    'cost' => 15, 
	]; 
    
	    
        $id=$this->database->insertRegister($this->sicher->encrypt($data["vorname"]), $this->sicher->encrypt($data["nachname"]), $this->sicher->encrypt($data["klasse"]), $data["email"], password_hash($this->sicher->decrypt(Pfeffer) . $data["passwort"] . $salt, PASSWORD_BCRYPT, $options), $salt);
        

        $header = "MIME-Version: 1.0\r\n";
        $header .= "Content-type: text/html; charset=utf-8\r\n";
        $header .= "From: noreply@abi24bws.de";

        mail($data["email"], "Verifizierung deiner Email-Adresse",
                
        "<html>
        <html lang='de'>
        <head>
            <meta charset='UTF-8'>
            <title>Mail Adresse Bestätigen</title>
            <meta name='description' content='Kurzbeschreibung'>
            <link href='design.css' rel='stylesheet'>
        
            <body bgcolor='FFFFFF'></body>
            
            
            <body> 
                    <font color='black'>
                    Hallo " . $data["vorname"] . " "  . $data["nachname"] . ",<br />
					<br />       
                    bitte bestätige <a href='https://abi24bws.de/Bestaetigung.html?id=" . $this->sicher->encrypt($id) . "'>hier</a> deine Email-Adresse.<br />
					<br />
                    Nachdem du deine Email bestätigt hast, bitten<br />
                    wir dich um ein wenig Geduld, bis du von unserem<br />
                    Admin-Team freigeschaltet wirst.<br />
					<br />
                    Sobald wir dich freigeschaltet haben, erhälst du<br />
					eine Bestätigungs-Mail und vollen Zugriff auf<br />
                    alle Dienste.<br />
					<br />
                    Wenn du dich nicht bei Abi24bws registriert hast,<br />
                    kannst du diese Email ignorieren und wir<br />
                    entschuldigen uns für die Störung.<br />
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


    public function UserFreischalten($data) 
    {
        if($this->sicher->check_id($data["registrierungs_id"]) == false)
        {
            echo json_encode(["Status" => "ERROR", "Message"=>"Ungultige Eingabe, bitte kontaktieren Sie den Supprt"]);
            exit;
        }
       
        $users = $this->database->getFreischalten($data["registrierungs_id"]);

        if($users==false)
        {
            echo json_encode(["Status"=> "ERROR", "Message"=>"Schwerwiegender interner System fehler, bitte kontaktieren Sie den Support mit dem Fehlercode 005."]);
            exit;
        }

        $this->database->insertTeilnehmer($users["vorname"], $users["nachname"], $users["email"], $users["passwort"], $data["registrierungs_id"]);
        $this->database->deleteRegistrierung($users["email"]);

        $header = "MIME-Version: 1.0\r\n";
        $header .= "Content-type: text/html; charset=utf-8\r\n";
        $header .= "From: noreply@abi24bws.de";

        mail($users["email"], "Du wurdest vom Abi24bws Team freigeschaltet!",
        "<html>
         <html lang='de'>
         <head>
	     <meta charset='UTF-8'>
	     <title>Freigeschaltet</title>
	     <meta name='description' content='Kurzbeschreibung'>
	     <link href='design.css' rel='stylesheet'>
                     
         <body bgcolor='FFFFFF'></body>
                     
                     
         <body>
            <font color='black'>
			Hallo " . $this->sicher->decrypt($users["vorname"]) . " "  . $this->sicher->decrypt($users["nachname"]) . ",<br />  
			<br />  
            es freut uns dir mitteilen zu können, dass du nun vollen <br />
            Zugriff auf unsere Abi-Webseite hast.<br />
            <br />  
            Das bedeutet für dich, dass du bis zu 5 Tickets an <br />
            einem frei wählbaren Ort kaufen kannst.<br /> 
            Zusätzlich kannst du Bilder und Videos vom Abiball <br />
            hoch- bzw. runterladen.<br />
            <br />
            Falls du noch Ideen, Verbesserungsvorschläge oder <br />
            Probleme hast, sag uns bitte bescheid, damit wir uns <br />
            schnellstmöglich darum kümmern können.<br />
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
        if(!isset($_POST["Admin"]))
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

        if($_POST["Admin"]!=$this->sicher->decrypt(AdminID) && $_POST["AdminPSW"]!=$this->sicher->decrypt(AdminPSW))
        {
            return "Wrong Input";
        }
            
        $data=$this->database->getFreischaltungsUebersicht();

        //echo $data;
        if($data=="")
        {
            return "0 rows affected";
        }
        
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
            <td>' . '<input type="button" value="Identitaet Ablehnen" onclick="Identitaet_Ablehnen(' . $value["email"] . ')"></td>
            </tr>';
        }

        $tabelle .= "</table></body></html>";
            
        unset($value); 
            
        return $tabelle;
    }

    public function Ablehnen($input)
    {
        $users = $this->database->getReName($input["registrierungs_id"]);

        if($users == "")
        {
            exit;
        }

        $header = "MIME-Version: 1.0\r\n";
        $header .= "Content-type: text/html; charset=utf-8\r\n";
        $header .= "From: noreply@abi24bws.de";

        mail($users["registrierungs_id"], "Du wurdest abgelehnt!",
        "<html>
         <html lang'en'>
        <head>
	    <meta charset='UTF-8'>
	    <title>MailAbgelehnt</title>
	    <meta name='description' content='Kurzbeschreibung'>
	    <link href='design.css' rel='sytlesheet'>
        
	    <body bgcolor='FFFFFF'></body>

	    		Hallo " . $this->sicher->decrypt($users["vorname"]) . " "  . $this->sicher->decrypt($users["nachname"]) . ",<br />
	    		<br />
	    		es tut uns leid dir mitteilen zu müssen, dass deine <br />
	    		Registrierung abgelehnt wurde.<br />
	    		<br />
	    		Gründe dafür können sein:<br />
	    		<li>nicht authentischer Name</li>
	    		<li>dein Name taucht nicht in den Kurslisten auf</li><br />
	    	<br />
	    		Falls du das Gefühl hast, zu Unrecht abgelehnt<br />
	    		worden zu sein, melde dich unter folgender<br /> 
	    		Mail-Adresse bei uns: support@abi24bws.de<br />
	    	<br />
	    		Mit freundlichen Grüßen<br />
	    	<br />
	    		Dein Abi24bwsTeam<br />
	    		</font>
	    </body>
	    </html>",
        $header);

        $this->database->deleteRegistrierung($input["registrierungs_id"]);

        echo json_encode(["Status" => "OK"]);
        exit;
    }
         

    public function resetingEmail($data) {

        $this->sicher->EMail_is_safe($data["email"]);

        $name = $this->database->getName($data["email"]);

        //$key = $this->sicher->encrypt($data["email"]);
        $header = "MIME-Version: 1.0\r\n";
        $header .= "Content-type: text/html; charset=utf-8\r\n";
        $header .= "From: noreply@abi24bws.de";
        mail($data["email"], "Passwort zurücksetzen",
        
        "<!DOCTYPE html>
        <html lang='de'>
        <head>
            <meta charset='UTF-8'>
            <title>Passwort Zurücksetzen</title>
            <meta name='description' content='Kurzbeschreibung'>
            <link href='design.css' rel='stylesheet'>

            <body bgcolor='FFFFFF'></body>
            <body>
            <font color='black'>
			Hallo " . $this->sicher->decrypt($name["vorname"]) . " " . $this->sicher->decrypt($name["nachname"]) . ",<br />
			<br />
            wenn du dein Passwort zurücksetzen möchtest, <br />
            kannst du dies <a href='https://abi24bws.de/passwortzuruck.html?" . $this->sicher->encrypt($data["email"]) . "'>hier</a> tun.<br />
            <br />
            Nachdem du dein neues Passwort eingegeben hast, <br />
            kannst du dich wieder wie gewohnt anmelden.<br />
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

    public function resetPSW($data)
    {   
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
        $this->database->ResetPasswort($email, password_hash($this->sicher->decrypt(Pfeffer) . $data["passwort"] . $salt["salt"], PASSWORD_DEFAULT));
        
        //Bestätigen das alles erfolgreich war 
        echo json_encode(["Status" => "OK", "Erfolgreich"=>true]);
        exit;
    }

    public function checkLogin($data)
    {
        $this->sicher->PSW_is_safe($data["passwort"]);

        $this->sicher->EMail_is_safe($data["email"]);
        
        $user = $this->database->getUser($data["email"]);

        if($user=="")
        {
            echo json_encode(["Status" => "OK", "Erfolgreich"=>false]);
            exit;
        }
        
        $salt = $this->database->getSalt($user["salt_id"]);
    
        //Überprüfen des passwords 
        $passVerfy = password_verify($this->sicher->decrypt(Pfeffer) . $data["passwort"] . $salt["salt"],  $user["passwort"]);

        //Ausgeben des Überprüfungsergebnisses 
        if ($passVerfy==false)
        {
            echo json_encode(["Status" => "OK", "Erfolgreich"=>false]);
            exit;
        } 

        else if($passVerfy == true)
        {
            $name = $this->database->getName($data["email"]);
            $id = $this->database->getID($data["email"]);
            
            $header = "MIME-Version: 1.0\r\n";
            $header .= "Content-type: text/html; charset=utf-8\r\n";
            $header .= "From: noreply@abi24bws.de";
            mail($data["email"], "Ein neues Gerät hat sich angemeldet",
            
            "<html>
            <html lang'en'>
            <head>
	        <meta charset='UTF-8'>
	        <title>Neuer Gerätelogin</title>
	        <meta name='description' content='Kurzbeschreibung'>
	        <link href='design.css' rel='sytlesheet'>

	        <body bgcolor='FFFFFF'></body>


	        <body>
	        		<font color='black'>
	        		Hallo " . $this->sicher->decrypt($name["vorname"]) . " " . $this->sicher->decrypt($name["nachname"]) . ",<br />
	        		<br />
	        		ein neues Gerät hat sich bei deinem<br />
	        		Abi24bws Konto angemeldet.
	        	<br />	
	        		Wenn du dich nicht eingeloggt hast,<br />
	        		kannst du dein Passwort <a href='https://abi24bws.de/passwortzuruckemail.html?id=" . $this->sicher->encrypt($id["teilnehmer_id"]) . "' >hier zurücksetzen</a>.<br />
	        		Dabei werden alle aktuell angemeldeten<br />
	        		Geräte automatisch ausgeloggt.<br />	
	        	<br />
	        		Mit freundlichen Grüßen<br />
	        	<br />
	        		Dein Abi24bwsTeam<br />
	        		</font>
	        	</body>
	        	</html>",
            $header);

        return $data["email"];
        }

        echo json_encode(["Status" => "ERROR", "Message" => "004"]);
        exit;
    }
}
