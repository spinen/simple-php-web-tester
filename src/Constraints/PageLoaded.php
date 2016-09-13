<?php

namespace Spinen\SimplePhpTester\Constraints;

class PageLoaded extends BrowserConstraint
{
    /**
     * The path loaded
     *
     * @var string
     */
    protected $path;

    /**
     * Create a new constraint instance.
     *
     * @param  string $path
     */
    public function __construct($path)
    {
        parent::__construct();

        $this->path = $path;
    }

    /**
     * Evaluates the constraint for parameter $other. Returns true if the
     * constraint is met, false otherwise.
     *
     * @param mixed $successful
     *
     * @return bool
     * @internal param mixed $crawler
     *
     */
    public function matches($successful)
    {
        return $successful === true;
    }

    protected function failureDescription($other)
    {
        return sprintf("[%s] %s", $this->path, $this->toString());
    }

    /**
     * Returns a string representation of the constraint.
     *
     * @return string
     */
    public function toString()
    {
        return sprintf("was loaded", $this->path);
    }
}
