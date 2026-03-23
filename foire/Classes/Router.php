<?php
namespace foire;

class Router extends \base\Router {

    // Ce serait bien de trouver un moyen de rendre ce truc indépendant 
    // de la classe finale... on doit pouvoir s'en sortir à partir du
    // nom de l'appli peut-être mais on va perdre la cascade alors.
    // ou alors si cela se trouve cela fonctionne tel quel ?
    // à voir plus tard.
    protected static function routeAsset($asset){
        $app = \TDS\App::get();
        if ($app::$router->doRouteAsset($asset, __DIR__. "/../assets" )){
            return true;
        }
        return parent::routeAsset($asset);
    }

    public static function callFunc($callback, $args){
        $app = \TDS\App::get();
        
        if ($app::$auth->isAuth &&  in_array($app::$auth->uid, ['magonzal', 'slerouge'])){
        //    $app::$phase='avantAvecStage';
        }
        
        if ($app::$auth->isAdmin){
            // $app::$phase='voeux';
        }

        parent::callFunc($callback, $args);
    }

    public static function CENS(){
        $app = \TDS\App::get();
        $app::$router->redirect('/'.\TDS\App::$appName.'/CENS');
    }



}
