<?php
namespace tssdv\Controllers;

use TDS\CasExtern;
use \tssdv\App;

class GenController extends \base\Controllers\GenController {

    /**
     * @param string $year
     * 
     * Cette fonction permet de fixer l'année courante d'utilisation
     * Elle ne fonctionne cependant pas très bien dans la mesure où 
     * elle fonctionne uniquement pour les persponnes normalement 
     * identifiées via CAS (c'est le problème de forceAuth() )
     * Il faudrait réfléchir un tant soit peu pour l'améliorer
     * afin de voir comment faire en sorte qu'elle puisse fonctionner
     * pour tout le monde.
     * 
     */
    public static function setCurrentYear(string $year){
        $app = \TDS\App::get();
    
        if (intval($year) <= intval($app::$officialYear)){
            parent::setCurrentYear($year);
            return;
        }
        $app::$auth->forceAuth();
        $roleList = $app::getRoleList($app::$auth->user);
        if (isset($roleList['Admin'])){
            $_SESSION['currentYear']=$year;
            $app::$router->redirect('/');        
        } else {
            die("Rien à faire ici...");
        }
        
        /*
        $app = \TDS\App::get();
        unset($_SESSION['TDS_auth_'.$app::$appName]);
        $_SESSION['currentYear']=$year;
        $app::$auth->forceAuth();
        $app::$router->redirect('/');
        */
    }
    

}