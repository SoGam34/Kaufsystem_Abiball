<?php

    $parts = explode("/", $_SERVER["REQUEST_URI"]);

   if((isset($_COOKIE["UId"]) )|| ($parts[1]=="Login"))
    {
        switch($parts[1])
        {
            case "Login":
                require_once "src/User/DatabaseUsers.php";
                require_once "src/User/UserHandling.php";
                require_once "src/Security.php";
                require_once "src/config.php";

                $Security = new Security();
                
                $dsnW = "mysql:host=" . SQL_SERVER_NAME_W . ";dbname=" . SQL_DB_NAME_W . ";charset=utf8";
                $dbwrite = new PDO($dsnW, SQL_DB_USER_W, SQL_DB_PSW_W, [
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_STRINGIFY_FETCHES => false
                ]);


                $dsnR = "mysql:host=" . SQL_SERVER_NAME_R . ";dbname=" . SQL_DB_NAME_R . ";charset=utf8";
                $dbreade = new PDO($dsnR, SQL_DB_USER_R, SQL_DB_PSW_R, [
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_STRINGIFY_FETCHES => false
                ]);

                $dbUsers = new DatabaseUsers($Security, $dbwrite, $dbreade);

                $UserHandling = new UserHandling($dbUsers, $Security);

                $state = $UserHandling->checkLogin();

                if($state!=false)
                {
                    $session=session_start([
                        'name'=>"UId",
                        'cookie_secure'=>true,
                        'cookie_httponly'=>"true", 
                        'cookie_samesite'=>"Strict"
                    ]);
                
                    header("Access-Control-Allow-Origin: https://abi24bws.de");
                    header("Access-Control-Allow-Methods: POST, GET");
                    
                    if($session==true)
                    {
                        $UId = session_id();
                    
                        if($UId==false)
                        {
                            echo json_encode(["Status" => "ERROR", "Message" => "Schwerwiegender interner Systemfehler, bitte kontaktieren Sie den Support mit dem Fehlercode 007."]);
                            exit;
                        }
                        
                        else if($UId=="")
                        {
                            echo json_encode(["Status" => "ERROR", "Message" => "Schwerwiegender interner Systemfehler, bitte kontaktieren Sie den Support mit dem Fehlercode 008."]);
                            exit;
                        }
                        
                        $dbUsers->addsession($UId, $state);
                        echo json_encode(["Status" => "OK", "Erfolgreich"=>true]);
                    }

                    else
                    {
                        echo json_encode(["Status" => "ERROR", "Message" => "Schwerwiegender interner Systemfehler, bitte kontaktieren Sie den Support mit dem Fehlercode 006."]);
                        exit;
                    }
                }
            break;

            case "Logout":
                require_once "src/User/DatabaseUsers.php";
                require_once "src/Security.php";
                require_once "src/config.php";

                $Security = new Security();

                $dsnW = "mysql:host=" . SQL_SERVER_NAME_W . ";dbname=" . SQL_DB_NAME_W . ";charset=utf8";
                $dbwrite = new PDO($dsnW, SQL_DB_USER_W, SQL_DB_PSW_W, [
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_STRINGIFY_FETCHES => false
                ]);
                
                $dsnR = "mysql:host=" . SQL_SERVER_NAME_R . ";dbname=" . SQL_DB_NAME_R . ";charset=utf8";
                $dbreade = new PDO($dsnR, SQL_DB_USER_R, SQL_DB_PSW_R, [
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_STRINGIFY_FETCHES => false
                ]);
                
                $dbUsers = new DatabaseUsers($Security, $dbwrite, $dbreade);
                
                $dbUsers->EndSession($_COOKIE["UId"]);

                $state=setcookie("UId");
                    
                if($state==true)
                {
                    echo json_encode(["Status" => "OK"]);
                }
                
                else
                {
                    echo json_encode(["Status" => "ERROR", "Message" => "Schwerwiegender interner Systemfehler, bitte kontaktieren Sie den Support mit dem Fehlercode 020."]);
                    exit;
                }
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
                
                $dsnW = "mysql:host=" . SQL_SERVER_NAME_W . ";dbname=" . SQL_DB_NAME_W . ";charset=utf8";
                $dbwrite = new PDO($dsnW, SQL_DB_USER_W, SQL_DB_PSW_W, [
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_STRINGIFY_FETCHES => false
                ]);
                
                $dsnR = "mysql:host=" . SQL_SERVER_NAME_R . ";dbname=" . SQL_DB_NAME_R . ";charset=utf8";
                $dbreade = new PDO($dsnR, SQL_DB_USER_R, SQL_DB_PSW_R, [
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_STRINGIFY_FETCHES => false
                ]);
                
                $dbUsers = new DatabaseUsers($Security, $dbwrite, $dbreade);
                
                $teilnehmer=$dbUsers->verifysession($_COOKIE["UId"]);
                
                if($teilnehmer==false)
                {
                    echo json_encode(["Status" => "ERROR", "Message" => "Sie sind nicht angemeldet, daher wird diese anfrage nicht bearbeitet."]);
                    exit;
                }

                $dbTickets = new DatabaseTickets($Security, $dbwrite, $dbreade);

                $SitzHandling = new Tickets($dbTickets, $Security);

                $SitzHandling->AlleTickets();
            break;

            case  "KaufTicket":
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
                
                $dsnW = "mysql:host=" . SQL_SERVER_NAME_W . ";dbname=" . SQL_DB_NAME_W . ";charset=utf8";
                $dbwrite = new PDO($dsnW, SQL_DB_USER_W, SQL_DB_PSW_W, [
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_STRINGIFY_FETCHES => false
                ]);
                
                $dsnR = "mysql:host=" . SQL_SERVER_NAME_R . ";dbname=" . SQL_DB_NAME_R . ";charset=utf8";
                $dbreade = new PDO($dsnR, SQL_DB_USER_R, SQL_DB_PSW_R, [
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_STRINGIFY_FETCHES => false
                ]);
                
                $dbUsers = new DatabaseUsers($Security, $dbwrite, $dbreade);
                
                $teilnehmer=$dbUsers->verifysession($_COOKIE["UId"]);
                
                if($teilnehmer==false)
                {
                    echo json_encode(["Status" => "ERROR", "Message" => "Sie sind nicht angemeldet, daher wird diese anfrage nicht bearbeitet."]);
                    exit;
                }

                $dbTickets = new DatabaseTickets($Security, $dbwrite, $dbreade);

                $SitzHandling = new Tickets($dbTickets, $Security);

                $SitzHandling->Ticketgekauft();
            break;

            case "Abstimmung":
                require_once "src/User/DatabaseUsers.php";
                require_once "src/Security.php";
                require_once "src/config.php";
                require_once "src/ErrorHandler.php";

                //Setzen der Selbsterstellten Fehlerhandhabungstools
                set_error_handler("ErrorHandler::handleError");
                set_exception_handler("ErrorHandler::handleException");

                $Security = new Security();
                
                $dsnW = "mysql:host=" . SQL_SERVER_NAME_W . ";dbname=" . SQL_DB_NAME_W . ";charset=utf8";
                $dbwrite = new PDO($dsnW, SQL_DB_USER_W, SQL_DB_PSW_W, [
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_STRINGIFY_FETCHES => false
                ]);
                
                $dsnR = "mysql:host=" . SQL_SERVER_NAME_R . ";dbname=" . SQL_DB_NAME_R . ";charset=utf8";
                $dbreade = new PDO($dsnR, SQL_DB_USER_R, SQL_DB_PSW_R, [
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_STRINGIFY_FETCHES => false
                ]);
                
                $dbUsers = new DatabaseUsers($Security, $dbwrite, $dbreade);
                
                $teilnehmer=$dbUsers->verifysession($_COOKIE["UId"]);

                if($teilnehmer==false)
                {
                    echo json_encode(["Status" => "ERROR", "Message" => "Sie sind nicht angemeldet, daher wird diese anfrage nicht bearbeitet."]);
                    exit;
                }

                $dbUsers->setAbstimmung($teilnehmer);
            break;
        }
    }

    else 
    {
        require_once "src/ErrorHandler.php";
        require_once "src/User/DatabaseUsers.php";
        require_once "src/User/UserHandling.php";
        require_once "src/Security.php";
        require_once "src/config.php";
       
        /*-------------------Erstellen aller Klassenobjeckte-------------*/

        $Security = new Security();

        $dsnW = "mysql:host=" . SQL_SERVER_NAME_W . ";dbname=" . SQL_DB_NAME_W . ";charset=utf8";
        $dbwrite = new PDO($dsnW, SQL_DB_USER_W, SQL_DB_PSW_W, [
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_STRINGIFY_FETCHES => false
        ]);

        $dsnR = "mysql:host=" . SQL_SERVER_NAME_R . ";dbname=" . SQL_DB_NAME_R . ";charset=utf8";
        $dbreade = new PDO($dsnR, SQL_DB_USER_R, SQL_DB_PSW_R, [
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_STRINGIFY_FETCHES => false
        ]);

        $dbUsers = new DatabaseUsers($Security, $dbwrite, $dbreade);
        
        $UserHandling = new UserHandling($dbUsers, $Security);

       

        /*-------------------Bearabeiten der Anfrage-------------*/

        header("Access-Control-Allow-Origin: https://abi24bws.de");
        header("Access-Control-Allow-Methods: POST, GET");

        //Setzen der Selbsterstellten Fehlerhandhabungstools
        set_error_handler("ErrorHandler::handleError");
        set_exception_handler("ErrorHandler::handleException");
 
        require_once "src/Tickets/Tickets.php";
        require_once "src/Tickets/DatabaseTickets.php";

        $dbTickets = new DatabaseTickets($Security, $dbwrite, $dbreade);
        $SitzHandling = new Tickets($dbTickets, $Security);

        switch ($parts[1]) 
        {
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
            case "create":
                $dbUsers->createRegistrierung();
                break;
            case "clear":
                //$dbUsers->cleardb();
                try {
                    $dbUsers->deleteRegistrierung("widawski.nico@gmail.com");
                } catch (PDOException $e) {
                    echo "Error deleting entry: \n" . $e->getMessage();
                }
                echo "succes";
                break;

                case "AB":
                    $dbUsers->setAbstimmung("JohannesEMH@web.de");
            default:
            //Da keine bekannte aktion getetigt werden soll
                http_response_code(404);
                exit;
                break;
        }
    }