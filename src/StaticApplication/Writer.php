<?php


namespace Extasy\StaticApplication;

use \Faid\Configure\Configure;
use \Faid\Configure\ConfigureException;

class Writer
{
    const AppFolderName = 'StaticApplication/cache/';
    const ConfigureKey = 'StaticApplication.baseDir';

    protected $path = '';

    public function __construct($relativePath)
    {
        $relativePath = explode('://', $relativePath);

        $this->path = self::getCacheFolderPath() . array_pop($relativePath);
        // cleanup from urls
        if ('/' == substr($this->path, -1)) {
            $this->path .= 'index.php';
        }

    }

    public static function getCacheFolderPath()
    {
        try {
            $folder = Configure::read(self::ConfigureKey);
        } catch (ConfigureException $e) {
            $folder = '';
        }

        if (!empty($folder)) {
            return $folder;
        } else {
            return SYS_ROOT . self::AppFolderName;
        }
    }

    public function write($content)
    {

        $basePath = dirname($this->path);

        \DAO_FileSystem::getInstance()->createPath($basePath);
        file_put_contents($this->path, $content);
    }
} 