<?php
use TDS\App;
use TDS\Model;

$params = getopt ("a:u:" ,["app:", "uid:"] );
$appName = $params['a'] ?? $params['app'] ?? null;
$uid = $params['u'] ?? $params['uid'] ?? null;


if (is_null($appName)){
    var_dump("Le paramètre -a appName  ou --app=appName est requis pour spécifier l'application");
    exit();
}
if (is_null($uid)){
    var_dump("Le paramètre -u uid  ou --uid=uid est requis pour spécifier l'utilisateur");
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

require_once("../TDS/Classes/AltoRouter.php"); // on a besoin de cela ou alors il faut faire une entrée spéciale dans l'autoload... à voir ce qui est préférable
$app::initRouter();

include($app::$pathList['base']."/router.php");
$app::$router->buildRoutes();

$app = App::get();


$P = $app::NS('Personne')::loadOneWhere("uid = '{$uid}'");

$app::$auth->data = new stdClass();
$app::$auth->data->uid = $P->uid;
$app::$auth->uid = $P->uid; 
$app::$auth->data->method = 'Console';
$app::$auth->data->displayname = 'depuis la console';


$directLink = $P->getDirectLink();

print("/{$app::$appName}/directLink/{$directLink}\n");

