<?php
namespace tssdv;

use \tssdv\Model\User;

use ECUE;

class Struct extends \base\Struct {

    /**
     * 
     * Les admins et Les responsables des parcours auxquels l'enseignement est attaché 
     * peuvent ajouter l'enseignement dans la base de données.
     * 
     */
    public function canCreateEnseignementInDatabase(ECUE $ecue){
        $app = \TDS\App::get();

        if (!$app::$auth->isAuth) return false; // si pas d'authentification alors non
        if (!$app::$auth->user->actif)  return false; // si pas actif alors non
        if ($app::$auth->hasRole('Admin')) return true;
        return true;
    }


}