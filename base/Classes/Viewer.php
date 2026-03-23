<?php
namespace base;

class Viewer extends \zeroUP\Viewer {

    function __construct() {
        parent::__construct();
        $this->loader->prependPath(__DIR__.'/../twig/templates');
    }

    protected function getAppGlobals(){
        $app = \TDS\App::get();

        $r =  parent::getAppGlobals();
        $r['hETD']= $app::$hETD;
        $r['phaseList'] = $app::$phaseList;
        $r['phase'] = $app::$phase;
        $r['texte'] = $app::$texte;
        $r['mail'] = $app::$mail;
        $r['perm'] = $app::$perm;
        $r['setURL'] = $app::$setURL;
        
        return $r;
    }

}
