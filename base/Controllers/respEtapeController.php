<?php
namespace base\Controllers;

use stdClass;

class respEtapeController extends \TDS\Controller {

    public static function home(){
        $app = \TDS\App::get();

        echo $app::$viewer->render('respEtape/index.html.twig');
    }




}
