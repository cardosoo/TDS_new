<?php
namespace zeroU;

use \zeroU\App;
use \TDS\Authenticate;

class Router extends \TDS\Router {

    // Ce serait bien de trouver un moyen de rendre ce truc indépendant 
    // de la classe finale... on doit pouvoir s'en sortir à partir du
    // nom de l'appli peut-être mais on va perdre la cascade alors.
    // ou alors si cela se trouve cela fonctionne tel quel ?
    // à voir plus tard.
    protected static function routeAsset($asset){
        if (App::$router->doRouteAsset($asset, __DIR__. "/../assets" )){
            return true;
        }
        return parent::routeAsset($asset);
    }

}
