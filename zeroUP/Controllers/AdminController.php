<?php
namespace zeroUP\Controllers;


class AdminController extends \TDS\Controller {

    public static function home(){
        $app = \TDS\App::get();

        echo $app::$viewer->render('admin/index.html.twig');
    }
}
