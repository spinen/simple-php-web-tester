<?php

namespace Spinen\SimplePhpTester\Constraints;

use PHPUnit_Framework_Constraint;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class BrowserConstraint
 *
 * @package Spinen\SimplePhpTester\Constraints
 */
abstract class BrowserConstraint extends PHPUnit_Framework_Constraint
{
    /**
     * Html from the crawler or null
     *
     * @param Crawler|null $crawler
     *
     * @return null
     */
    protected function getHtml($crawler)
    {
        if (is_null($crawler)) {
            return null;
        }

        return $crawler->html();
    }

    /**
     * Text from the crawler or null
     *
     * @param Crawler|null $crawler
     *
     * @return null
     */
    protected function getText($crawler)
    {
        if (is_null($crawler)) {
            return null;
        }

        return $crawler->text();
    }
}
