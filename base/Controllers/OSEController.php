<?php

namespace base\Controllers;

use base\Model\Voeu;

class OSEController extends \TDS\Controller {

    /*****************************************************
     * Comparaison entre le listing des service des OSE (listingServices.csv) 
     * et la table des voeux de TS
     * 
     * 
     * 
     */
    public static function comparaison(){
        $app = \TDS\App::get();
        echo $app::$viewer->render('OSE/comparaison.html.twig');
    }


    /*******
     * Pour la comparaisonFromBD, on passe successivement tous les voeux présents dans la base de données
     * et on regarde si on les trouve dans OSE
     * Si on les trouve, on compare les volumes horaires
     * 
     */
    public static function comparaisonFromDB(){
        $app = \TDS\App::get();
        $voeuList = ($app::NS('Voeu'))::loadWhere("actif and id>0", ['personne']);
        
        $rn = []; // il doit y avoir plusieurs ECUE ici (il faudrait vérifier)...
        $r0 = []; // pour mettre les choses pour lesquelles il n'y a pas de correspondance
        $r1 = []; // pour mettre les choses pour lesquelles il y a exactement une correspondance
        $r2 = []; // pour mettre les choses pour lesquelles il y a plus d'une correspondance (une même ECUE mais sur plusieurs semestre ?)

        foreach($voeuList as $V){
            $t = $V->compareWithOSE();
            if ($t=== true){

            } else {
                if (count($t)> 1){ // je pense que cela se produit lorsqu'un enseignement est relié à plusieurs ECUE mais il faut vérifier
                    $rn[] = [
                        'DB' => $V,
                        'OSE' => $t,   
                    ];
                } else {
                    switch (count($t[0])) {
                        case 0:
                            $r0[] = $V;
                            break;
                        case 1:
                            $r1[] = [
                                'DB' => $V,
                                'OSE' => $t[0][0],   
                            ];
                            break;
                        default:
                        $r2[] = [
                            'DB' => $V,
                            'OSE' => $t,   
                        ];
                        break;
                    }
                }
            }
        }

        $app::$cmpl["withJQuery"]=true;
        $app::$cmpl["withDataTables"]=true;

        echo $app::$viewer->render('OSE/comparaisonFromDB.html.twig', [
           'rn' =>$rn, 
           'r0' => $r0, 
           'r1' =>$r1, 
           'r2' =>$r2,
        ]);


    }

    public static function comparaisonFromOSE(){
        $app = \TDS\App::get();


        $OSENS = (\TDS\App::$appName)."\OSE";
        $OSE = new $OSENS;

        $noPersonne=[];       // pour indiquer les trucs où le code OSE de la personne n'est pas présent dans DB
        $noEnseignement=[];   // pour indiquer les trucs où le code ECUE de l'enseignement n'est pas présent dans DB
        $noNo = [];           // pour indiquer les trucs pour lesquels il n'y a ni le code OSE de la personne ni le code ECUE de l'enseignement dans DB
        $noVoeu = [];         // pour indiquer les trucs pour lesquels il n'y a de voeu
        $moreVoeu = [];        // pour indiquer qu'il y a plus d'un voeu associé
        $badVolume = [];      // pour indiquer les trucs pour lesquels les volumes dans les voeux ne sont pas les mêmes que les volumes dans DB
        $badFullVolume = [];  // pour indiquer les trucs pour lesquels le volume global du voeu ne correspond pas

        $t = $OSE->readNextService();
        while ($t !== false){

            $ose = $t['ose'];
            $ecue = $t['ecue'];

            if ($ecue == ''){ // alors c'est un élément du référentiel, il faudrait faire un traitement spécial
                $EL = $t;
            } else {
                $personne = $app::NS("Personne")::loadOneWhere("ose= '{$ose}'");
                $EL = $app::$db->getAll("
                    SELECT DISTINCT
                        SE.enseignement as id, 
                        SE.code_ecue as ecue
                    FROM  structure_enseignement as SE
                    WHERE SE.code_ecue = '{$ecue}' 
                ");

                $isPersonne = $personne !== null;
                $isEnseignement = count($EL)>0;
                $isFull = $isPersonne && $isEnseignement;
                $isNo = !($isPersonne || $isEnseignement);

                if ($isFull){ // alors personne et enseignement sont connus on peut chercher le voeu
                    $idList = [];
                    foreach($EL as $E ){
                        $idList[]=$E->id;
                    }
                    $idList = join(', ', $idList);
                    $voeuList = $app::NS("Voeu")::loadWhere("personne= {$personne->id} AND enseignement in ({$idList})");
                    if (count($voeuList)===0){
                        $noVoeu[]=$t;
                    } else {
                        if (count($voeuList)===1){
                            // on regarde dabord de façon globale le volume horaire correspondant à la ligne
                            if ($voeuList[0]->compareGlobalWithOSE() !== true){
                                $badFullVolume[] = ['fromOSE' => $t, 'voeu' => $voeuList[0]];
                            }            
                            // puis on compare pour chacune des modalités
                            if ($voeuList[0]->compareWithOSE() !== true){
                                $badVolume[] = ['fromOSE' => $t, 'voeu' => $voeuList[0]];
                            }            
                        } else {
                            $moreVoeu[]= ['fromOSE' => $t, 'voeuList' => $voeuList];
                        }
                    }                
                } else { // alors soit la peronne soit l'enseignement (ou les 2) est inconnu
                    if ($isNo){
                        $noNo[]= $t;
                    } else {
                        if ($isPersonne){
                            $noEnseignement[]=$t;
                        } else {
                            $noPersonne[]=$t;
                        }
                    }
                }

            }
            $t = $OSE->readNextService();
        }
        $app::$cmpl["withJQuery"]=true;
        $app::$cmpl["withDataTables"]=true;

        echo $app::$viewer->render('OSE/comparaisonFromOSE.html.twig', [
            'noPersonne' => $noPersonne,       // pour indiquer les trucs où le code OSE de la personne n'est pas présent dans DB
            'noEnseignement' => $noEnseignement,   // pour indiquer les trucs où le code ECUE de l'enseignement n'est pas présent dans DB
            'noNo' => $noNo,           // pour indiquer les trucs pour lesquels il n'y a ni le code OSE de la personne ni le code ECUE de l'enseignement dans DB
            'noVoeu' => $noVoeu,         // pour indiquer les trucs pour lesquels il n'y a de voeu
            'moreVoeu' => $moreVoeu,        // pour indiquer qu'il y a plus d'un voeu associé
            'badVolume' => $badVolume,      // pour indiquer les trucs pour lesquels les volumes dans les voeux ne sont pas les mêmes que les volumes dans DB
            'badFullVolume' => $badFullVolume,      // pour indiquer les trucs pour lesquels le volume global du voeu ne correspond pas
        ]);

    }


    public static function bilanPersonneList(){
        $app = \TDS\App::get();

        $personneList = $app::NS('Personne')::loadWhere('actif');

        $app::$cmpl["withJQuery"]=true;
        $app::$cmpl["withDataTables"]=true;
        
        echo $app::$viewer->render('OSE/bilanPersonneList.html.twig', ['PList' => $personneList]);

    }


    /******************************************
     * Récupère la liste des id des enseignements pour lesquels il y a des voeux
     * 
     */
    public static function getIdEnseignementListFromDB(){
        $app = \TDS\App::get();
        $tmp = $app::$db->getAll("
            SELECT DISTINCT
                V.enseignement as id
            FROM voeu as V
            WHERE V.id >0
            AND V.actif
        ");

        $idList = [];
        foreach($tmp as $t){
            $idList[]=$t->id;
        }
        return $idList;
    }

    public static function getEnseignementListFromDB(){
        $app = \TDS\App::get();
        
        $idList = self::getIdEnseignementListFromDB();
        $idList = join(",",  $idList);
        return  ($app::NS('Enseignement'))::loadWhere(" id in ({$idList}) ");
    }

}
