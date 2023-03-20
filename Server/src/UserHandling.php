<?php
class UserHandling
{
    public function __construct(private Database $database)
    {
    }

    public function createAcc()
    {
        $data["email"]= (array)json_decode(file_get_contents("php://input"));
        $data["passwort"]= $_POST["passwort"];
        $id=$this->database->insertRegistrierer($data["email"], password_hash($data["passwort"], PASSWORD_DEFAULT));
        $headers = "From: johannes@abi24bws.de";
        mail($data["email"], "Verifizierung ihrer Email-Adresse bei Abi24bws.de", "Sehr geehrte Abiturientinnen und Abiturienten, \n\n
        bitte bestaetigen Sie ihre Email-Adresse indem Sie auf den folgenden Link klicken: \n\nhttps://abi24bws.de/Bestaetigung.html?id={$id}\n
        Nachdem sie ihre Email bestaetigt haben, bitten wir Sie um ein wenig Geduld bis Sie von unserem Admin-Team freigeschaltet sind. Sobald dies erfolgt ist, erhalten Sie Zugriff auf alle Dienste.
        \n\nWenn Sie sich nicht bei Abi24bws registriert haben, koennen Sie diese Email ignorieren und wir entschuldigen uns fuer die Stoerung\n\n\n
        Mit freundlichen Grueßen\n 
        Ihr Abi24bws Team", $headers);
    }

    public function resetPSW()
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

    public function deleteRegistrirung()
    {
        $this->database->delete("JohannesEMH@web.de");
    }
}
?>