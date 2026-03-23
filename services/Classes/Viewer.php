<?php
namespace services;

class Viewer extends \foire\Viewer {

    function __construct() {
        parent::__construct();
        $this->loader->prependPath(__DIR__.'/../twig/templates');
    }

    protected function getAppGlobals(){
        $r =  parent::getAppGlobals(); 
        return $r;
    }

}
