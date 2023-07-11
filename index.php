<?php
declare(strict_types=1);

//echo "load files ";
require_once "src/ErrorHandler.php";
require_once "src/User/DatabaseUsers.php";
require_once "src/User/UserHandling.php";
require_once "src/Tickets/DatabaseTickets.php";
require_once "src/Tickets/Tickets.php";
require_once "src/Security.php";
require_once "src/config.php";

//echo "files loaded";
//Setzen der Selbsterstellten Fehlerhandhabungstools
set_error_handler("ErrorHandler::handleError");
set_exception_handler("ErrorHandler::handleException");

//Auseinandernehemen der URI damit mit bestimmten Teilen gleich weitergearbeitet werden kann
$parts = explode("/", $_SERVER["REQUEST_URI"]);
/*-------------------Erstellen aller Klassenobjeckte-------------*/
//echo "create Objekts";
try {
//echo "in try";
$Security = new Security();
echo "nach Security\n";
$dbUsers = new DatabaseUsers();
echo "nach dbUsers\n";
$UserHandling = new UserHandling($dbUsers, $Security);
echo "nach Userhandling\n";
$dbTickets = new DatabaseTickets();
echo "nach dbTickets\n";
$SitzHandling = new Tickets($dbTickets, $Security);
echo "nach tickets\n";
} 
catch (PDOException $e) {
    echo "initzialisation failed in index(): \n" . $e->getMessage();
}
echo "\nObjeckts created\n";
/*-------------------Bearbeiten der Anfrage-------------*/

switch ($parts[1]) {
    case "Register":
        echo "in registrieren\n";
        $UserHandling->createAcc();
        break;
    case "bestaetigung":
        echo "in bestatigen\n";
        $dbUsers->bestaetigen();
        break;
    case "Login":
        echo "in login\n";
        $UserHandling->checkLogin();       
        break;
    case "RequestEmail":
        echo "in request mail\n";
        $UserHandling->resetingEmail();
        break;
    case "Reseting":
        echo "in reseting\n";
        $UserHandling->resetPSW();
        break;
    case "Freischalten":
        echo "in freischalten\n";
        echo $UserHandling->FreischaltenTabelle();
        break;
    case "Freigeschaltet":
        echo "in freigeschaltet\n";
        $UserHandling->UserFreischalten();
        break;
    case "create":
        echo "in create\n";
        $dbUsers->createRegistrierung();
        break;
    default://Da keine bekannte aktion getetigt werden soll
        http_response_code(404);
        exit;
        break;
}
