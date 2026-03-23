<?php
namespace tssdv\Controllers;


class respParcoursController extends \TDS\Controller {

    public static function home(){
        $app = \TDS\App::get();

        echo $app::$viewer->render('respParcours/index.html.twig');
    }
    
}
