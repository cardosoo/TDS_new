<?php
namespace base\Controllers;

use base\Struct;
use stdClass;

function p($num){
    return str_replace(".",",",$num);
}


class PlanningController extends \TDS\Controller {

    public static function home(){
        $app = \TDS\App::get();

        echo $app::$viewer->render('planning/index.html.twig');
    }

    public static function detailsEnseignements(){
        $app = \TDS\App::get();

        $enseignementNS = $app::NS('Enseignement');
        $enseignementList = $enseignementNS::loadWhere('actif');
        

        $app::$cmpl["withJQuery"]=true;
        $app::$cmpl["withDataTables"]=true;

        echo $app::$viewer->render('planning/detailsEnseignements.html.twig', ['L' => $enseignementList]);
    }

    /*
    public static function getListeID($cursus='Licence 1', $periode='1', $composante='UFR Physique'){
        $app = \TDS\App::get();


        $periode = "{{$periode}}";
        $idL = $app::$db->getAll("
            SELECT ES.id from enseignement_structure as ES
            LEFT JOIN enseignement as E on E.id = ES.id
            WHERE ES.cursus = '{$cursus}'
            AND composante LIKE '%{$composante}%'
            AND periode = '{$periode}'
            AND E.actif
            ORDER BY ES.code
        ");

        $idList = [];
        foreach($idL as $id){
            $idList[] = $id->id;
        }
        return '('.join(',', $idList).')';
    }
    */
    /*
    public static function getStructurelist($listeID, $serie=0){
        $app = \TDS\App::get();

        $offset = $serie*8;

        $structureList = $app::$db->getAll("
            SELECT DISTINCT 
                SE.cursus as cursus_id, 
                C.nom as cursus, 
                SE.maquette as maquette_id, 
                M.nom as maquette, 
                SE.etape as etape_id, 
                E.nom as etape
            FROM structure_enseignement as SE
            LEFT JOIN cursus AS C ON C.id = SE.cursus
            LEFT JOIN maquette AS M ON M.id = SE.maquette
            LEFT JOIN etape AS E ON E.id = SE.etape
            WHERE SE.enseignement in {$listeID}
            LIMIT 8 OFFSET {$offset}
        ");
//var_dump($listeID);
//var_dump($structureList);        
        $SL = [];
        $col = 'H';
        foreach($structureList as $S){
            $SL[$S->etape] = $S;
            $S->col = $col++; 
        }
        return $SL;
    }
    */


    public static function echoDetails($g, $s, $d, $n){
        echo ('"'.str_replace('.', ',', $g).'", ');
        echo ('"'.str_replace('.', ',', $s).'", ');
        echo ('"'.str_replace('.', ',', $d).'", ');
        echo ('"'.str_replace('.', ',', $n).'", ');
    }


/*
    public static function detailsAPI($cursus, $composante, $periode, $serie=0){
        $app = \TDS\App::get();

        $fmt = new \IntlDateFormatter(
            'fr-FR',
            \IntlDateFormatter::FULL,
            \IntlDateFormatter::FULL,
            'Europe/Paris',
            \IntlDateFormatter::GREGORIAN
        );

        echo("Exportation depuis {$app::$baseName}, le {$fmt->format(time())}\n");

        $cursus = urldecode($cursus);
        $composante = urldecode($composante);


        $listeID = self::getListeID($cursus, $periode, $composante);
        $SL = self::getStructureList($listeID, $serie);

//var_dump($SL);

        echo '"id", "ECUE", "nuac", "Enseignement", "nbetu", ';
        echo '"g_cm", "s_cm", "d_cm", "n_cm", ';
//        echo '"g_ctd", "s_ctd", "d_ctd", "n_ctd", ';
        echo '"g_td", "s_td", "d_td", "n_td", ';
        echo '"g_tp", "s_tp", "d_tp", "n_tp", ';
        foreach($SL as $S){
            echo "\"{$S->etape}\", ";
        }
        echo "\n";

        $EL = $app::NS('Enseignement')::loadWhere("id in {$listeID}");
        foreach($EL as $E){
            $code = "";
            foreach($SL as $S){
                $S->nbEtu = 0;
            }
            $netu =0;
            $found = false;
            foreach($E->ecueList as $ecue){
                $etape = $ecue->ue->semestre->etape;
                if (!empty($ecue->code) && false===strstr($code, $ecue->code)){
                    $code .= ($code==''?'':'|').$ecue->code;
                }
                if (isset($SL[$etape->nom])){
                    $SL[$etape->nom]->nbEtu = $ecue->nbetu();
                    $found = true;
                }
                $netu += $ecue->nbetu();
            }
            if ($found){
                echo ("\"{$E->id}\", ");
                echo ("\"{$code}\", ");
                echo ("\"{$E->nuac}\", ");
                echo ("\"{$E->nom}\", ");
                echo ('"'.str_replace('.', ',', $netu).'", ');
                self::echoDetails($E->cm, $E->s_cm, $E->d_cm, $E->n_cm);
                self::echoDetails($E->ctd +$E->td, $E->s_ctd + $E->s_td, $E->d_ctd + $E->d_td, $E->n_ctd + $E->n_td);
    //            self::echoDetails($E->ctd, $E->s_ctd, $E->d_ctd, $E->n_ctd);
    //            self::echoDetails($E->td, $E->s_td, $E->d_td, $E->n_td);
                self::echoDetails($E->tp, $E->s_tp, $E->d_tp, $E->n_tp);
                foreach($SL as $S){
                    echo ('"'.str_replace('.', ',', $S->nbEtu).'", ');
                }
                echo "\n";
                echo ("\"{$E->id}\", ");
                foreach($E->voeuList as $V){
                    if ($V->correspondant){
                        echo ("\"{$V->personne->email}\", ");
                    }
                }
                echo "\n";
            }
        }
    }
*/
    private static function extractEtapeList($enseignementList){
        $app = \TDS\App::get();


        $etapeList = [];
        foreach($enseignementList as $E){
            $ecue = $E['ecue'];
            foreach($ecue->getEtapes() as $ET){
                $etapeList[$ET->getCode()] = $ET;
            }
        }
        return $etapeList;
    }

    private static function getNbEtuECUE($code){
        $app = \TDS\App::get();
        $ldap = new \TDS\LDAPExtern();

        $liste = $ldap->list("(&(supannEtuAnneeInscription={$app::$currentYear})(supannEtuElementPedagogique=*{$code}*))", ['uid'], 1000);
        return $liste->count;
    }

    private static function getNbEtuEtape($code){
        $app = \TDS\App::get();
        $ldap = new \TDS\LDAPExtern();

        $liste = $ldap->list("(&(supannEtuAnneeInscription={$app::$currentYear})(supannEtuEtape=*{$code}*))", ['uid'], 1000);
        return $liste->count;
    }

    private static function getNbEtuEtapeECUE($codeEtape, $codeECUE){
        $app = \TDS\App::get();
        $ldap = new \TDS\LDAPExtern();

        $liste = $ldap->list("(&(supannEtuAnneeInscription={$app::$currentYear})(supannEtuElementPedagogique=*{$codeECUE}*)(supannEtuEtape=*{$codeEtape}*))", ['uid'], 1000);
    // var_dump(['code'=> 'PH24E055', 'count' => $liste->count, 'liste' => $liste]);
        return $liste->count;
    }

    private static function prepareDetailsList($enseignementList, $etapeList){
        $app = \TDS\App::get();
        
        $ldap = new \TDS\LDAPExtern();
        $detailsList = [];
        foreach($enseignementList as $EL){
            $E = $EL['enseignement'];
            $ecue = $EL['ecue'];
            $elm = new stdClass();
            $elm->id = $E->id;
            $elm->code = $E->code;
            $elm->variante = $E->variante;
            $elm->nom = $E->nom;
            // attention, ici il faut peut-être faire quelque chose pour traiter les code multiples
            $codeList = explode('|', $E->code);
            $nbetu = 0;
            foreach($codeList as $code){
                $nbetu += self::getNbEtuECUE($code);
            }
            $elm->nbetu = $nbetu;
            $elm->g_cm = p($E->cm);
            $elm->s_cm = p($E->s_cm);
            $elm->d_cm = p($E->d_cm);
            $elm->n_cm = p($E->n_cm);
            $elm->g_td = p($E->td   + $E->ctd);
            $elm->s_td = p($E->s_td + $E->s_ctd);
            $elm->d_td = p($E->d_td + $E->d_ctd);
            $elm->n_td = p($E->n_td + $E->n_ctd);
            $elm->g_tp = p($E->tp);
            $elm->s_tp = p($E->s_tp);
            $elm->d_tp = p($E->d_tp);
            $elm->n_tp = p($E->n_tp);
            $elm->mail = join(',', $E->getMailCorrespondantList());
            $elm->etapeList = [];
            $etapeListList = $E->getStructEtapeList();
/* var_dump([
    'code' => $E->code,
    'etapes' => $etapeListList,
]); */
            foreach($etapeListList as $codeECUE => $etapeList){
                foreach($etapeList as $etape){
                    $codeEtape = $etape->getCode();
                    $nbetu = self::getNbEtuEtapeECUE($codeEtape, $codeECUE);
                    // Si il n'y a aucun étudiant inscrit cela veut sans doute dire que les inscriptions ne sont pas encore faites. 
                    // Dans ce cas, on reporte le nobmre d'étudiants inscrits à l'étape
                    if ($nbetu ==0){
                        $nbetu = self::getNbEtuEtape($codeEtape);
                    }
                    if (! isset($elm->etapeList[$codeEtape])) {
                        $elm->etapeList[$codeEtape] = 0;
                    }
                    $elm->etapeList[$codeEtape] += $nbetu;
                }
            }
            $detailsList[] = $elm;
        }

        return $detailsList;
    }

    private static function getEnseignementListForDetails($cursusName, $structureName, $periode){
        $app = \TDS\App::get();


        $cursusList = Struct::getCursusList();
        $cursusID = 0;
        foreach($cursusList as $cursus){
            if ($cursus->nom == $cursusName){
                $cursusID = $cursus->id;
            }
        }

        $struct = new Struct();
        $structure = $struct->getStructureByNom($structureName);
        $structureID = $structure->getId();

        $modalite  = [];
        $selectors = [
            'cursus'    => [$cursusID],
            'semestre'  => [$periode],
            'structure' => [$structureID], // [35],
            'etape'     => [],
            'modalite'  => [],
           'actif'      => true
        ];

        $with = new \stdClass;
        $with->withInactif        = false;
        $with->onlyInactif        = false;
        $with->withSousEffectif   = false;
        $with->nonPrioritaire     = false;
        $with->withStructure      = false;
        $with->withEtape          = false;
        $with->withEquipe         = false;
        $with->withCorrespondant  = false;
        $with->withDomaine        = false;


        $searchValue="";

        $enseignementList = $app::NS('Enseignement')::search($searchValue, $selectors , $modalite, $with);
        return $enseignementList;
    }
 
    public static function detailsAPI($cursus, $composante, $periode, $serie=0){
        $app = \TDS\App::get();
        /*
        var_dump([
            'cursus' => $cursus,
            'composante' => $composante,
            'periode' => $periode,
            'serie' => $serie,
        ]);
        */
        
        $fmt = new \IntlDateFormatter(
            'fr-FR',
            \IntlDateFormatter::FULL,
            \IntlDateFormatter::FULL,
            'Europe/Paris',
            \IntlDateFormatter::GREGORIAN
        );

        $cursusName = urldecode($cursus);
        $structureName = urldecode($composante);


        header("Access-Control-Allow-Origin: *");
        

        $enseignementList = self::getEnseignementListForDetails($cursusName, $structureName, $periode);
        $etapeList = self::extractEtapeList($enseignementList);
        $detailsList = self::prepareDetailsList($enseignementList, $etapeList);

        $options=[
            'DL' => $detailsList,
            'EL' => $etapeList,
        ];
        echo("Exportation depuis {$app::$baseName}, le {$fmt->format(time())}\n");
        echo $app::$viewer->render("planning/detailsAPI.html.twig", $options);

    }
}