<?php

namespace Spinen\SimplePhpTester;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

trait Browser
{
    /**
     * @var Crawler
     */
    protected $crawler;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var null|boolean
     */
    protected $successful = null;

    /**
     * @var string
     */
    protected $web_root = 'public';

    /**
     *
     *
     * @param $uri
     *
     * @return $this
     */
    public function visit($uri)
    {
        list($this->path, $query) = array_pad(explode('?', $uri), 2, null);

        $this->determinePath();

        $command = $this->buildEnvironmentVariables() . $this->buildCallToScript() . $this->buildQueryVariables($query);

        $process = new Process($command);
        $process->run();

        if (!$process->isSuccessful()) {
//            throw new ProcessFailedException($process);
            $this->successful = false;

            return $this;
        }

        $this->successful = true;

        $this->crawler = new Crawler($process->getOutput(), $uri);

        return $this;
    }

    /**
     * @return string
     */
    protected function buildCallToScript()
    {
        return $this->phpCgiScriptPath() . ' -f "' . $this->determinedFullPath($this->path) . '"';
    }

    /**
     * @return null|string
     */
    protected function buildEnvironmentVariables()
    {
        // TODO: Figure out how to pass vars in windows
        return null;

        $prefix = '';
        $prepend = ' ';

        if (strncasecmp(PHP_OS, 'WIN', 3) == 0) {
            $prefix = 'SET ';
            $prepend = ';';
        }

        return "${prefix}REQUEST_URI='/${path}'${prepend}${prefix}SCRIPT_NAME='/${path}'${prepend}";
    }

    /**
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
     * @return string
     */
    protected function determinedFullPath()
    {
        return __DIR__ .
               DIRECTORY_SEPARATOR .
               '..' .
               DIRECTORY_SEPARATOR .
               $this->getWebRoot() .
               DIRECTORY_SEPARATOR .
               $this->path;
    }

    /**
     *
     */
    protected function determinePath()
    {
        $this->path = ltrim($this->path, '/');

        if ('' === $this->path) {
            $this->path = "index.php";
        }
    }

    /**
     * @return string
     */
    protected function getWebRoot()
    {
        return trim($this->web_root, "/");
    }

    /**
     * @return mixed
     */
    protected function phpCgiScriptPath()
    {
        return preg_replace("/(php)(\\.exe)?$/um", "$1-cgi$2", (new PhpExecutableFinder)->find(false));
    }

    /**
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
