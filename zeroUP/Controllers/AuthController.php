<?php
namespace zeroUP\Controllers;

use \zeroUP\App;

class AuthController extends \TDS\Controller {

    public static function login(){
        \zeroUP\Controllers\GenController::home();
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
        if ( time()-$message->timestamp < (60*60*24)*150 ){  // on limite l'utilisation du lien à 150 jours...

            $app::$auth->forceAuth($message->id);
            if ($app::$auth->isInBase()){
//                header("Location: ".$app::$router->generate("texte", ['t' => 'introduction']));
                header("Location: /{$app::$appName}/texte/introduction");
                exit();
            }
            header("Location: /{$app::$appName}/");
            exit(); 
        }
        var_dump("Après l'heure c'est plus l'heure");
        var_dump("Le lien est périmé mais...");
    }

    


}
