<?php
namespace foire\Controllers;

use TDS\Query;

class AdminController extends \base\Controllers\AdminController {

    public static function home(){
        $app = \TDS\App::get();

        echo $app::$viewer->render('admin/index.html.twig');
    }

    private static function updateAllSituation($situationList){
        $app = \TDS\App::get();
 
        foreach($situationList as $situation){
            $situation->setReductionEffective();
            $situation->save();
        }

        // pour la situation légaliste, il faut faire un truc spécial.
        $situationNS = $app::NS('Situation');
        $legaliste = $situationNS::loadOneWhere("nom = 'Légaliste'");
        $reduc = $app::$chargeUFR -192;
        $legaliste->reduction_legale = "{$reduc}h";
        $legaliste->reduction        = $reduc;
        $legaliste->ufr = true;
        $legaliste->save();

    }

    private static function updateMCF_PROF(){
        $app = \TDS\App::get();
        $statutNS = $app::NS('Statut');
        $mcf = $statutNS::loadOneWhere("nom = 'MCF'");
        $mcf->obligation = $app::$chargeUFR;
        $mcf->save();
        $prof = $statutNS::loadOneWhere("nom = 'PROF'");
        $prof->obligation = $app::$chargeUFR;
        $prof->save();

        $statutNS::loadOneWhere("nom = 'PROF'")->obligation = $app::$chargeUFR;
    }

    public static function setChargeUFR(){
        $app = \TDS\App::get();

        $args = [
            'charge' => FILTER_VALIDATE_INT,
        ];
        $propList = filter_input_array(INPUT_POST, $args);

        
        $situationNS = $app::NS('Situation');
        $situationList = $situationNS::loadWhere('actif', ['reduction']);

        if (!is_null($propList)){
            $charge= $propList['charge']?$propList['charge']:null;
            $app::$chargeUFR = $charge;
            $app::$texte['chargeReference'] = $charge;
            file_put_contents('../../TDS_plus/foire/chargeUFR', $charge);
            self::updateAllSituation($situationList);
            self::updateMCF_PROF();
        } else {
            $charge = $app::$chargeUFR;
        }

        $app::$cmpl["withJQuery"]=true;
        $app::$cmpl["withDataTables"]=true;

        echo $app::$viewer->render('admin/setCharge.html.twig', ['charge' => $charge, 'situationList' => $situationList]);
    }


    public static function deletePanierInactive(){
        $app = \TDS\App::get();

        $sql = "DELETE FROM Panier as P USING Enseignement as E
        WHERE E.id = P.enseignement AND NOT E.actif";
        $app::$db->h_query($sql);        
        $affectedRows = $app::$db->getAffectedRows();

        $app::$pub->info[]="{$affectedRows} panier(s) avec des enseignemnents inactifs ont été supprimés";

        $sql = "DELETE FROM Panier as P USING Personne as Pe
        WHERE Pe.id = P.personne AND NOT Pe.actif";
        $app::$db->h_query($sql);        
        $affectedRows = $app::$db->getAffectedRows();

        $app::$pub->info[]="{$affectedRows} panier(s) avec des personnes inactives ont été supprimés";

        echo $app::$viewer->render('admin/index.html.twig', []);

    }

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

    public static function migrationVoeux($action=null){
        $app = \TDS\App::get();

        if (is_null($action)){
            echo $app::$viewer->render('admin/migrationVoeuxSommaire.html.twig', []);
            exit();
        }

        $app::$db->h_query("
            UPDATE voeu SET 
               anciennete = anciennete +1
            WHERE id>0;
        ");
        $nb = 0; //$app::$db->getAffectedRows();

        // permet de supprimer les voeux qui ne sont pas prioritaires et qui sont attribuables 
        $app::$db->h_query("
            DELETE FROM voeu
            WHERE anciennete >= 4
            AND enseignement in (
                SELECT id
                FROM Enseignement
                WHERE id > 0
                AND attribuable
            );
        ");
        $deleted = $app::$db->getAffectedRows();
        // permet de remettre à 0 les volumes de voeux qui sont attribuables
        $app::$db->h_query("
            UPDATE voeu SET
                actif = TRUE,
                CM = 0,
                CTD = 0,
                TD = 0,
                TP = 0
            WHERE enseignement in (
                SELECT id
                FROM Enseignement
                WHERE id > 0
                AND attribuable
            );
        ");
        $kept = $app::$db->getAffectedRows();

        echo $app::$viewer->render('admin/migrationVoeux.html.twig', ['nb' => $nb, 'deleted'=> $deleted, 'kept'=>$kept]);
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
                   OR S.nom LIKE 'DCME%'
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


    public static function calculReports($action=null){
        $app = \TDS\App::get();
 
        if (is_null($action)){
            echo $app::$viewer->render('admin/calculReportsSommaire.html.twig', []);
            exit();
        }
      
        $app::$db->h_query("
            DELETE FROM personne_situation
            WHERE id>0
            AND (situation = 1045 OR situation=1046)
            ;
        ");
        $deleted = $app::$db->getAffectedRows();

        $rp = self::reports(true);
        $rn = self::reports(false);

        echo $app::$viewer->render('admin/calculReports.html.twig', ['deleted' => $deleted, 'rp' => $rp, 'rn' => $rn]);
    }


}
