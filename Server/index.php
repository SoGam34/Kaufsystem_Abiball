<?php

require_once "src/ErrorHandler.php";
require_once "src/User/DatabaseUsers.php";
require_once "src/User/UserHandling.php";
require_once "src/Tickets/DatabaseTickets.php";
require_once "src/Tickets/Tickets.php";
require_once "src/Security.php";
require_once "src/config.php";

//Setzen der Selbsterstellten Fehlerhandhabungstools
set_error_handler("ErrorHandler::handleError");
set_exception_handler("ErrorHandler::handleException");

//Auseinandernehemen der URI damit mit bestimmten Teilen gleich weitergearbeitet werden kann
$parts = explode("/", $_SERVER["REQUEST_URI"]);
/*-------------------Erstellen aller Klassenobjeckte-------------*/

$Security = new Security();

$dbUsers = new DatabaseUsers();

$UserHandling = new UserHandling($dbUsers, $Security);

$dbTickets = new DatabaseTickets();

$SitzHandling = new Tickets($dbTickets, $Security);

/*-------------------Bearabeiten der Anfrage-------------*/

switch ($parts[1]) {
    case "Register":
        $UserHandling->createAcc();
        break;
    case "bestaetigung":
        $dbUsers->bestaetigen();
        break;
    case "Login":
        $UserHandling->checkLogin();       
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
    default:
    //Da keine bekannte aktion getetigt werden soll
        http_response_code(404);
        exit;
        break;
}
