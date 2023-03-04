<?php 

class UserHandling
{
    public function __construct(private Database $database)
    {
         echo "open UserHandling succesfully";
    }
      
    public function resetAcc()
    {
        $data = (array) json_decode(file_get_contents("php://input"), true);
        
        $errors = $this->getValidationErrors($data);
        if(!empty($errors))
        {
            echo json_encode(["errors" => $errors]);
            exit;
        }

        $this->database->ResetPasswort($data["email"], password_hash($data["passwort"], PASSWORD_DEFAULT));
    }
    
    public function checkLogin()
    {
        $data = (array) json_decode(file_get_contents("php://input"), true);
        
        $errors = $this->getValidationErrors($data);
        if(!empty($errors))
        {
            echo json_encode(["errors" => $errors]);
            exit;
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

    public function createAcc()
    {
        $data = (array) json_decode(file_get_contents("php://input"), true);

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

    public function getValidationErrors($data)
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
?>