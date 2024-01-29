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

            $input = (array)json_decode(file_get_contents("php://input"),true);

            $UserHandling->checkLogin($input);

            $session = session_start([
                'name' => "UId",
                'cookie_secure' => true,
                'cookie_httponly' => "false",
                'cookie_samesite' => "Strict"
            ]);

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

            if ($state == false) {
                echo json_encode(["Status" => "ERROR", "Message" => "Schwerwiegender interner Systemfehler, bitte kontaktieren Sie den Support mit dem Fehlercode 020."]);
                exit;
            }

            echo json_encode(["Status" => "OK"]);
            exit;

            break;

        case  "KaufTicket":

            require_once "src/config.php";
            require_once "src/User/DatabaseUsers.php";
            require_once "src/Security.php";
            require_once "src/Tickets/DatabaseTickets.php";
            require_once "src/Tickets/Tickets.php";

            
            require_once "src/ErrorHandler.php";

            //Setzen der Selbsterstellten Fehlerhandhabungstools
            set_error_handler("ErrorHandler::handleError");
            set_exception_handler("ErrorHandler::handleException");

            require_once "src/Tickets/PaypalCheckoutclass.php";

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

            /*$paypal = new PaypalCheckout("https://api-m.sandbox.paypal.com/v1/oauth2/token", "https://api-m.sandbox.paypal.com/v2/checkout/orders/", Client_ID, Client_secret);

            if (!empty($_POST['paypal_order_check']) && !empty($_POST['order_id'])) 
            {
                // Validate and get order details with PayPal API 
                try 
                {
                    $order = $paypal->validate($_POST['order_id']);

                    echo json_encode(["order in" => $order]);
                    if ((!empty($order)) && ($order != false)) {
                        $order_id = $order['id'];
                        $order_status = $order['status'];

                        if (!empty($order_id) && $order_status == 'COMPLETED') {
*/
                            $dbTickets->setTicket($teilnehmer["Temail"], $_POST["amount"]);

                            $amount = $_POST["amount"];

                            settype($test, "string");

                           /* for($i=0; $i<$amount; $i++)
                            {
                                $test = $test + $_POST[$i];
                            }
*/
                            echo json_encode(['status' => 1, 'msg' => 'Die Bezahlung war erfolgreich! In den nächsten Minuten erhalten Sie eine E-Mail mit dem Ticket. Wir freuen uns schon dich auf dem Abiball zu treffen.', 'anzahl' => $amount, 'test' => $test]);
/*                        }
                    } else {
                        echo json_encode(["order declined" => $order]);
                        exit;
                    }
                }
                catch (Exception $e)
                {
                    echo json_encode(["Status" => "ERROR_1", "Message" =>  $e->getMessage()]); 
                    exit;
                }
            }*/
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

    /*require_once "src/ErrorHandler.php";

    //Setzen der Selbsterstellten Fehlerhandhabungstools
    set_error_handler("ErrorHandler::handleError");
    set_exception_handler("ErrorHandler::handleException");*/
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

    $input = (array)json_decode(file_get_contents("php://input"),true);

    switch ($parts[1]) {
        case "Register":
            $UserHandling->createAcc($input);
            break;
        case "bestaetigung":
            $dbUsers->bestaetigen($input);
            break;
        case "RequestEmail":
            $UserHandling->resetingEmail($input);
            break;
        case "Reseting":
            $UserHandling->resetPSW($input);
            break;
        case "Freischalten":
            echo $UserHandling->FreischaltenTabelle();
            break;
        case "Freigeschaltet":
            $UserHandling->UserFreischalten($input);
            break;
        case "Ablehnen": 
            $UserHandling->Ablehnen($input);
            break;
        default:
            //Da keine bekannte aktion getetigt werden soll
            http_response_code(404);
            exit;
    }
}
