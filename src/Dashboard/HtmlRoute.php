<?php


namespace Extasy\Dashboard;


use Extasy\Dashboard\Controllers\HtmlRouteController;


class HtmlRoute extends Route
{
    protected $filePath = '';

    public function __construct($config = [])
    {
        if (!isset($config['path'])) {
            throw new \InvalidArgumentException( 'Argument `path` not found');
        }
        $this->filePath = $config['path'];
        parent::__construct($config);
    }

    public function dispatch()
    {
        $result = \CMSAuth::getInstance()->check();
        if ( $result ) {
            $controller = new HtmlRouteController( $this->filePath, $this->request );
            $controller->main();
        }

    }
} 