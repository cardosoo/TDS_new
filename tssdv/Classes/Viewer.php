<?php
namespace tssdv;

class Viewer extends \base\Viewer {

    function __construct() {
        parent::__construct();
        $this->loader->prependPath(__DIR__.'/../twig/templates');
    }

    protected function getAppGlobals(){
        $r =  parent::getAppGlobals(); 
        $r['etatTS'] = App::$etatTS;
        return $r;
    }

}
