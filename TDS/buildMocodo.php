<?php 

use TDS\App;
use TDS\Model;
use \TDS\Model\Model as M;

$params = getopt ("a:" ,["app:"] );
$appName = $params['a'] ?? $params['app'] ?? null;


if (is_null($appName)){
    var_dump("Le paramètre -a=appName  ou --app=appName est requis pour spécifier l'application");
    exit();
}

if (! chdir("../{$appName}")){
    var_dump("Il semble que l'application {$appName} n'existe pas.");
    exit();
}

function getallheaders(){
    return [];
}


require_once("../TDS/Classes/App.php");
$_SERVER['PHP_SELF'] = "/{$appName}/local";
$_SERVER["REQUEST_URI"]="/";
$_SERVER["HTTP_HOST"]="{$appName}.fake.url";
$_SERVER['SERVER_ADDR']="192.168.1.81";


App::init(getcwd(), false, false, $appName);
require_once App::$basePath.'/../vendor/autoload.php'; 
include(App::$pathList['base']."/config.php");
include("../{$appName}/model.php");


// var_dump(M::getEntityList());

$mocodo = Model\Model::getMocodo();

echo $mocodo."\n";