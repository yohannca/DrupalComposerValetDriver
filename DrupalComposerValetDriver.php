<?php

class DrupalComposerValetDriver extends ValetDriver
{
    /**
    * The public web directory.
    *
     * @var string
     */
    protected $docroot = '/web';

    /**
     * Determine if the driver serves the request.
     *
     * @param  string  $sitePath
     * @param  string  $siteName
     * @param  string  $uri
     * @return void
     */
    public function serves($sitePath, $siteName, $uri)
    {
        return file_exists($sitePath . $this->docroot . '/core/lib/Drupal.php');
    }

    /**
     * Determine if the incoming request is for a static file.
     *
     * @param  string  $sitePath
     * @param  string  $siteName
     * @param  string  $uri
     * @return string|false
     */
    public function isStaticFile($sitePath, $siteName, $uri)
    {
        $publicPath = $sitePath . $this->docroot . $uri;

        if (file_exists($publicPath) && !is_dir($publicPath) && pathinfo($publicPath)['extension'] != 'php') {
          return $publicPath;
        }

        return false;
    }

    /**
     * Get the fully resolved path to the application's front controller.
     *
     * @param  string  $sitePath
     * @param  string  $siteName
     * @param  string  $uri
     * @return string
     */
    public function frontControllerPath($sitePath, $siteName, $uri)
    {
        if (!empty($uri) && $uri !== '/') {
          $_GET['q'] = $uri;
        }

        $matches = [];

        if (preg_match('/^\/(.*?)\.php/', $uri, $matches)) {
          $filename = $matches[0];

          if (file_exists($sitePath.$this->docroot.$filename) && ! is_dir($sitePath.$this->docroot.$filename)) {
            $_SERVER['SCRIPT_FILENAME'] = $sitePath.$this->docroot.$filename;
            $_SERVER['SCRIPT_NAME'] = $filename;
            return $sitePath.$this->docroot.$filename;
          }
        }

        // Fallback
        $_SERVER['SCRIPT_FILENAME'] = $sitePath.$this->docroot.'/index.php';
        $_SERVER['SCRIPT_NAME'] = '/index.php';
        return $sitePath.$this->docroot.'/index.php';
    }
}
