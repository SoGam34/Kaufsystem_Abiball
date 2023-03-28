<?php

declare(strict_types=1);

spl_autoload_register(function ($class) {
    require __DIR__ . "/src/$class.php";
});

set_error_handler("ErrorHandler::handleError");
set_exception_handler("ErrorHandler::handleException");

$parts = explode("/", $_SERVER["REQUEST_URI"]);
/*-------------------Erstellen aller Klassenobjeckte-------------*/
$database = new Database();

$UserHandling = new UserHandling($database);

/*-------------------Bearbeiten der Anfrage-------------*/

switch ($parts[1]) {
    case "LogIn":
        $UserHandling->checkLogin();
        break;
    case "Register":
        $UserHandling->deleteRegistrirung();
        $UserHandling->createAcc();
        break;
    case "Reseting":
        $UserHandling->resetPSW();
        break;
    case "bestaetigung":
        $database->bestaetigen();
        break;
    case "create":
        $database->createRegistrierung();
        break;
    default:
        http_response_code(404);
        exit;
        break;
}
