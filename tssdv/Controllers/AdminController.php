<?php
namespace tssdv\Controllers;

use \tssdv\App;

use TDS\Query;

class AdminController extends \base\Controllers\AdminController {

    public static function home(){


        $app = \TDS\App::get();
        echo $app::$viewer->render('admin/index.html.twig');
    }

    public static function allVOeux2(){
        parent::allVOeux2();
    }


    /**
     * @param string $entity
     * @param string $state
     * 
     * @return [type]
     */
    public static function setEditionTS(string $entityName, string $etat){
        $app = \TDS\App::get();
        $etat = $etat=='1';

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT );
        if (! ( is_null($id) || ($id===false) )){
            $entity = $app::NS($entityName)::load($id);
            $entity->etat_ts = $etat;
            $entity->save(); 
        }
        $idList = filter_input(INPUT_POST, 'idList', FILTER_VALIDATE_INT, FILTER_REQUIRE_ARRAY );
        if (! ( is_null($idList) || ($idList===false) )){
            foreach($idList as $id){
                $entity = $app::NS($entityName)::load($id);
                $entity->etat_ts = $etat;
                $entity->save(); 
            }            
        }

        echo('Done');
    }


    public static function configEditionTSEnseignement(){
        $app = \TDS\App::get();

        $domaineList = $app::NS('Domaine')::loadWhere('actif');
        $horsDomaineList =  $app::NS('Enseignement')::getHorsDomaine();

        $app::$cmpl['withJQuery'] = true;

        echo $app::$viewer->render('admin/configEditionTSEnseignement.html.twig', ['domaineList' => $domaineList, 'horsDomaineList' => $horsDomaineList]);        
    }


    public static function configEditionTSPersonne(){
        $app = \TDS\App::get();

        $statutList = $app::NS('Statut')::loadWhere('actif');

        $app::$cmpl['withJQuery'] = true;

        echo $app::$viewer->render('admin/configEditionTSPersonne.html.twig', ['statutList' => $statutList]);        
    }

    // 
    public static function migrationSituations($action=null){
        $app = \TDS\App::get();


        if (!is_null($action)){
            $idList = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT , FILTER_REQUIRE_ARRAY);

            $sql = "";
            foreach($idList as $id){
                if (! is_null(filter_input(INPUT_POST, "id_{$id}"))){
                $sql .="
                        DELETE FROM personne_situation
                        WHERE id = {$id};
                ";
                }
            }
            if ($sql !==""){
                $app::$db->h_query($sql);
            }
        }

        $situationNS = $app::NS('Situation');
        $sList = $situationNS::loadWhere('actif', ['ose']);

        echo $app::$viewer->render('admin/migrationSituationsSommaire.html.twig', ['sList' => $sList]);

    }

    // depuis foire, mais cela a l'air Ok
    public static function migrationReferentiels($action=null){
        $app = \TDS\App::get();

        if (is_null($action)){
            echo $app::$viewer->render('admin/migrationReferentielsSommaire.html.twig', []);
            exit();
        }

        $app::$db->h_query("
            DELETE FROM personne_foncref
            WHERE foncref = 4 /* C'est l'id pour les stages*/
            AND id>0;
        ");
        $deleted = $app::$db->getAffectedRows();
        echo $app::$viewer->render('admin/migrationReferentiels.html.twig', ['deleted'=> $deleted]);

    }

    // depuis la foire, mais on a preque tout supprimé (ancienneté, priorité) et ajouté la remise à 0 de etat_ts
    public static function remise0Voeux($action=null){
        $app = \TDS\App::get();

        if (is_null($action)){
            echo $app::$viewer->render('admin/remise0VoeuxSommaire.html.twig', []);
            exit();
        }
        // permet de remettre à 0 les volumes de voeux qui sont attribuables
        // ainsi que l'état de validation
        $app::$db->h_query("
            UPDATE voeu SET
                actif = TRUE,
                CM = 0,
                CTD = 0,
                TD = 0,
                TP = 0,
                Bonus = 0,
                Extra = 0,
                etat_ts = 1
            WHERE enseignement in (
                SELECT id
                FROM Enseignement
                WHERE id > 0
                AND attribuable
            );
        ");
        $kept = $app::$db->getAffectedRows();

        echo $app::$viewer->render('admin/remise0Voeux.html.twig', ['kept'=>$kept]);
    }

    private static function reports($positif = true){
        $app = \TDS\App::get();

        $test = $positif?"> 0.5":"< -0.5";
        $reportId = $positif?1045:1046;

        $previousYear = $app::$currentYear - 1;
        $baseName = $app::$appName."{$previousYear}";

        $db = new \TDS\Database($baseName, $app::$baseUser, $app::$basePwd, 'localhost' );
        pg_set_client_encoding($db->conn, "UNICODE");


        $RP = $db->getAll("
            SELECT 
                VPB.id,
                round(VPB.heures) as heures
            FROM voeu_personne_bilan as VPB
            WHERE VPB.heures $test;
        ");
        $idList = [];
        $idTab = [];

        foreach($RP as $r){
            $idList[] = $r->id;
            $report = $r->heures;
            if ($report > 40 ){
                $report = 40;
            }
            if ($report < -40 ){
                $report = -40;
            }
            $idTab[$r->id]=$report;
        }
        $idList = join(', ', $idList);

        $req = $app::$db->getAll("
            SELECT
                P.id
            FROM personne as P
            LEFT JOIN statut as S on P.statut = S.id
            WHERE P.id in ({$idList})
            AND (
                   S.nom = 'ATER'
                   OR S.nom LIKE 'Missi%'
                   OR S.nom = 'PROF'
                   OR S.nom = 'MCF'
             )
        ");

        $maxId = $app::$db->getOne("
            SELECT
               max(id) as maxid
            FROM personne_situation
        ")->maxid;

        $rp = 0;

        $debut = $app::$currentYear."-09-01";
        $fin = ($app::$currentYear+1)."-08-31";
        $sqlList= [];
        foreach($req as $r){
            $maxId++;
            $rp++;
            $rep = -$idTab[$r->id];
            $sqlList[] = "({$maxId}, 't', {$r->id}, {$reportId}, '{$debut}', '{$fin}', {$rep} )";

        }


        $sql = "
        INSERT INTO personne_situation 
        ( id, actif, personne, situation, debut, fin, reduction)
        VALUES
         ".join(",\n", $sqlList);
        
        $app::$db->h_query($sql);

        return $rp;
    }



    private static function getReports(){
        $app = \TDS\App::get();

        $sql = "SELECT 
                P.id,
                P.prenom,
                P.nom,
                PS.reduction,
                PS.situation,
                PS.id as identifiant
            FROM personne_situation as PS
            LEFT JOIN Personne as P on P.id = PS.personne
            WHERE PS.id>0
            AND (PS.situation = 1045 OR PS.situation=1046)
            AND PS.reduction > 0
        ";
        $a_supprimer = $app::$db->getAll($sql);
        $sql = "SELECT 
                P.id,
                P.prenom,
                P.nom,
                PS.reduction,
                PS.situation,
                PS.id as identifiant
            FROM personne_situation as PS
            LEFT JOIN Personne as P on P.id = PS.personne
            WHERE PS.id>0
            AND (PS.situation = 1045 OR PS.situation=1046)
            AND PS.reduction < 0
        ";
        $a_convertir = $app::$db->getAll($sql);
        
        return [$a_supprimer, $a_convertir]; 
    }

    public static function transfertReports($action=null){
        $app = \TDS\App::get();

        if (is_null($action)){
            list($a_supprimer, $a_convertir) = self::getReports();

            echo $app::$viewer->render('admin/transfertReportsSommaire.html.twig',["a_supprimer" => $a_supprimer, "a_convertir" => $a_convertir]);
 
            exit();
        }

        list($a_supprimer, $a_convertir) = self::getReports();

        // à supprimer
        if (count($a_supprimer) > 0){
            $idList = [];
            foreach($a_supprimer as $report){
                $idList[] = $report->identifiant; 
            }

            $idListString = "(".implode(',', $idList ).")";

            $app::$db->h_query("
                DELETE FROM personne_situation
                WHERE id in {$idListString}
                ;
            ");
            $deleted = $app::$db->getAffectedRows();
    }  else {
        $deleted = 0;
    }
        // à convertir 
        $psNS = $app::NS('personne_situation');        


        $nb = 0;
        foreach($a_convertir as $rep){
            $report = $psNS::load($rep->identifiant);
            $report->reduction *= -1;
            $report->debut = $app::$currentYear."-09-01";
            $report->fin = ($app::$currentYear+1)."-08-31";
    
            $report->commentaire = "report de ".(-1+ $app::$currentYear)." vers ".($app::$currentYear);
            $report->save();
            $nb += 1;
        }


        echo $app::$viewer->render('admin/transfertReports.html.twig', ['deleted' => $deleted, 'nb' => $nb]);
    }


    private static function getEnLatence(){
        $app = \TDS\App::get();
        //$voeuNS = $app::NS('Voeu'); 

        // $VL = Query('Voeu', 'V')->join('V.voeu_bilan_ligne', 'VBL')

        $q = new Query($app::NS('Voeu'), 'V');
        $q->join('V.personne', 'P', ['id', 'actif', 'nom', 'prenom']);
        $q->join('V.enseignement', 'E', ['id', 'actif', 'uid', 'nuac']);
        $q->join('V.voeu_bilan_ligne', 'VBL');
        $q->addSQL("WHERE V.actif and P.actif and E.actif and V.etat_ts <2 and VBL.heures>0");


        return $q->exec();
        //return $voeuNS::loadWhere('actif and etat_ts<2');
    }


    public static function forceAValider($action=null){
        $app = \TDS\App::get();

        if (is_null($action)){
            $enLatence = self::getEnLatence();


            $app::$cmpl['withJQuery'] = true;
            $app::$cmpl["withDataTables"]=true;
            echo $app::$viewer->render('admin/forceAValider.html.twig',["enLatence" => $enLatence, 'Prelim' => true]);
 
            exit();
        }

        $enLatence = self::getEnLatence();
        foreach($enLatence as $enr){
            $V = $enr['v'];
            $V->etat_ts = 2;
            $V->save();
        }

        $app::$cmpl['withJQuery'] = true;
        $app::$cmpl["withDataTables"]=true;
        echo $app::$viewer->render('admin/forceAValider.html.twig',["enLatence" => $enLatence, 'Prelim' => false]);
    }

}