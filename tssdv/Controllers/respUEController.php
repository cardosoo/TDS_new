<?php
namespace tssdv\Controllers;

use stdClass;

class respUEController extends \TDS\Controller {

    public static function home(){
        $app = \TDS\App::get();

        echo $app::$viewer->render('respUE/index.html.twig');
    }



    private static function getVoeuxFromEnseignement($id){
        $app = \TDS\App::get();
        $enseignementNS = $app::NS("Enseignement");

        $obj = new stdClass;
        $obj->E = $enseignementNS::load($id);
        $vList = $obj->E->voeuList;            
        $obj->aValider = 0;
        $obj->valide = 0;
        $obj->enAttente = 0;
        foreach($vList as $v){
            switch ($v->etat_ts){
                case 2: 
                    $obj->aValider++;
                    break;
                case 3: 
                    $obj->valide++;
                    break;
                default:
                    $obj->enAttente++;
            }
        }
        return $obj;
    }
    
    /**
     *    Renvoie la liste des trucs à valider
     */
    public static function liste1(){
        $app = \TDS\App::get();

        $personneNS = $app::NS("Personne");
        $P = $personneNS::load($app::$auth->user->id);

        $enseignementIdList = $P->getEnseignementResponsable();

        $eList = [];
        foreach($enseignementIdList as $E){
            $obj = self::getVoeuxFromEnseignement($E->enseignement->id);
            $eList[] = $obj;
        }

        $app::$cmpl['withJQuery'] = true;
        echo $app::$viewer->render('respUE/liste1.html.twig', ['eList' => $eList]) ;
    }


    /**
     * Sort la liste des enseignements dont l'utilisateur est responsable
     * avec l'ensemble de la répartition
     * 
     */
    public static function liste2(){
        $app = \TDS\App::get();

        $personneNS = $app::NS("Personne");
        $P = $personneNS::load($app::$auth->user->id);

        $enseignementIdList = $P->getEnseignementResponsable();

        $eList = [];
        foreach($enseignementIdList as $E){
            $obj = self::getVoeuxFromEnseignement($E->enseignement->id);
            $eList[] = $obj;
        }

        $app::$cmpl["withDataTables"]=true;
        $app::$cmpl['withJQuery'] = true;
        echo $app::$viewer->render('respUE/liste2.html.twig', ['eList' => $eList]) ;
    }


    /**
     * Sort les voeux pour la liste1 
     * pour l'enseignement d'id $id
     * 
     */
    public static function doReloadVoeuxListe1( $id){
        $app = \TDS\App::get();
        $elm = self::getVoeuxFromEnseignement($id);
        echo $app::$viewer->render('respUE/voeuxList1.html.twig', ['elm' => $elm, 'open'=> true]);
    }

    public static function doSaveVoeuxListe1(){
        $app = \TDS\App::get();
        $voeuNS = $app::NS("Voeu");

        foreach($_POST as $id => $action){
            $voeu = $voeuNS::load($id);
            switch ($action){
                case 'validate':
                    $voeu->etat_ts=3;
                    $voeu->save();
                    break;
                case 'delete':
                    $voeu->delete();
                    break;
            }
       }
       echo "Done";
    }

}
