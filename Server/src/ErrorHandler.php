<?php

class ErrorHandler
{
    public static function handleException(Throwable $exception): void
    {
        http_response_code(500);

             echo json_encode([
            "Status" => "ERROR",
            "Message" =>$exception->getMessage() . "file" . $exception->getFile() . "line" . $exception->getLine()
        ]);
        exit;
       
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










