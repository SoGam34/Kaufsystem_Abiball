<?php

namespace Tests\Unit;

use \Tests\Support\UnitTester;

class ExampleTest extends \Codeception\Test\Unit
{
    protected UnitTester $tester;

    protected function _before()
    {
        $UH = new Server/src/User/UserHandling();
    }

    // tests
    public function testnewAco()
    {
        $data = array("passwort")
        $tester->assertTrue($UH->createAcc());
    }
}