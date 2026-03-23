<?php
namespace foire\Controllers;


class Auth extends \TDS\Controller {

    public static function login(){
var_dump('foire Scritch');        
exit();
//        \foire\Controllers\Gen::home();

    }

    public static function alive(){
        exit();
    }

    public static function logout(){
        $app = \TDS\App::get();
        $app::$auth->deconnexion();
    }

    public static function directLink($hex){
        $app = \TDS\App::get();
        $message = $app::$auth->directLink($hex);
        if ( time()-$message->timestamp < (60*60*24)*20000000){  // on limite l'utilisation du lien à 200 jours...
            $app::$auth->forceAuth($message->uid);
            if ($app::$auth->isInBase()){
                header("Location: /texte/introduction");
                exit();
            }
            header("Location: /");
            exit(); 
        }
        var_dump("Après l'heure c'est plus l'heure");
        var_dump("Le lien est périmé mais pourquoi ?");
    }
}
