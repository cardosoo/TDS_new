<?php
namespace services\Controllers;

use \services\App;

class Auth extends \TDS\Controller {

    public static function login(){
        \services\Controllers\Gen::home();
    }

    public static function alive(){
        exit();
    }

    public static function logout(){
        App::$auth->deconnexion();
    }

    public static function directLink($hex){
        $message = App::$auth->directLink($hex);
        if ( time()-$message->timestamp < (60*60*24)*30 ){  // on limite l'utilisation du lien à 30 jours...
            App::$auth->forceAuth($message->uid);
            if (App::$auth->isInBase()){
                header("Location: /texte/introduction");
                exit();
            }
            header("Location: /");
            exit(); 
        }
        var_dump("Après l'heure c'est plus l'heure");
        var_dump("Le lien est périmé");
    }
}
