<?php

declare(strict_types=1);

//Laden der Klassen
spl_autoload_register(function ($class) {
    require __DIR__ . "/src/$class.php";
});

//Setzen der Selbsterstellten Fehlerhandhabungstools
set_error_handler("ErrorHandler::handleError");
set_exception_handler("ErrorHandler::handleException");

//Auseinandernehemen der URI damit mit bestimmten Teilen gleich weitergearbeitet werden kann
$parts = explode("/", $_SERVER["REQUEST_URI"]);
/*-------------------Erstellen aller Klassenobjeckte-------------*/
$dbUsers = new DatabaseUsers();

$UserHandling = new UserHandling($dbUsers);

$dbT = new DatabaseTickets();

$SitzHandling = new Tickets

/*-------------------Bearbeiten der Anfrage-------------*/

switch ($parts[1]) {
    case "LogIn":
        $UserHandling->checkLogin();        //   int temp = (einkommen > 10000)?1:0.5;
        break;
    case "Register":
        $UserHandling->createAcc();
        break;
    case "Reseting":
        $UserHandling->resetPSW();
        break;
    case "bestaetigung":
        $dbUsers->bestaetigen();
        break;
    case "Freischalten":
        $dbUsers->FreischaltungsUebersicht();
        break;
    case "Freigeschaltet":
        $dbUsers->Freischalten();
        break;
    case "create":
        $dbUsers->createRegistrierung();
        break;
    default://Da keine bekannte aktion getetigt werden soll
        http_response_code(404);
        exit;
        break;
}
