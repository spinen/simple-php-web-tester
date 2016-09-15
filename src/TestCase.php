<?php

namespace Spinen\SimplePhpTester;

use PHPUnit_Framework_TestCase;

/**
 * Class TestCase
 *
 * @package Spinen\SimplePhpTester
 */
abstract class TestCase extends PHPUnit_Framework_TestCase
{
    use Browser, PageAssertions;
}
