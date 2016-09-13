<?php

namespace Spinen\SimplePhpTester;

use PHPUnit_Framework_TestCase;
use Spinen\SimplePhpTester\Constraints\PageLoaded;
use Spinen\SimplePhpTester\Constraints\SourceContains;
use Spinen\SimplePhpTester\Constraints\TextContains;

/**
 * Class TestCase
 *
 * @package Spinen\SimplePhpTester
 */
abstract class TestCase extends PHPUnit_Framework_TestCase
{
    use Browser;

    public function assertPageLoaded()
    {
        self::assertThat($this->successful, (new PageLoaded($this->path)));

        return $this;
    }

    public function see($source)
    {
        self::assertThat($this->crawler, (new SourceContains($source)));

        return $this;
    }

    public function seeText($text)
    {
        self::assertThat($this->crawler, (new TextContains($text)));

        return $this;
    }
}
