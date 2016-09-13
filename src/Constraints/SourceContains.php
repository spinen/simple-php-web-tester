<?php

namespace Spinen\SimplePhpTester\Constraints;

use PHPUnit_Framework_Constraint;
use Symfony\Component\DomCrawler\Crawler;

class SourceContains extends PHPUnit_Framework_Constraint
{
    /**
     * The source that should be found
     *
     * @var string
     */
    protected $source;

    /**
     * Create a new constraint instance.
     *
     * @param  string $source
     */
    public function __construct($source)
    {
        parent::__construct();

        $this->source = $source;
    }

    /**
     * Evaluates the constraint for parameter $other. Returns true if the
     * constraint is met, false otherwise.
     *
     * @param Crawler|string $crawler
     *
     * @return bool
     * @internal param mixed $crawler
     *
     */
    public function matches($crawler)
    {
        // TODO: Need to deal with possible escaping "/"
        return preg_match("|({$this->source})|i", $crawler->html());
    }

    protected function failureDescription($other)
    {
        return sprintf("[%s] %s", $this->source, $this->toString());
    }

    /**
     * Returns a string representation of the constraint.
     *
     * @return string
     */
    public function toString()
    {
        return sprintf("was found in the source", $this->source);
    }
}
