<?php


namespace Extasy\Dashboard\Controllers;

use Faid\Request\HttpRequest;
use Faid\View\View;

class HtmlRouteController extends \adminPage {

    /**
     * @var null
     */
    protected $view = null;
    public function __construct( $tplPath, HttpRequest $request ) {
        parent::__construct();
        $this->view = new View( $tplPath );

        $this->view->set( 'request',$request );
    }
    public function main() {

        print $this->view->render();
        $this->output();
    }
} 