<?php

declare(strict_types=1);

echo "load files ";
include "/src/ErrorHandler.php";
include "/src/User/DatabaseUsers.php";
include "/src/User/UserHandling.php";
include "/src/Tickets/DatabaseTickets.php";
include "/src/Tickets/Tickets.php";
include "/src/Security.php";

echo "files loaded";
//Setzen der Selbsterstellten Fehlerhandhabungstools
set_error_handler("ErrorHandler::handleError");
set_exception_handler("ErrorHandler::handleException");

//Auseinandernehemen der URI damit mit bestimmten Teilen gleich weitergearbeitet werden kann   jkjgh
$parts = explode("/", $_SERVER["REQUEST_URI"]);
/*-------------------Erstellen aller Klassenobjeckte-------------*/
echo "craete Objekts";
try {

$Security = new Security();

$dbUsers = new DatabaseUsers();

$UserHandling = new UserHandling($dbUsers, $Security);

$dbTickets = new DatabaseTickets();

$SitzHandling = new Tickets($dbTickets, $Security);
} catch (PDOException $e) {
    echo "initzialisation failed in index(): \n" . $e->getMessage();
}
echo "Objeckts created";
/*-------------------Bearbeiten der Anfrage-------------*/

switch ($parts[1]) {
    case "LogIn":
        $UserHandling->checkLogin();       
        break;
    case "Register":
        $UserHandling->createAcc();
        break;
    case "Reseting":
        $UserHandling->resetPSW();
        break;
    case "RequestEmail":
        $UserHandling->resetingEmail();
        break;
    case "bestaetigung":
        $dbUsers->bestaetigen();
        break;
    case "Freischalten":
        echo $UserHandling->FreischaltenTabelle();
    case "Freigeschaltet":
        $UserHandling->UserFreischalten();
        break;
    case "create":
        $dbUsers->createRegistrierung();
        break;
    default://Da keine bekannte aktion getetigt werden soll
        http_response_code(404);
        exit;
        break;
}
