<?php


namespace Tests\Unit;

use Tests\Support\UnitTester;

class ExampleTest extends \Codeception\Test\Unit
{

    protected UnitTester $tester;

    protected function _before()
    {
    }

    // tests
    public function testPSW()
    {
        $Sicherheit = new \Server\src\Security();

        //Fälle in denen das PSW nicht zulässig ist 
        $this->assertFalse($Sicherheit->PSW_is_safe("123456789"));
        $this->assertFalse($Sicherheit->PSW_is_safe("1234567890"));
        $this->assertFalse($Sicherheit->PSW_is_safe("asdfghjkl"));

        //Fälle in denen das PSW in Ordnung ist 
        $this->assertTrue($Sicherheit->PSW_is_safe("12345678abc"));
        $this->assertTrue($Sicherheit->PSW_is_safe("asdfghhjkl1"));
        $this->assertTrue($Sicherheit->PSW_is_safe("üäöüäöüäöüääöüäöüüä1"));
    }
}
