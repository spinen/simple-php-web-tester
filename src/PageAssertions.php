<?php

namespace Spinen\SimplePhpTester;

use Illuminate\Foundation\Testing\Constraints\HasElement;
use Illuminate\Foundation\Testing\Constraints\HasInElement;
use Illuminate\Foundation\Testing\Constraints\HasLink;
use Illuminate\Foundation\Testing\Constraints\HasSource;
use Illuminate\Foundation\Testing\Constraints\HasText;
use Illuminate\Foundation\Testing\Constraints\HasValue;
use Illuminate\Foundation\Testing\Constraints\IsChecked;
use Illuminate\Foundation\Testing\Constraints\IsSelected;
use Illuminate\Foundation\Testing\Constraints\PageConstraint;
use Illuminate\Foundation\Testing\Constraints\ReversePageConstraint;
use Spinen\SimplePhpTester\Constraints\PageLoaded;

trait PageAssertions
{
    /**
     * Assert the given constraint.
     *
     * @param  PageConstraint $constraint
     * @param  bool           $reverse
     * @param  string         $message
     *
     * @return $this
     */
    protected function assertInPage(PageConstraint $constraint, $reverse = false, $message = '')
    {
        if ($reverse) {
            $constraint = new ReversePageConstraint($constraint);
        }

        self::assertThat($this->crawler ?: $this->response, $constraint, $message);

        return $this;
    }

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
     * Assert that a given string is not seen on the current HTML.
     *
     * @param  string $text
     *
     * @return $this
     */
    public function dontSee($text)
    {
        return $this->assertInPage(new HasSource($text), true);
    }

    /**
     * Assert that an element is not present on the page.
     *
     * @param  string $selector
     * @param  array  $attributes
     *
     * @return $this
     */
    public function dontSeeElement($selector, array $attributes = [])
    {
        return $this->assertInPage(new HasElement($selector, $attributes), true);
    }

    /**
     * Assert that a given string is not seen inside an element.
     *
     * @param  string $element
     * @param  string $text
     *
     * @return $this
     */
    public function dontSeeInElement($element, $text)
    {
        return $this->assertInPage(new HasInElement($element, $text), true);
    }

    /**
     * Assert that the given checkbox is not selected.
     *
     * @param  string $selector
     *
     * @return $this
     */
    public function dontSeeIsChecked($selector)
    {
        return $this->assertInPage(new IsChecked($selector), true);
    }

    /**
     * Assert that the given value is not selected.
     *
     * @param  string $selector
     * @param  string $value
     *
     * @return $this
     */
    public function dontSeeIsSelected($selector, $value)
    {
        return $this->assertInPage(new IsSelected($selector, $value), true);
    }

    /**
     * Assert that an input field does not contain the given value.
     *
     * @param  string $selector
     * @param  string $value
     *
     * @return $this
     */
    public function dontSeeInField($selector, $value)
    {
        return $this->assertInPage(new HasValue($selector, $value), true);
    }

    /**
     * Assert that a given link is not seen on the page.
     *
     * @param  string      $text
     * @param  string|null $url
     *
     * @return $this
     */
    public function dontSeeLink($text, $url = null)
    {
        return $this->assertInPage(new HasLink($text, $url), true);
    }

    /**
     * Assert that a given string is not seen on the current text.
     *
     * @param  string $text
     *
     * @return $this
     */
    public function dontSeeText($text)
    {
        return $this->assertInPage(new HasText($text), true);
    }

    /**
     * Filter elements according to the given name or ID attribute.
     *
     * @param  string       $name
     * @param  array|string $elements
     *
     * @return \Symfony\Component\DomCrawler\Crawler
     */
    protected function filterByNameOrId($name, $elements = '*')
    {
        $name = str_replace('#', '', $name);

        $id = str_replace(['[', ']'], ['\\[', '\\]'], $name);

        $elements = is_array($elements) ? $elements : [$elements];

        array_walk($elements, function (&$element) use ($name, $id) {
            $element = "{$element}#{$id}, {$element}[name='{$name}']";
        });

        return $this->crawler->filter(implode(', ', $elements));
    }

    /**
     * Assert that the given checkbox is selected.
     *
     * @param  string $selector
     * @param  bool   $negate
     *
     * @return $this
     */
    public function seeIsChecked($selector, $negate = false)
    {
        return $this->assertInPage(new IsChecked($selector), $negate);
    }

    /**
     * Assert that the expected value is selected.
     *
     * @param  string $selector
     * @param  string $value
     * @param  bool   $negate
     *
     * @return $this
     */
    public function seeIsSelected($selector, $value, $negate = false)
    {
        return $this->assertInPage(new IsSelected($selector, $value), $negate);
    }

    /**
     * Dump the HTML
     *
     * @return $this
     */
    public function dump()
    {
        print_r($this->crawler ? $this->crawler->html() : 'No HTML to dump');

        return $this;
    }

    /**
     * Assert that a given string is seen on the current HTML.
     *
     * @param  string $text
     * @param  bool   $negate
     *
     * @return $this
     */
    public function see($text, $negate = false)
    {
        return $this->assertInPage(new HasSource($text), $negate);
    }

    /**
     * Assert that an element is present on the page.
     *
     * @param  string $selector
     * @param  array  $attributes
     * @param  bool   $negate
     *
     * @return $this
     */
    public function seeElement($selector, array $attributes = [], $negate = false)
    {
        return $this->assertInPage(new HasElement($selector, $attributes), $negate);
    }

    /**
     * Assert that a given string is seen inside an element.
     *
     * @param  string $element
     * @param  string $text
     * @param  bool   $negate
     *
     * @return $this
     */
    public function seeInElement($element, $text, $negate = false)
    {
        return $this->assertInPage(new HasInElement($element, $text), $negate);
    }

    /**
     * Assert that an input field contains the given value.
     *
     * @param  string $selector
     * @param  string $expected
     * @param  bool   $negate
     *
     * @return $this
     */
    public function seeInField($selector, $expected, $negate = false)
    {
        return $this->assertInPage(new HasValue($selector, $expected), $negate);
    }

    /**
     * Assert that a given link is seen on the page.
     *
     * @param  string      $text
     * @param  string|null $url
     * @param  bool        $negate
     *
     * @return $this
     */
    public function seeLink($text, $url = null, $negate = false)
    {
        return $this->assertInPage(new HasLink($text, $url), $negate);
    }

    /**
     * Assert that a given string is seen on the current text.
     *
     * @param  string $text
     * @param  bool   $negate
     *
     * @return $this
     */
    public function seeText($text, $negate = false)
    {
        return $this->assertInPage(new HasText($text), $negate);
    }
}
