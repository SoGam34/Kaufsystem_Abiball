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

            $state = $UserHandling->checkLogin();

            if ($state == false) 
            {
                exit;
            }

            $session = session_start([
                'name' => "UId",
                'cookie_secure' => true,
                'cookie_httponly' => "false",
                'cookie_samesite' => "Strict"
            ]);

            header("Access-Control-Allow-Origin: https://abi24bws.de");
            header("Access-Control-Allow-Methods: POST, GET");

            if ($session == false) {
                echo json_encode(["Status" => "ERROR", "Message" => "Schwerwiegender interner Systemfehler, bitte kontaktieren Sie den Support mit dem Fehlercode 006."]);
                exit;
            }

            $UId = session_id();

            if (($UId == false)||($UId == "")) {
                echo json_encode(["Status" => "ERROR", "Message" => "Schwerwiegender interner Systemfehler, bitte kontaktieren Sie den Support mit dem Fehlercode 007."]);
                exit;
            }

            $dbUsers->addsession($UId, $state);

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

            if ($state == true) {
                echo json_encode(["Status" => "ERROR", "Message" => "Schwerwiegender interner Systemfehler, bitte kontaktieren Sie den Support mit dem Fehlercode 020."]);
                exit;
            }

            echo json_encode(["Status" => "OK"]);
            exit;

            break;

        case  "Tickets":
            require_once "src/User/DatabaseUsers.php";
            require_once "src/Security.php";
            require_once "src/config.php";
            require_once "src/Tickets/DatabaseTickets.php";
            require_once "src/Tickets/Tickets.php";

            require_once "src/ErrorHandler.php";

            //Setzen der Selbsterstellten Fehlerhandhabungstools
            set_error_handler("ErrorHandler::handleError");
            set_exception_handler("ErrorHandler::handleException");

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

            $SitzHandling->AlleTickets();
            break;

        case  "KaufTicket":
            require_once "src/User/DatabaseUsers.php";
            require_once "src/Security.php";
            require_once "src/config.php";
            require_once "src/Tickets/DatabaseTickets.php";
            require_once "src/Tickets/Tickets.php";
            require_once "src/Tickets/PaypalCheckout.class.php";

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

            $paypal = new PaypalCheckout($Security);

            $response = array('status' => 0, 'msg' => 'Transaction Failed!');

            if (!empty($_POST['paypal_order_check']) && !empty($_POST['order_id'])) {
                // Validate and get order details with PayPal API 
                try {
                    $order = $paypal->validate($_POST['order_id']);
                } catch (Exception $e) {
                    $api_error = $e->getMessage();
                }

                if ((!empty($order)) && ($order != false)) {
                    $order_id = $order['id'];
                    $order_status = $order['status'];

                    if (!empty($order_id) && $order_status == 'COMPLETED') {

                        $dbTickets->setTicket($teilnehmer["Temail"], $_POST["amount"]);
                        $response = array('status' => 1, 'msg' => 'Transaction completed!');
                    }
                } else {
                    $response['msg'] = $api_error;
                }
            }
            echo json_encode($response);
            break;

        case "Abstimmung":
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

            $teilnehmer = $dbUsers->verifysession($_COOKIE["UId"]);

            if ($teilnehmer == false) {
                echo json_encode(["Status" => "ERROR", "Message" => "Sie sind nicht angemeldet, daher wird diese anfrage nicht bearbeitet."]);
                exit;
            }

            $data = (array)json_decode(file_get_contents("php://input"), true);

            $dbUsers->setAbstimmung($teilnehmer["Temail"], $data["location"]);
            break;
    }
} 

else 
{
    require_once "src/User/DatabaseUsers.php";
    require_once "src/User/UserHandling.php";
    require_once "src/Security.php";
    require_once "src/config.php";
    require_once "src/Tickets/Tickets.php";
    require_once "src/Tickets/DatabaseTickets.php";

    //Setzen der Selbsterstellten Fehlerhandhabungstools

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

    $dbTickets = new DatabaseTickets($Security, $dbwrite, $dbreade);

    /*-------------------Bearabeiten der Anfrage-------------*/

    // header("Access-Control-Allow-Origin: https://abi24bws.de");
    // header("Access-Control-Allow-Methods: POST, GET");

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
        case "clear":
            try {
                $dbUsers->deleteRegistrierung("widawski.nico@gmail.com");
            } catch (PDOException $e) {
                echo "Error deleting entry: \n" . $e->getMessage();
            }
            echo "succes";
            break;

        default:
            //Da keine bekannte aktion getetigt werden soll
            http_response_code(404);
            exit;
    }
}
