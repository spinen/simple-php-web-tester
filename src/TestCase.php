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

    /**
     * Assert that the page was "loaded"
     *
     * @return $this
     */
    public function assertPageLoaded()
    {
        self::assertThat($this->successful, (new PageLoaded($this->path)));

        return $this;
    }

    /**
     * Dump the HTML
     *
     * @return $this
     */
    public function dump()
    {
        var_dump($this->crawler ? $this->crawler->html() : 'No HTML to dump', false);

        return $this;
    }

    /**
     * Assert that there is specific text in the source
     *
     * @param string $source
     *
     * @return $this
     */
    public function see($source)
    {
        self::assertThat($this->crawler, (new SourceContains($source)));

        return $this;
    }

    /**
     * Assert that there is specific text on the page
     *
     * @param string $text
     *
     * @return $this
     */
    public function seeText($text)
    {
        self::assertThat($this->crawler, (new TextContains($text)));

        return $this;
    }
}
