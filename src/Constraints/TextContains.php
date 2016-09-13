<?php

namespace Spinen\SimplePhpTester\Constraints;

use Symfony\Component\DomCrawler\Crawler;

class TextContains extends BrowserConstraint
{
    /**
     * The text that should be found
     *
     * @var string
     */
    protected $text;

    /**
     * Create a new constraint instance.
     *
     * @param  string $text
     */
    public function __construct($text)
    {
        parent::__construct();

        $this->text = $text;
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
        return preg_match("|({$this->text})|i", $this->getText($crawler));
    }

    protected function failureDescription($other)
    {
        return sprintf("[%s] %s", $this->text, $this->toString());
    }

    /**
     * Returns a string representation of the constraint.
     *
     * @return string
     */
    public function toString()
    {
        return sprintf("was found in the text", $this->text);
    }
}
