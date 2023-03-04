<?php
class Test
{
    public function __construct(private Database $database)
    {
    }

    public function createAcc()
    {
        $data["email"]= $_POST["email"];
        $data["passwort"]= $_POST["passwort"];
        $this->database->insertUser($data["email"], password_hash($data["passwort"], PASSWORD_DEFAULT));
    }

    public function resetAcc()
    {
        $data["email"]= $_POST["email"];
        $data["passwort"]= $_POST["passwort"];
        $this->database->ResetPasswort($data["email"], password_hash($data["passwort"], PASSWORD_DEFAULT));
    }

    public function checkLogin()
    {
        $data["email"]= $_POST["email"];
        $data["passwort"]= $_POST["passwort"];

        $user = $this->database->getUser($data["email"]);
        
        $passVerfy = password_verify($data["passwort"], $user["passwort"]);

        if (!$passVerfy) {
            echo json_encode(["passwort" => "invalid"]);
        } else {
            echo json_encode(["passwort" => "valid"]);
        }
    }
}
?>