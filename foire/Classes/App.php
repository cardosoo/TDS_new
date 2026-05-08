<?php
namespace foire;

use \foire\Model\User;

class App extends \base\App {
    static string $service = 'UPCite';
    static string $structure = 'UPCite';
    public static int $chargeUFR; // cette valeur est mise à jour dans le config.php

    static function setPermission(){
        parent::setPermission();

        $app = \TDS\App::get();
        if ($app::$currentYear != "2026"){
            return;
        }

        if ($app::$auth->isAuth() && !is_null($app::$auth->user)){
            if(in_array(substr($app::$auth->user->statut->nom, 0, 6), ["Vacata", "Ancien"])){
                $app::$auth->forceLogout(); // je ne suis pas certain que cela serve à quelque chose...
                echo $app::$viewer->render('refus/vacataire.html.twig');
                exit();
            }
        }
    }

}
