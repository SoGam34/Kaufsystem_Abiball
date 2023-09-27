<?php

class ErrorHandler
{
    public static function handleException(Throwable $exception): void
    {
        http_response_code(500);
        
        /*if($exception->getCode()=="23000")
        {
            echo json_encode(["Status"=>"ERROR", "Message"=>"Die Email-Adresse wird bereits von wem anders Verwendet"]);
        }

        else{*/
             echo json_encode([
            "code" => $exception->getCode(),
            "message" => $exception->getMessage(),
            "file" => $exception->getFile(),
            "line" => $exception->getLine()
        ]);
        
       
    }
    
    public static function handleError(
        int $errno,
        string $errstr,
        string $errfile,
        int $errline
    ): bool
    {
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }
}
?>










