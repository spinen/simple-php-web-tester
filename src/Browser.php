<?php

namespace Spinen\SimplePhpTester;

use InvalidArgumentException;
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
     * The response from the call
     *
     * @var null|string
     */
    protected $response = null;

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
        if (strncasecmp(PHP_OS, 'WIN', 3) == 0) {
            return (new PhpExecutableFinder)->find(false) .
                   ' -e -r "parse_str($_SERVER[\"QUERY_STRING\"], $_GET); include \"' .
                   $this->determinedFullPath($this->path) .
                   '\";"';
        }

        return (new PhpExecutableFinder)->find(false) .
               ' -e -r \'parse_str($_SERVER["QUERY_STRING"], $_GET); include "' .
               $this->determinedFullPath($this->path) .
               '";\'';
    }

    /**
     * Push variables into php, so that they are there as $_SERVER
     *
     * @param string $query
     *
     * @return string
     */
    protected function buildEnvironmentVariables($query)
    {
        // Mac/UNIX defaults
        $command = 'export';
        $chain = '&&';

        // Windows specific
        if (strncasecmp(PHP_OS, 'WIN', 3) == 0) {
            $command = 'set';
            $chain = '&';
            $query = str_replace('&', '^&', $query);
        }

        // Environmental variables to set
        $variables = [
            'REQUEST_URI'  => $this->path,
            'SCRIPT_NAME'  => $this->path,
            'QUERY_STRING' => $query,
        ];

        // Mac/UNIX requires variables to be in enclosed in quotes if there is a space
        if (strncasecmp(PHP_OS, 'WIN', 3) != 0) {
            $variables = array_map(function ($value) {
                return '"' . str_replace('"', '\"', $value) . '"';
            }, $variables);
        }

        $line = null;

        foreach ($variables as $variable => $value) {
            $line .= "${command} ${variable}=${value}${chain}";
        }

        return $line;
    }

    /**
     * Click a link with the given body, name, or ID attribute.
     *
     * @param  string $name
     *
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    protected function click($name)
    {
        $link = $this->crawler->selectLink($name);

        if (!count($link)) {
            $link = $this->filterByNameOrId($name, 'a');

            if (!count($link)) {
                throw new InvalidArgumentException("Could not find a link with a body, name, or ID attribute of [{$name}].");
            }
        }

        $this->visit($link->link()
                          ->getUri());

        return $this;
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

        $command = $this->buildEnvironmentVariables($query) . $this->buildCallToScript();

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

        $this->response = $process->getOutput();

        $this->crawler = new Crawler($this->response, $uri);

        return $this;
    }
}
