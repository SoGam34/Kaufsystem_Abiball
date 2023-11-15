<?php

use Tests\Support\UnitTester;

class DatabaseUsers
{
    public function __construct()
    {
        
    }

    public function someting() {
        return true; 
    }
}

class Security
{
    public function PSW_is_safe($data) {
        return true; 
    }

    public function EMail_is_safe($data) {
        return true; 
    }
}

class UserHandlingTest extends \Codeception\Test\Unit
{

    protected UnitTester $tester;

    protected function _before()
    {
    }

    // tests
    public function testSomeFeature()
    {
        require_once "Server/src/config.php";
        require_once "Server/src/User/UserHandling.php";

        $db = new DatabaseUsers();
        $s = new Security();


        $UH = new UserHandling($db, $s);

        $data = array("passwort");
        $this->tester->assertTrue($UH->createAcc());
    }
}
