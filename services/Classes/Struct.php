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



    public static function getCursusList(){
        return [
            (object)['id'=> 1, 'nom'=> 'L1', 'filter' => ['type' => 'L', 'niveau' => 1]],            
            (object)['id'=> 2, 'nom'=> 'L2', 'filter' => ['type' => 'L', 'niveau' => 2]],            
            (object)['id'=> 3, 'nom'=> 'L3', 'filter' => ['type' => 'L', 'niveau' => 3]],            
            (object)['id'=> 4, 'nom'=> 'M1', 'filter' => ['type' => 'M', 'niveau' => 1]],            
            (object)['id'=> 5, 'nom'=> 'M2', 'filter' => ['type' => 'M', 'niveau' => 2]],         
            //(object)['id'=> 7, 'nom'=> 'MEEF', 'filter' => ['type' => 'Master Enseignement']],
            (object)['id'=> 6, 'nom'=> 'Agreg', 'filter' => ['type' => 'A', 'niveau' => 'G']],
            (object)['id'=> 7, 'nom'=> 'ING1', 'filter' => ['type' => 'ING', 'niveau' => 1]],
            (object)['id'=> 8, 'nom'=> 'ING2', 'filter' => ['type' => 'ING', 'niveau' => 2]],
            (object)['id'=> 9, 'nom'=> 'ING3', 'filter' => ['type' => 'ING', 'niveau' => 3]],
            (object)['id'=> 10, 'nom'=> 'ING4', 'filter' => ['type' => 'ING', 'niveau' => 4]],
            (object)['id'=> 11, 'nom'=> 'ING5', 'filter' => ['type' => 'ING', 'niveau' => 5]],
            (object)['id'=> 12, 'nom'=> 'PASS', 'filter' => ['type' => 'PAS', 'niveau' => 'S']],
        ];
    }
/*
DAEU
DU
BUT1
BUT3
CPES1
CPES2
LP
Fictif
CPES3
DEUST1
DEUST2
BUT2
DCG1
*/

}