<?php
namespace tssdv\Controllers;


class RHEAdminController extends \TDS\Controller {

    public static function home(){
        $app = \TDS\App::get();

        echo $app::$viewer->render('RHEAdmin/index.html.twig');
    } 

}
