<?php

namespace Tests\Unit;

use \Tests\Support\UnitTester;

class ExampleTest extends \Codeception\Test\Unit
{
    protected UnitTester $tester;

    protected function _before()
    {
        $s = new Server/src/Security();
    }

    // tests
    public function testVerschlüsselung()
    {
        $teststring = "sdlfjweriofjiwefjlajliseoaäwjeifhweroifjldkjfosw";

        $tester->assertEquals($teststring, $s->decrypt($s->encrypt($teststring)));
    }

    public function testPSW()
    {
        $tester->assertFalse($s->PSW_is_safe(kiwefjoiawfiojof));
        $tester->assertFalse($s->PSW_is_safe("asdfghjklbvnmfj"));
        $tester->assertFalse($s->PSW_is_safe("12345678909865"));
        $tester->assertFalse($s->PSW_is_safe("a12345689"));
        $tester->assertFalse($s->PSW_is_safe("1asfkhjhn"));

        $tester->assertTrue($s->PSW_is_safe("aasdfghj12"));
        $tester->assertTrue($s->PSW_is_safe("123456789a"));
        $tester->assertTrue($s->PSW_is_safe("üäöüäöüüäöüäö12123314"));
        $tester->assertTrue($s->PSW_is_safe("###/-/-++-*/+#´ßa3"));        
    }

    public function testEmail()
    {
        $tester->assertFalse($s->EMail_is_safe(test@email.de));
        $tester->assertFalse($s->EMail_is_safe("test.de"));
        $tester->assertFalse($s->EMail_is_safe("test@test"));
        $tester->assertFalse($s->EMail_is_safe("testtest.de"));

        $tester->assertTrue($s->EMail_is_safe("test@test.de"));
        $tester->assertTrue($s->EMail_is_safe("test+abi24bws.de@test.de"));
    }

    public function testID()
    {
        $tester->assertTrue($s->check_id(546946846));
        $tester->assertTrue($s->check_id("5646465454"));
        $tester->assertFalse($s->check_id(sdfge));
        $tester->assertFalse($s->check_id("sgffrgwe"));
    }
}

