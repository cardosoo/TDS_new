<?php
namespace tssdv;

use \tssdv\Model\User;

class App extends \base\App {
    static string $service = 'UPCite';
    static string $structure = 'UPCite';

    static $etatTS = [];


    public static function getRoleList($personne){
        $app = \TDS\App::get();


        $roleList = parent::getRoleList($personne);
        if (($personne->id==100) && (filter_input(INPUT_GET,"s")=="s" ) )  {
            $roleList= [];
        } 

       
        // en plus des rôles ordinaires on regarde si l'utilisateur est responsable d'UE
        $enseignementList = $app::$auth->user->getEnseignementResponsable();

        if (count($enseignementList) >0){
            $roleList['respUE'] = true;
        }

        $domaineList = $app::$auth->user->domaine_responsableList;
        if (count($domaineList) >0){
            $roleList['respDomaine'] = true;
        }

        return $roleList;
    }

}