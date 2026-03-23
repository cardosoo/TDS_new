<?php
namespace tssdv\Controllers;


class respDiplomeController extends \TDS\Controller {

    public static function home(){
        $app = \TDS\App::get();

        echo $app::$viewer->render('respDiplome/index.html.twig');
    }
    
}
