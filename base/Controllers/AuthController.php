<?php
namespace base\Controllers;


class AuthController extends \TDS\Controller {

    public static function login(){
        \base\Controllers\GenController::home();
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

        var_dump('DirectLink');
        exit();

        $message = $app::$auth->directLink($hex);


        if ( time()-$message->timestamp < (60*60*24)*30 ){  // on limite l'utilisation du lien à 30 jours...
            $app::$auth->forceAuth($message->uid);
            if ($app::$auth->isInBase()){
                header("Location: /{$app::$appName}/base/texte/introduction");
                exit();
            }
            header("Location: /");
            exit(); 
        }
        var_dump("Après l'heure c'est plus l'heure");
        var_dump("Le lien est périmé");
    }
}
