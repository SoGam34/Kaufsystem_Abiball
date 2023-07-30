<?php

$parts = explode("/", $_SERVER["REQUEST_URI"]);

if($parts[1]=="Login")
{
    
require_once "src/User/DatabaseUsers.php";
require_once "src/User/UserHandling.php";
require_once "src/Security.php";
require_once "src/config.php";


$Security = new Security();

$dbUsers = new DatabaseUsers($Security);

$UserHandling = new UserHandling($dbUsers, $Security);

    $state=$UserHandling->checkLogin();
    
    if($state!=false)
    {
        $session=session_start([
            'name'=>"UId",
            'cookie_secure'=>true,
            'cookie_httponly'=>"true", 
            'cookie_samesite'=>"Strict"
        ]);

        header("Access-Control-Allow-Origin: https://abi24bws.de");
        header("Access-Control-Allow-Methods: POST, GET");
        
        if($session==true)
        {
            $UId = session_id();

            if($UId==false)
            {
                echo json_encode(["Status" => "ERROR", "Message" => "Schwerwiegender interner Systemfehler, bitte kontaktieren Sie den Support mit dem Fehlercode 007."]);
                exit;
            }

            else if($UId=="")
            {
                echo json_encode(["Status" => "ERROR", "Message" => "Schwerwiegender interner Systemfehler, bitte kontaktieren Sie den Support mit dem Fehlercode 008."]);
                exit;
            }

            else
            {
                $dbUsers->addsession($UId, $state);
                echo json_encode(["Status" => "OK", "Erfolgreich"=>true]);
            }
        }
        
        else
        {
            echo json_encode(["Status" => "ERROR", "Message" => "Schwerwiegender interner Systemfehler, bitte kontaktieren Sie den Support mit dem Fehlercode 006."]);
        }
    }
    exit;
}

else if($parts[1]=="Logout"){
require_once "src/User/DatabaseUsers.php";
require_once "src/Security.php";
require_once "src/config.php";

$Security = new Security();

$dbUsers = new DatabaseUsers($Security);

    $dbUsers->EndSession($_COOKIE["UId"]);
    $state=setcookie("UId", time()-3600);
    if($state==true)
    {
    echo json_encode(["Status" => "OK"]);
    }
    else{
        echo json_encode(["Status" => "ERROR", "Message" => "Schwerwiegender interner Systemfehler, bitte kontaktieren Sie den Support mit dem Fehlercode 020."]);
        exit;
    }
}

else 
{
require_once "src/ErrorHandler.php";
require_once "src/User/DatabaseUsers.php";
require_once "src/User/UserHandling.php";
require_once "src/Tickets/DatabaseTickets.php";
require_once "src/Tickets/Tickets.php";
require_once "src/Security.php";
require_once "src/config.php";


//Auseinandernehemen der URI damit mit bestimmten Teilen gleich weitergearbeitet werden kann
$parts = explode("/", $_SERVER["REQUEST_URI"]);
/*-------------------Erstellen aller Klassenobjeckte-------------*/

$Security = new Security();

$dbUsers = new DatabaseUsers($Security);

$UserHandling = new UserHandling($dbUsers, $Security);

$dbTickets = new DatabaseTickets();

$SitzHandling = new Tickets($dbTickets, $Security);

/*-------------------Bearabeiten der Anfrage-------------*/

header("Access-Control-Allow-Origin: https://abi24bws.de");
header("Access-Control-Allow-Methods: POST, GET");

//Setzen der Selbsterstellten Fehlerhandhabungstools
set_error_handler("ErrorHandler::handleError");
set_exception_handler("ErrorHandler::handleException");

switch ($parts[1]) {
    case "Register":
        $UserHandling->createAcc();
        break;
    case "bestaetigung":
        $dbUsers->bestaetigen();
        break;
    case "RequestEmail":
        $UserHandling->resetingEmail();
        break;
    case "Reseting":
        $UserHandling->resetPSW();
        break;
    case "Freischalten":
        echo $UserHandling->FreischaltenTabelle();
        break;
    case "Freigeschaltet":
        $UserHandling->UserFreischalten();
        break;
    case "create":
        $dbUsers->createRegistrierung();
        break;
    case "clear":
        //$dbUsers->cleardb();
        try {
            $dbUsers->deleteRegistrierung("widawski.nico@gmail.com");
        } catch (PDOException $e) {
            echo "Error deleting entry: \n" . $e->getMessage();
        }
        echo "succes";
        break;

    case  "Tickets":
        $teilnehmer=$dbUsers->verifysession($_COOKIE["UId"]);

        if($teilnehmer==false)
        {
            echo json_encode(["Status" => "ERROR", "Message" => "Sie sind entweder nicht angemeldet oder es liegt ein interner Systemfehler vor bei dem wir Sie bitten den Support, mit dem Fehlercode 009, zu kontaktieren."]);
            exit;
        }
    
        break;
    default:
    //Da keine bekannte aktion getetigt werden soll
        http_response_code(404);
        exit;
        break;
}
}
