<?php
namespace services;

use \services\Model\User;

use ECUE;

class Struct extends \foire\Struct {

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
        // Par défaut seuls slerouge peut ajouter un enseignement
        if ($app::$auth->hasRole('Admin')) return true;

        $etapeList = $ecue->getEtapes();
        foreach($etapeList as $etape){
            foreach($etape->getResponsables() as $personne){
                //var_dump($personne);
                if ($personne->id == $app::$auth->user->id) return true;
            };
        }
        return false;
    }


}