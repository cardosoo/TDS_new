<?php
namespace service;

use \TDS\Route;

$CN = '\\'.__NAMESPACE__.'\\Controllers\\';

include '../base/router.php';

$app = \TDS\App::get();


$app::$router->setNamespace('\\'.__NAMESPACE__.'\\Controllers\\');

// test
$app::$router->updateRoute('test_1', 'TestController::test1');
$app::$router->updateRoute('test_2', 'TestController::test2');
$app::$router->updateRoute('test_3', 'TestController::test3');

$app::$router->routeList['public']['test']['test4']= new Route('GET','/test/test4', 'TestController::test4', 'test_4');

