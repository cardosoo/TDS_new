<?php
namespace base\Controllers;

use stdClass;

class respAdminController extends \TDS\Controller {

    public static function home(){
        $app = \TDS\App::get();

        echo $app::$viewer->render('respAdmin/index.html.twig');
    }




}
