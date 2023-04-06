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
$databaseUsers = new DatabaseUsers();

$UserHandling = new UserHandling($databaseUsers);

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
    case "bestaetigung":
        $databaseUsers->bestaetigen();
        break;
    case "Freischalten":
        $databaseUsers->FreischaltungsUebersicht();
        break;
    case "Freigeschaltet":
        $databaseUsers->Freischalten();
        break;
    case "create":
        $databaseUsers->createRegistrierung();
        break;
    default://Da keine bekannte aktion getetigt werden soll
        http_response_code(404);
        exit;
        break;
}
