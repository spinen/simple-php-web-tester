<?php

namespace Spinen\SimplePhpTester;

class ServerCreationTest extends TestTestCase
{
    /**
     * @test
     */
    public function it_can_get_the_output_of_a_script()
    {
        $this->visit('/')
             ->assertPageLoaded()
             ->see('<h1>SPINEN Simple PHP Web Tester</h1>')
             ->seeText('SPINEN Simple PHP Web Tester');
    }
}
