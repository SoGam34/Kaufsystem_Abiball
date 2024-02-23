<?php


$parts = explode("/", $_SERVER["REQUEST_URI"]);

if ((isset($_COOKIE["UId"])) || ($parts[1] == "Login")) 
{
    switch ($parts[1]) 
    {
        case "Login":
            
            require_once "src/User/DatabaseUsers.php";
            require_once "src/User/UserHandling.php";
            require_once "src/Security.php";
            require_once "src/config.php";

            $Security = new Security();

            $dsnW = "mysql:host=" . $Security->decrypt(SQL_SERVER_NAME_W) . ";dbname=" . $Security->decrypt(SQL_DB_NAME_W) . ";charset=utf8";
            $dbwrite = new PDO($dsnW, $Security->decrypt(SQL_DB_USER_W), $Security->decrypt(SQL_DB_PSW_W), [
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_STRINGIFY_FETCHES => false
            ]);


            $dsnR = "mysql:host=" . $Security->decrypt(SQL_SERVER_NAME_R) . ";dbname=" . $Security->decrypt(SQL_DB_NAME_R) . ";charset=utf8";
            $dbreade = new PDO($dsnR, $Security->decrypt(SQL_DB_USER_R), $Security->decrypt(SQL_DB_PSW_R), [
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_STRINGIFY_FETCHES => false
            ]);

            $dbUsers = new DatabaseUsers($Security, $dbwrite, $dbreade);

            $UserHandling = new UserHandling($dbUsers, $Security);

            $input = (array)json_decode(file_get_contents("php://input"),true);

            $email = $UserHandling->checkLogin($input);

            $session = session_start([
                'name' => "UId",
                'cookie_secure' => true,
                'cookie_httponly' => "false",
                'cookie_samesite' => "Strict"
            ]);

            if ($session == false) {
                echo json_encode(["Status" => "ERROR", "Message" => "Schwerwiegender interner Systemfehler, bitte kontaktieren Sie den Support mit dem Fehlercode 006."]);
                exit;
            }

            $UId = session_id();

            if (($UId == false)||($UId == "")) {
                echo json_encode(["Status" => "ERROR", "Message" => "Schwerwiegender interner Systemfehler, bitte kontaktieren Sie den Support mit dem Fehlercode 007."]);
                exit;
            }

            $dbUsers->addsession($UId, $email);

            echo json_encode(["Status" => "OK", "Erfolgreich" => true]);
            exit;
        
            break;

        case "Logout":
            require_once "src/User/DatabaseUsers.php";
            require_once "src/Security.php";
            require_once "src/config.php";

            $Security = new Security();

            $dsnW = "mysql:host=" . $Security->decrypt(SQL_SERVER_NAME_W) . ";dbname=" . $Security->decrypt(SQL_DB_NAME_W) . ";charset=utf8";
            $dbwrite = new PDO($dsnW, $Security->decrypt(SQL_DB_USER_W), $Security->decrypt(SQL_DB_PSW_W), [
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_STRINGIFY_FETCHES => false
            ]);


            $dsnR = "mysql:host=" . $Security->decrypt(SQL_SERVER_NAME_R) . ";dbname=" . $Security->decrypt(SQL_DB_NAME_R) . ";charset=utf8";
            $dbreade = new PDO($dsnR, $Security->decrypt(SQL_DB_USER_R), $Security->decrypt(SQL_DB_PSW_R), [
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_STRINGIFY_FETCHES => false
            ]);

            $dbUsers = new DatabaseUsers($Security, $dbwrite, $dbreade);

            $dbUsers->EndSession($_COOKIE["UId"]);

            $state = setcookie("UId");

            if ($state == false) {
                echo json_encode(["Status" => "ERROR", "Message" => "Schwerwiegender interner Systemfehler, bitte kontaktieren Sie den Support mit dem Fehlercode 020."]);
                exit;
            }

            echo json_encode(["Status" => "OK"]);
            exit;

            break;

        case  "KaufTicket":

            require_once "src/config.php";
            require_once "src/User/DatabaseUsers.php";
            require_once "src/Security.php";
            require_once "src/Tickets/DatabaseTickets.php";

            $Security = new Security();

            $dsnW = "mysql:host=" . $Security->decrypt(SQL_SERVER_NAME_W) . ";dbname=" . $Security->decrypt(SQL_DB_NAME_W) . ";charset=utf8";
            $dbwrite = new PDO($dsnW, $Security->decrypt(SQL_DB_USER_W), $Security->decrypt(SQL_DB_PSW_W), [
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_STRINGIFY_FETCHES => false
            ]);


            $dsnR = "mysql:host=" . $Security->decrypt(SQL_SERVER_NAME_R) . ";dbname=" . $Security->decrypt(SQL_DB_NAME_R) . ";charset=utf8";
            $dbreade = new PDO($dsnR, $Security->decrypt(SQL_DB_USER_R), $Security->decrypt(SQL_DB_PSW_R), [
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_STRINGIFY_FETCHES => false
            ]);

            $dbUsers = new DatabaseUsers($Security, $dbwrite, $dbreade);

            $teilnehmer = $dbUsers->verifysession($_COOKIE["UId"]);

            if ($teilnehmer == false) {
                echo json_encode(["Status" => "ERROR", "Message" => "Sie sind nicht angemeldet, daher wird diese anfrage nicht bearbeitet."]);
                exit;
            }

            $dbTickets = new DatabaseTickets($Security, $dbwrite, $dbreade);

            settype($begleitung, "string");

            settype($begleitung_mail, "string");

            $begleitung_mail = "<br>";

            for($i=0; $i<$_POST["amount"]; $i = $i + 1)
            {
                if(isset($_POST[$i]))
                {
                    $begleitung = $begleitung . $_POST[$i] . ", ";

                    $begleitung_mail =  $begleitung_mail . "- " . $_POST[$i] . "<br>";
                }
            }

            $dbTickets->setTicket($teilnehmer["Temail"], $_POST["amount"], $Security->encrypt($begleitung));


            $users = $dbUsers->getName($teilnehmer["Temail"]);

            $header = "MIME-Version: 1.0\r\n";
            $header .= "Content-type: text/html; charset=utf-8\r\n";
            $header .= "From: noreply@abi24bws.de";
    
            mail($teilnehmer["Temail"], "Kaufbestätigung der Abiball Tickets",
            "<html>
                <html lang'de'>
                <head>
	            <meta charset='UTF-8'>
	            <title>Kaufbestätigung</title>

	            <body bgcolor='FFFFFF'></body>

	            <body>    
                    Hallo " . $Security->decrypt($users["vorname"]) . " "  . $Security->decrypt($users["nachname"]) . ",<br />
                    <br />
                    vielen Dank für deine Bestellung.<br />
                    Wir haben deine Bestellung erfolgreich bearbeitet.<br />
                    <br />
                    Du hast gekauft: <b>" . $_POST["amount"] . "</b><br />
                    Du hast bezahlt: <b>" . $_POST["amount"] * 50 . "€</b><br />
                    Datum: <b>" . date("H:i:s d-m-Y") . "</b><br />
                    Transaktions ID: <b>" . $_COOKIE["UId"] . "</b><br />

                    <br>Die Einlass berechtigten Personen: 

                    " . $begleitung_mail ." <br>

                    <font color='red'><b>Jede</b>, auf dem Ticket genannte Person, muss<br />
                    für den Einlass einen gültigen Lichtbildausweis<br />
                    mitnehmen.<br /></font>
                <br />
                    Mit freundlichen Grüßen<br />
                <br />
                    Dein Abi24bwsTeam<br />
                    </font>
                </body>
                </html>",
            $header);
    

            echo json_encode(['status' => 1, 'msg' => 'Die Bezahlung war erfolgreich! In den nächsten Minuten erhalten Sie eine E-Mail mit dem Ticket. Wir freuen uns schon dich auf dem Abiball zu treffen.']);
            exit;

            break;
        case "Datenloeschen":
            require_once "src/config.php";
            require_once "src/User/DatabaseUsers.php";
            require_once "src/Security.php";

            $Security = new Security();

            $dsnW = "mysql:host=" . $Security->decrypt(SQL_SERVER_NAME_W) . ";dbname=" . $Security->decrypt(SQL_DB_NAME_W) . ";charset=utf8";
            $dbwrite = new PDO($dsnW, $Security->decrypt(SQL_DB_USER_W), $Security->decrypt(SQL_DB_PSW_W), [
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_STRINGIFY_FETCHES => false
            ]);


            $dsnR = "mysql:host=" . $Security->decrypt(SQL_SERVER_NAME_R) . ";dbname=" . $Security->decrypt(SQL_DB_NAME_R) . ";charset=utf8";
            $dbreade = new PDO($dsnR, $Security->decrypt(SQL_DB_USER_R), $Security->decrypt(SQL_DB_PSW_R), [
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_STRINGIFY_FETCHES => false
            ]);

            $dbUsers = new DatabaseUsers($Security, $dbwrite, $dbreade);

            $teilnehmer = $dbUsers->verifysession($_COOKIE["UId"]);

            if ($teilnehmer == false) {
                echo json_encode(["Status" => "ERROR", "Message" => "Sie sind nicht angemeldet, daher wird diese anfrage nicht bearbeitet."]);
                exit;
            }

            $dbUsers->deleteUser($teilnehmer["Temail"]);

            $dbUsers->EndSession($_COOKIE["UId"]);

            $state = setcookie("UId");

            if ($state == false) {
                echo json_encode(["Status" => "ERROR", "Message" => "Schwerwiegender interner Systemfehler, bitte kontaktieren Sie den Support mit dem Fehlercode 020."]);
                exit;
            }

            echo json_encode(["Status" => "OK", "Message" => "Die Löschung der Daten war erfolgreich"]);
            exit;
    }
} 

else 
{
    require_once "src/User/DatabaseUsers.php";
    require_once "src/User/UserHandling.php";
    require_once "src/Security.php";
    require_once "src/config.php";

    /*-------------------Erstellen aller Klassenobjeckte-------------*/

    $Security = new Security();

    $dsnW = "mysql:host=" . $Security->decrypt(SQL_SERVER_NAME_W) . ";dbname=" . $Security->decrypt(SQL_DB_NAME_W) . ";charset=utf8";
    $dbwrite = new PDO($dsnW, $Security->decrypt(SQL_DB_USER_W), $Security->decrypt(SQL_DB_PSW_W), [
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_STRINGIFY_FETCHES => false
    ]);


    $dsnR = "mysql:host=" . $Security->decrypt(SQL_SERVER_NAME_R) . ";dbname=" . $Security->decrypt(SQL_DB_NAME_R) . ";charset=utf8";
    $dbreade = new PDO($dsnR, $Security->decrypt(SQL_DB_USER_R), $Security->decrypt(SQL_DB_PSW_R), [
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_STRINGIFY_FETCHES => false
    ]);

    $dbUsers = new DatabaseUsers($Security, $dbwrite, $dbreade);

    $UserHandling = new UserHandling($dbUsers, $Security);

    /*-------------------Bearabeiten der Anfrage-------------*/

    $input = (array)json_decode(file_get_contents("php://input"),true);

    switch ($parts[1]) {
        case "Register":
            $UserHandling->createAcc($input);
            break;
        case "bestaetigung":
            $dbUsers->bestaetigen($input);
            break;
        case "RequestEmail":
            $UserHandling->resetingEmail($input);
            break;
        case "Reseting":
            $UserHandling->resetPSW($input);
            break;
        case "Freischalten":
            echo $UserHandling->FreischaltenTabelle();
            break;
        case "Freigeschaltet":
            $UserHandling->UserFreischalten($input);
            break;
        case "Ablehnen": 
            $UserHandling->Ablehnen($input);
            break;
        default:
            http_response_code(404);
            exit;
    }
}
