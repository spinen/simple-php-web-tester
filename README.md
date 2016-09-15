# Simple Tester for PHP web sites

We were wanting a testing framework very similar to Laravel's to run acceptance-like tests on basic php sites.  This was built to help a college class learn about TDD in a dynamic content class.  We wanted to use Laravel's constraints that are in the Foundation section, but they no longer subsplit that code out into a read-only Illuminate repository.  We are requiring the Laravel Framework just to get the constraints, but it's going to pull in many packages.  Therefore, only require this package in the dev section.

## Credits

There are huge sections of the code that is in `PageAssertions` class that is a complete copy & paste from Laravel's `InteractsWithPages` class.  We would have just used the class, but it relies too much on booting Laravel, so we wrote our own class & just pulled over the assertions that we could reuse.  

## Installation

```
composer require --dev spinen/spinen-php-web-tester
```

## Usage

You can either extend an abstract class that we provide or use the traits that provides the functionality.

1. Extend `Spinen\SimplePhpTester\TestCase` so that you will have access to the test.
2. Mixin the test by `use`ing `Spinen\SimplePhpTester\Browser` and `Spinen\SimplePhpTester\PageAssertions` in your testfile.

## Configuration

The test assumes that your web files are in a directory named `public`.  If that is not the case, then you have 3 options...

1. Add a protected property to your test class named `$web_root` with the name of the directory (i.e. `protected $web_root = 'web';`).
2. Call `setWebRoot` with the directory relative to the directory that you are running phpunit (i.e. `$this->setWebRoot('web');`).
3. Add a protected method to your test class named `determinedFullPath` that builds the full path to the directory of the script being tested.

## Assertions

Here is a list of the assertions that are provided...

* assertPageLoaded
* dontSee
* dontSeeElement
* dontSeeInElement
* dontSeeInField
* dontSeeIsChecked
* dontSeeIsSelected
* dontSeeLink
* dontSeeText
* see
* seeElement
* seeInElement
* seeInField
* seeIsChecked
* seeIsSelected
* seeLink
* seeText

## Example Test

```php
<?php

use Spinen\SimplePhpTester\TestCase;

class HomeTest extends TestCase
{
    /**
     * @test
     */
    public function it_loads_the_home_page()
    {
        $this->visit('/') // Could have used $this->visit('/index.php')
             ->assertPageLoaded();
    }
    
    /**
     * @test
     */
    public function it_has_the_expected_title_in_an_h1()
    {
        $this->visit('/')
             ->see('<h1>Some title</h1>');
    }
    
    /**
     * @test
     */
    public function it_has_the_the_correct_navigation_links()
    {
        $this->visit('/')
             ->seeLink('Home')
             ->seeLink('About Us')
             ->seeLink('Services')
             ->seeLink('Contact')
             ->dontSeeLink('Profile');
    }
}
```

## Troubleshooting

You can call the `dump` method on your chain of commands to print out the rendered HTML.
