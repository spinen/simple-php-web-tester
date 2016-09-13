<?php

namespace Spinen\SimplePhpTester;

use ReflectionClass;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

trait Browser
{
    /**
     * Trigger script to fail on inability to load script
     *
     * @var bool
     */
    protected $abort_on_error = false;

    /**
     * Parsed output
     *
     * @var Crawler
     */
    protected $crawler;

    /**
     * Location that we are testing
     *
     * @var string
     */
    protected $path;

    /**
     * Did the script load OK?
     *
     * @var null|boolean
     */
    protected $successful = null;

    /**
     * Folder that contains the web files
     *
     * @var string
     */
    protected $web_root = 'public';

    /**
     * "Visit" a page
     *
     * Take a uri & pretend that we vested it by running it through the php processor to get the output.  Then let the
     * DomCrawler parse the output.
     *
     * @param string $uri
     *
     * @return $this
     */
    public function visit($uri)
    {
        list($this->path, $query) = array_pad(explode('?', $uri), 2, null);

        $this->determinePath();

        // NOTE: This is out until the windows issue denoted below is fixed
        // $command = $this->buildEnvironmentVariables() .
        $command = $this->buildCallToScript() . $this->buildQueryVariables($query);

        $process = new Process($command);
        $process->run();

        if (!$process->isSuccessful()) {
            $this->successful = false;

            if ($this->abort_on_error) {
                throw new ProcessFailedException($process);
            }

            return $this;
        }

        $this->successful = true;

        $this->crawler = new Crawler($process->getOutput(), $uri);

        return $this;
    }

    /**
     * Force the execution to abort if page cannot load
     *
     * @param bool $flag
     *
     * @return $this
     */
    public function abortOnFail($flag = true)
    {
        $this->abort_on_error = (bool)$flag;

        return $this;
    }

    /**
     * Full path to php-cgi with the full path to the script
     *
     * @return string
     */
    protected function buildCallToScript()
    {
        return $this->phpCgiScriptPath() . ' -f "' . $this->determinedFullPath($this->path) . '"';
    }

    /**
     * Push variables into php, so that they are there as $_SERVER
     *
     * @return string
     */
    protected function buildEnvironmentVariables()
    {
        $prefix = '';
        $prepend = ' ';

        if (strncasecmp(PHP_OS, 'WIN', 3) == 0) {
            $prefix = 'SET ';
            $prepend = ';';
        }

        // TODO: Figure out how to pass vars in windows
        return "${prefix}REQUEST_URI='/${path}'${prepend}${prefix}SCRIPT_NAME='/${path}'${prepend}";
    }

    /**
     * Format the query string parameters as the php-cgi needs them
     *
     * @param $query
     *
     * @return null|string
     */
    protected function buildQueryVariables($query)
    {
        if (is_null($query)) {
            return null;
        }

        return ' ' . str_replace('&', ' ', $query);
    }

    /**
     * Build out the full path the script
     *
     * Since this file gets extended by a test case, __DIR__ will not work as it points to this file.  Use reflection to
     * review the class to get the directory.
     *
     * @return string
     */
    protected function determinedFullPath()
    {
        return dirname((new ReflectionClass($this))->getFileName()) .
               DIRECTORY_SEPARATOR .
               '..' .
               DIRECTORY_SEPARATOR .
               $this->getWebRoot() .
               DIRECTORY_SEPARATOR .
               $this->path;
    }

    /**
     * Normalize the path
     */
    protected function determinePath()
    {
        $this->path = ltrim($this->path, '/');

        if ('' === $this->path) {
            $this->path = "index.php";
        }
    }

    /**
     * Getter for the webroot
     *
     * Don't allow it to have a slash on the front or end
     *
     * @return string
     */
    protected function getWebRoot()
    {
        return trim($this->web_root, "/");
    }

    /**
     * Full path to the php-cgi binary
     *
     * @return mixed
     */
    protected function phpCgiScriptPath()
    {
        return preg_replace("/(php)(\\.exe)?$/um", "$1-cgi$2", (new PhpExecutableFinder)->find(false));
    }

    /**
     * Allow setting the root
     *
     * @param $web_root
     *
     * @return $this
     */
    public function setWebRoot($web_root)
    {
        $this->web_root = $web_root;

        return $this;
    }
}
