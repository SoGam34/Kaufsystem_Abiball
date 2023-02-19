<?php

declare(strict_types=1);

spl_autoload_register(function ($class) {
    require __DIR__ . "/src/$class.php";
});

set_error_handler("ErrorHandler::handleError");
set_exception_handler("ErrorHandler::handleException");

header("Content-type: application/json; charset=UTF-8");

$parts = explode("/", $_SERVER["REQUEST_URI"]);
/*-------------------Erstellen aller Klassenobjeckte-------------*/
$database = new Database("localhost", "abiball", "root", "root");


$data = (array) json_decode(file_get_contents("php://input"), true);

echo json_encode($data);

//echo file_get_contents("php://input");
//$UserHandling=new UserHandling($database);

//$test = new test();
/*

echo $UserHandling;= new UserHandling($database);
echo "\nUser handling erfolgreich";exit;*/
https://github.com/SoGam34/Kaufsystem_Abiball
/*-------------------Bearbeiten der Anfrage-------------*/
/*switch($parts[5])
{git remote set-url origin {https://github.com/SoGam34/Kaufsystem_Abiball}
    case "loggingin":
        $UserHandling->checkLogin(); 
    break;
    case "register":
        $UserHandling->createAcc();
    break;
    case "reseting":
        $UserHandling->resetAcc();
    break;
    default:
    http_response_code(404);
    exit;
    break;
}
*/
?>