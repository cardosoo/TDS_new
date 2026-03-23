<?php
namespace tssdv;

use \tssdv\App;
use \TDS\Authenticate;

class Router extends \base\Router {

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

    protected function beforeCall(){
        if (App::$auth->inBase){
            if (App::$auth->isAdmin || App::$auth->isSuperAdmin || (App::$auth->roleList['bureauRHE'] ?? false) ){
                App::$phase = 'saisie';
            }
        }
        
    }

    public static function respUE(){
        $app = \TDS\App::get();
        $app::$router->redirect('/'.$app::$appName.'/respUE');
    }


    public static function respDomaine(){
        $app = \TDS\App::get();
        $app::$router->redirect('/'.$app::$appName.'/respDomaine');
    }

    public static function respParcours(){
        $app = \TDS\App::get();
        $app::$router->redirect('/'.$app::$appName.'/respParcours');
    }

    public static function respDiplome(){
        $app = \TDS\App::get();
        $app::$router->redirect('/'.$app::$appName.'/respDiplome');
    }

    public static function bureauRHE(){
        $app = \TDS\App::get();
        $app::$router->redirect('/'.$app::$appName.'/bureauRHE');
    }

    public static function RHEAdmin(){
        $app = \TDS\App::get();
        $app::$router->redirect('/'.$app::$appName.'/RHEAdmin');
    }

}
