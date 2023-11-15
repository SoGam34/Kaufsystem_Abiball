<?php

use Tests\Support\UnitTester;

class ExampleTest extends \Codeception\Test\Unit
{
    

    protected UnitTester $tester;

    protected function _before()
    {
        
    }

    // tests
    public function testVerschlüsselung()
    {
        require_once "Server/src/config.php";
        require_once "Server/src/Security.php";
        $s = new Security();

        $teststring = "sdlfjweriofjiwefjlajliseoaäwjeifhweroifjldkjfosw";

        $this->tester->assertEquals($teststring, $s->decrypt($s->encrypt($teststring)));
    }

    
    public function testPSW()
    {
        require_once "Server/src/Security.php";
        $s = new Security();

        $this->tester->assertFalse($s->PSW_is_safe("kiwefjoiawfiojof"));
        $this->tester->assertFalse($s->PSW_is_safe("asdfghjklbvnmfj"));
        $this->tester->assertFalse($s->PSW_is_safe("12345678909865"));
        $this->tester->assertFalse($s->PSW_is_safe("a12345689"));
        $this->tester->assertFalse($s->PSW_is_safe("1asfkhjhn"));

        $this->tester->assertTrue($s->PSW_is_safe("aasdfghj12"));
        $this->tester->assertTrue($s->PSW_is_safe("123456789a"));
        //$this->tester->assertTrue($s->PSW_is_safe("üäöüäöüüäöüäö12123314"));
        $this->tester->assertTrue($s->PSW_is_safe("###/-/-++-*/#ßa3"));
    }

    public function testEmail()
    {
        require_once "Server/src/Security.php";
        $s = new Security();

        $this->tester->assertFalse($s->EMail_is_safe("test.de"));
        $this->tester->assertFalse($s->EMail_is_safe("test@test"));
        $this->tester->assertFalse($s->EMail_is_safe("testtest.de"));

        $this->tester->assertTrue($s->EMail_is_safe("test@test.de"));
        $this->tester->assertTrue($s->EMail_is_safe("test+abi24bws.de@test.de"));
    }

    public function testID()
    {
        require_once "Server/src/Security.php";
        $s = new Security();

        $this->tester->assertTrue($s->check_id(546946846));
        $this->tester->assertTrue($s->check_id("5646465454"));
        $this->tester->assertTrue($s->check_id("sgffrgwe"));
    }
}
