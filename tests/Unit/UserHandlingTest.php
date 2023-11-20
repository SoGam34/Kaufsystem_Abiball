<?php


use Tests\Support\UnitTester;



class DatabaseUsers 
{
    public function __construct()
    {
        
    }

    public function insertRegister(string $vorname, string $nachname, string $klasse, string $email, string $passwort, string $salt): int {
        return 3; 
    }

    public function getName(string $data) {
        return $data;
    }

    public function getID(string $data) {
        return $data;
    }

    public function getUser($data) {
        if($data== "non")
        {
            return "";
        }

        return array("salt_id"=>"a");
    }

    public function getSalt( $data) {
        return $data;
    }

    public function ResetPasswort($data, $data2): void
    {
    }
}

class Security 
{
    public function PSW_is_safe($data): bool {
        if($data == "a")
        {
            return true; 
        }

        return false;
    }

    public function EMail_is_safe($data):bool {
        if($data == "test@test.com")
        {
            return true; 
        }

        return false;
    }

    public function encrypt(string $data) : string {
        return $data;
    }

    public function decrypt(string $data) : string {
        return $data;
    }

}

class UserHandlingTest extends \Codeception\Test\Unit
{

    protected UnitTester $tester;

    protected function _before()
    {
    }

    // tests
    public function testcreateAcc()
    {
        require_once "Server/src/config.php";
        require_once "Server/src/User/UserHandling.php";
        require_once "Server/src/Security.php";
        require_once "Server/src/User/DatabaseUsers.php";

        $db = new DatabaseUsers();
        $s = new Security();

        $UH = new UserHandling($db, $s);

        $data1 = array("passwort"=>"a", "email"=>"test@test.com", "vorname"=>"a", "nachname"=>"a", "klasse"=>"a");
        $this->tester->assertTrue($UH->createAcc($data1));

        $data2 = array("passwort"=>"b", "email"=>"test@test.com", "vorname"=>"a", "nachname"=>"a", "klasse"=>"a");
        $this->tester->assertFalse($UH->createAcc($data2));

        $data3 = array("passwort"=>"a", "email"=>".com", "vorname"=>"a", "nachname"=>"a", "klasse"=>"a");
        $this->tester->assertFalse($UH->createAcc($data3));

    }

    public function testresetingEmail()
    {
        require_once "Server/src/config.php";
        require_once "Server/src/User/UserHandling.php";
        require_once "Server/src/Security.php";
        require_once "Server/src/User/DatabaseUsers.php";

        $db = new DatabaseUsers();
        $s = new Security();

        $UH = new UserHandling($db, $s);

        $data1 = array("passwort"=>"a", "email"=>"test@test.com", "vorname"=>"a", "nachname"=>"a", "klasse"=>"a");
        $this->tester->assertTrue($UH->createAcc($data1));

        $data3 = array("passwort"=>"a", "email"=>".com", "vorname"=>"a", "nachname"=>"a", "klasse"=>"a");
        $this->tester->assertFalse($UH->createAcc($data3));

    }

    public function testresetPSW()
    {
        require_once "Server/src/config.php";
        require_once "Server/src/User/UserHandling.php";
        require_once "Server/src/Security.php";
        require_once "Server/src/User/DatabaseUsers.php";

        $db = new DatabaseUsers();
        $s = new Security();

        $UH = new UserHandling($db, $s);

        $data1 = array("passwort"=>"a", "email"=>"test@test.com", "vorname"=>"a", "nachname"=>"a", "klasse"=>"a");
        $this->tester->assertTrue($UH->createAcc($data1));

        $data2 = array("passwort"=>"b", "email"=>"test@test.com", "vorname"=>"a", "nachname"=>"a", "klasse"=>"a");
        $this->tester->assertFalse($UH->createAcc($data2));

        $data3 = array("passwort"=>"a", "email"=>".com", "vorname"=>"a", "nachname"=>"a", "klasse"=>"a");
        $this->tester->assertFalse($UH->createAcc($data3));

        $data3 = array("passwort"=>"a", "email"=>"non", "vorname"=>"a", "nachname"=>"a", "klasse"=>"a");
        $this->tester->assertFalse($UH->createAcc($data3));

    }
}
