<?php
declare(strict_types=1);

echo "load files ";
require_once "src/ErrorHandler.php";
require_once "src/User/DatabaseUsers.php";
require_once "src/User/UserHandling.php";
require_once "src/Tickets/DatabaseTickets.php";
require_once "src/Tickets/Tickets.php";
require_once "src/Security.php";
require_once "src/config.php";

echo "files loaded";
//Setzen der Selbsterstellten Fehlerhandhabungstools
set_error_handler("ErrorHandler::handleError");
set_exception_handler("ErrorHandler::handleException");

//Auseinandernehemen der URI damit mit bestimmten Teilen gleich weitergearbeitet werden kann
$parts = explode("/", $_SERVER["REQUEST_URI"]);
/*-------------------Erstellen aller Klassenobjeckte-------------*/
echo "create Objekts";
try {
echo "in try";
$Security = new Security();
echo "nach Security";
$dbUsers = new DatabaseUsers();
echo "nach dbUsers";
$UserHandling = new UserHandling($dbUsers, $Security);
echo "nach Userhandling ";
$dbTickets = new DatabaseTickets();
echo "nach dbTickets";
$SitzHandling = new Tickets($dbTickets, $Security);
echo "nach tickets ";
} 
catch (PDOException $e) {
    echo "initzialisation failed in index(): \n" . $e->getMessage();
}
echo "Objeckts created";
/*-------------------Bearbeiten der Anfrage-------------*/

switch ($parts[1]) {
    case "Register":
        echo "in registrieren ";
        $UserHandling->createAcc();
        break;
    case "bestaetigung":
        echo "in bestatigen";
        $dbUsers->bestaetigen();
        break;
    case "Login":
        echo "in login";
        $UserHandling->checkLogin();       
        break;
    case "RequestEmail":
        echo "in request mail";
        $UserHandling->resetingEmail();
        break;
    case "Reseting":
        echo "in reseting";
        $UserHandling->resetPSW();
        break;
    case "Freischalten":
        echo "in freischalten ";
        echo $UserHandling->FreischaltenTabelle();
        break;
    case "Freigeschaltet":
        echo "in freigeschaltet";
        $UserHandling->UserFreischalten();
        break;
    case "create":
        echo "in create";
        $dbUsers->createRegistrierung();
        break;
    default://Da keine bekannte aktion getetigt werden soll
        http_response_code(404);
        exit;
        break;
}
