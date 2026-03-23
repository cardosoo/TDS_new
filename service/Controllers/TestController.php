<?php

namespace service\Controllers;

class TestController extends \base\Controllers\TestController {

    private static function showRoutes(){
        $app = \TDS\App::get();

        $app::$cmpl["withJQuery"]=true;
        $app::$cmpl['withDataTables'] = true; 
        echo $app::$viewer->render('test/routeList.html.twig',['routeList' => $app::$router->getRoutes()]);
    }


    public static function test1(){
        // var_dump('désactivation de /service/test/test1'); exit();
        $app = \TDS\App::get();

        // self::showRoutes();
        $path = $app::$pathList['secret']."/data";

        $glob = glob("{$path}/*");
        var_dump($glob);
    }

    public static function test2(){
        // var_dump('désactivation de /service/test/test2'); exit();


    }

    public static function test3(){
        var_dump('désactivation de /service/test/test3'); exit();
    }

    public static function test4(){
        var_dump('désactivation de /service/test/test4'); exit();
    }

}
