<?php
namespace TDS;

//var_dump($_SESSION);
if ( file_exists('maintenance.php_ooo')){
    $deb = false;

    if (  
           ($_SERVER['REMOTE_ADDR'] == '81.194.35.226') 
        && ($_SERVER['HTTP_X_FORWARDED_FOR'] == '172.27.1.120')
    ){
        $deb = true;
    }
    
    if ($deb){
        ini_set('display_errors', '1');
        error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
        ini_set('xdebug.overload_var_dump','2');
        ini_set('xdebug.var_display_max_children','128');
        ini_set('xdebug.var_display_max_data','2048');
        ini_set('xdebug.var_display_max_depth','10');
        ini_set('xdebug.collect_includes','1');  //   ; Noms de fichiers
        ini_set('xdebug.collect_params','2');    //   ; Paramètres de fonctions / méthodes
        ini_set('xdebug.show_exception_trace','0');    
    } else {
        include('maintenance.php');
        exit();
    }
}


//  Les classes à inclure de toute façon et qui ne peuvent pas passer par l'autoload
require_once("../TDS/Classes/App.php");

//set_error_handler("TDS\App::errorHandler");

App::init(getcwd());
$app = App::get();
$app::reopenSession();
//var_dump($_SESSION, $app::$appName, $app::$pub);

// Cela c'est pour les modules installés avec composer pour l'application globale
// Je ne suis pas certain qu'on puisse faire en sorte qu'il y ait aussi un import
// de modules spécifique aux applications... à voir   
/*
spl_autoload_register(function ($class) {
    var_dump("Chargement de la classe : $class\n");
}, true, true);
*/

require_once $app::$basePath.'/../vendor/autoload.php';

include($app::$pathList['base']."/config.php");


$app::setPermission();
$app::initViewer();

require_once("../TDS/Classes/AltoRouter.php"); // on a besoin de cela ou alors il faut faire une entrée spéciale dans l'autoload... à voir ce qui est préférable
$app::initRouter();


include($app::$pathList['base']."/router.php");
$app::$router->buildRoutes();
$app::$router->doMatch();

