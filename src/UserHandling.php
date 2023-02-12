<?php

class UserHandling
{
    public function __construct(private Database $database)
    {
    }
    
    public function processRequest(string $method, ?string $id): void
    {
        switch($id)
        {
            case "loggingin":
                $this->checkLogin();
            break;

            case "register":
                $this->createAcc();
            break;
            case "reseting":
                $this->resetAcc();
        }
    }
    
    private function resetAcc()
    {
        $data = json_decode(file_get_contents("php://input"));
        
        $errors = $this->getValidationErrors($data);
        if(!empty($errors))
        {
            echo json_encode(["errors" => $errors]);
            break;
        }

        $this->database->ResetPasswort($data["email"], password_hash($data["passwort"], PASSWORD_DEFAULT));
    }
    
    private function checkLogin()
    {
        $data = json_decode(file_get_contents("php://input"));
        
        $errors = $this->getValidationErrors($data);
        if(!empty($errors))
        {
            echo json_encode(["errors" => $errors]);
            break;
        }

        $user = $this->database->getUser($data["email"]);
        
        $entry = empty($user);        
        
        if(! $entry )
        {
            $passVerfy = password_verify($data["Passwort"], $user["UserPasswort"]);
            
            if(! $passVerfy)
            {
                echo json_encode(["passwort" => "invalid"]);
            }

            else{
                echo json_encode(["user" => "valid"]);
            }            
        }

        elseif($entry)
        {
            echo json_encode(["email" => "User not found"]);
        }
    }

    private function createAcc()
    {
        $data = json_decode(file_get_contents("php://input"));

        $errors = $this->getValidationErrors($data);
        
        if(empty($errors))
        {
            $this->database->insertUser($data["email"], password_hash($data["passwort"], PASSWORD_DEFAULT)); 
        }

        else 
        {
            echo json_encode(["errors" => $errors]);
            break;
        } 
    }

    private function getValidationErrors($data)
    {
        $errors = [];
        if (array_key_exists("email", $data)) {
            if (filter_var($data["email"], FILTER_VALIDATE_EMAIL) === false) {
                $errors[] = "incorrect email";
        }}

        if (! array_key_exists("email", $data)) {
            $errors[] = "email requiere";
        }

        if (! array_key_exists("passwort", $data)) {
            $errors[] = "passwort requiere";
        }
    }
}