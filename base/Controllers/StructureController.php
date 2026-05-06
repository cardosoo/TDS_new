<?php

namespace base\Controllers;

use stdClass;
use Structure;
use StructureQuery;
use Etape;
use EtapeQuery;
use ECUE;
use ECUEQuery;
use ecue_etape;
use ecue_etapeQuery;
use \Propel\Runtime\ActiveQuery\Criteria;

/**
 * Travail préparatoire pour remplacer la partie Ajout à la structure des enseignements commune à l'établissement
 * par une partie ajout qui elle est définie au niveau de la base de données.
 * L'idée est de faire une table qui permet de concrétiser cet ajout au niveau de la base de données
 * et de permettre une procédure utilisable par l'administrateur de l'appli d'ajouter une entrée qui permet d'associer un e
 * nseignement donnés à une étape particulière.
 * la table en question fait simplement la jonction entre une étape et un code ecue (cette partie là est simple).
 * Cela suppose que toutes les étapes sont déjà définies défines dans la structure des enseignements.
 * 
 * 
 * Le challenge, c'est de faire en sorte que les différentes fonctions qui permettent d'interroger la structure des enseignements
 *  puissent également utiliser cette table de liaison.
 * 
 * le tag de marquage à utiliser pour les fonctions qui font partie de ce travail préparatoire est // #OC_structure-ajout  
 * (pour pouvoir faire le tri facilement dans le code et faire le ménage une fois que tout est en place).
 */


class StructureController extends \TDS\Controller {

    public static function home(){
        $app = \TDS\App::get();   
        $struct = new \base\Struct(); 

        $structureList = $struct->getStructureList();

        $app::$cmpl["withJQuery"]=true;
        echo $app::$viewer->render('structure/home.html.twig', ['structureList' => $structureList]);
    }

    public static function structure($nom){
        $app = \TDS\App::get();
        $struct = new \base\Struct();

        $nom = urldecode($nom); 
        $structure = $struct->getStructureByNom($nom);
        
        $app::$cmpl["withJQuery"]=true;
        echo $app::$viewer->render('structure/structure.html.twig', ['structure' => $structure, 'nom' => $nom]);
    }

    public static function etape(string $code){
        $app = \TDS\App::get();
        $struct = new \base\Struct();

        $etape = $struct->getEtapeByCode($code);
        $ecueList = $etape->getEcueList();
        $inBase = $struct->getInBaseEnseignementFromEcueList($ecueList);

        list($in, $out) = $struct->getInOutFromEcueList($ecueList, $inBase);

        $struct->addVacation($out);
        $struct->addVacation($in);

        $app::$cmpl["withJQuery"]=true;
        echo $app::$viewer->render('structure/etape.html.twig', ['etape' => $etape, 'IL' => $in, 'OL' => $out]);
    }

    public static function ecue(string $code){
        $app = \TDS\App::get();
        $struct = new \base\Struct();

        $ecue = $struct->getECUEByCode($code);
        
        $app::$cmpl["withJQuery"]=true;
        echo $app::$viewer->render('structure/ecue.html.twig', ['ecue' => $ecue]);
    }

    public static function addEnseignement(string $code){
        $app = \TDS\App::get();
        $struct = new \base\Struct();



        if (strlen($code)!=8){
            $app::$pub->warning[] = "Cela ne ressemble pas à un code ECUE";
            $app::$router->redirect("/{$app::$appName}/structure/ecue/{$code}");
            exit();
        }
        $ecue = $struct->getECUEByCode($code);
        if (is_null($ecue)){
            $app::$pub->warning[] = "Ce code ECUE ne fait pas partie de la structure des enseignements répertoriées";
            $app::$router->redirect("/{$app::$appName}/structure/ecue/{$code}");
            exit();
        }
        if (!$ecue->canCreateEnseignementInDatabase() ){
            $app::$pub->warning[] = "Vous n'êtes pas autorisé à ajouter un enseignement";
            $app::$router->redirect("/{$app::$appName}/structure/ecue/{$code}");
            exit();
        }


        $EList = $app::NS('Enseignement')::loadWhere(" code LIKE '%{$code}%'");
        if (count($EList)!=0){
            $app::$pub->warning[] = "Il y a déjà un enseignement avec cet ECUE créé";
            $app::$router->redirect("/{$app::$appName}/structure/ecue/{$code}");
            exit();
        }

        // tout est bon, mais il faut peut-être une étape de validation
        $E = $ecue->createEnseignementInDatabase();
        // on ajoute un truc pour dire que la personne qui a demandé la création du truc y participe et est responsable de cet enseignement.
        $voeuNS = $app::NS("Voeu");
        $V = new $voeuNS;
        $V->personne = $app::$auth->user->id;
        $V->enseignement = $E->id;
        $V->correspondant = true;
        $V->save();

        $app::$cmpl["withJQuery"]=true;
        $app::$router->redirect("/{$app::$appName}/enseignement/{$E->id}");
        // echo $app::$viewer->render('structure/addEnseignement.html.twig', ['ecue' => $ecue]);
    }



    // importation du fichier extractionYYYY.csv 
    // M00; M27; M30; S30; S31; S32; S34; S36; IPG; H41; H43; H44; H46; H48; H49; S51; H50; P55; S56; S75; S76; H71; H78; H77; P89; P94; 097; H16; S08; M11; S05; H14; H25; IFD; USI; UNI 
    // S05 (UFR de math/info)
    // S75 (IUT Pajol)
    // H43 (UFR GHES)
    // IPG (IPGP-DEFD)
    // H77 (IHSS)

    public static function importExtraction(){
        $app = \TDS\App::get();

        $struct = new \base\Struct($app::$currentYear);
        $struct->importExtraction();

        var_dump("L'importation de l'extraction {$app::$currentYear} est terminée.");
    }

    public static function importMutualisation(){
        $app = \TDS\App::get();

        $struct = new \base\Struct($app::$currentYear);
        $struct->importMutualisation();

        var_dump("L'importation de la mutualisation {$app::$currentYear} est terminée.");
    }

    public static function importAjout(){
        $app = \TDS\App::get();

        $struct = new \base\Struct($app::$currentYear);
        $struct->importAjout();

        var_dump("L'importation de l'ajout {$app::$currentYear} est terminée.");
    }

    public static function correctionEtape(){
        $app = \TDS\App::get();

        $struct = new \base\Struct($app::$currentYear);
        $struct->correctionEtape();

        var_dump("La correction des étapes  {$app::$currentYear} est terminée.");
    }


    public static function importStructure(){
var_dump("Pour l'instant on ne fait rien ici...");
    }


}

