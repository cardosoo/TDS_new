<?php
namespace foire\Controllers;

use foire\Model\Voeu;
use stdClass;

class T2020Controller extends \base\Controllers\TestController {

    /**
     * Migration des voeux
     * - on fait la migration si l'enseignement et l'enseignant sont toujours actifs
     * - il faut aussi que l'ancienneté soit inférieur à 3
     * - Si l'enseignement n'est pas attribuable, on fait aussi le report (en conservant les volumes)
     */

    public static function migrationVoeux2020(){
        $app = \TDS\App::get();

        $foire2020 = new \TDS\Database("foire2020", $app::$baseUser, $app::$basePwd, 'localhost');
        // suppression des voeux déjà existants
        $app::$db->h_query("
            DELETE FROM voeu; 
        ");

        $voeu2020List = $foire2020->getAll("SELECT * from voeu where num>0");
        $voeuNS = $app::NS('Voeu');
        foreach($voeu2020List as $voeu2020 ){
            
            //if ($voeu2020->enseignant != 292) continue;
            //if ($voeu2020->actif !='t') continue;  // en pratique il y a des voeux qui sont indiqués comme actif = NULL dans foire2020 du coup, j'enlève cela
            $enseignement = $app::NS('Enseignement')::load($voeu2020->enseignement);
            if (is_null($enseignement)) continue;
            if (! $enseignement->actif) continue;
            if ($enseignement->nom == "report") continue;

            $personne = $app::NS('Personne')::load($voeu2020->enseignant);
            if (is_null($personne)) continue;
            if (! $personne->actif) continue;
            
            if ($enseignement->attribuable){
                if ( $voeu2020->anciennete >= 3) continue;
            }

            $voeu = new $voeuNS();
            $voeu->personne = $voeu2020->enseignant;
            $voeu->enseignement = $voeu2020->enseignement;
            $voeu->anciennete = $voeu2020->anciennete+1;

            if (! $enseignement->attribuable){
                $voeu->cm = $voeu2020->cours;
                $voeu->ctd = $voeu2020->ctd;
                $voeu->td = $voeu2020->td;
                $voeu->tp = $voeu2020->tp;
                $voeu->extra = $voeu2020->colle;
                $voeu->bonus = $voeu2020->bonus;
            }
            $voeu->save();
        }

        var_dump("migrationVoeux2020 -> terminé");
    }

    public static function calculReports2020(){
        $app = \TDS\App::get();

        $foire2020 = new \TDS\Database("foire2020", $app::$baseUser, $app::$basePwd, 'localhost');
        $reportList = $foire2020->getAll("
            SELECT 
                P.num as id,
                P.prenom,
                P.nom,
                -round(VPB.heures) as report 
            FROM enseignant as P
            LEFT JOIN voeu_enseignant_bilan as VPB on P.num = VPB.num
            LEFT JOIN statut as ST on P.statut = ST.num
            LEFT JOIN situation as S on S.num = P.situation
            WHERE ST.obligation >0
            AND S.nom_court != 'Légaliste'
            AND round(VPB.heures) != 0
        ");

        $reportEnseignement = $app::NS("Enseignement")::loadOneWhere("nom='report'");
        // suppression des reports existants si il y en a déjà
        $app::$db->h_query("
            DELETE FROM Voeu as V
            WHERE V.enseignement = {$reportEnseignement->id}
        ");

        /* il faut sans doute vérifier que l'enseignant est toujours actif */
        $voeuNS = $app::NS('Voeu');
        $volumeReport = 0;
        $nbReport = 0;
        foreach($reportList as $rep){
            $personne = $app::NS('Personne')::load($rep->id);
            if ($personne->actif) {
                $report = new $voeuNS();
                $report->enseignement = $reportEnseignement->id;
                $report->personne = $rep->id;
                if ($rep->report > 40 ){
                    $rep->report = 40;
                }
                if ($rep->report < -40 ){
                    $rep->report = -40;
                }
                $report->extra = $rep->report;
                $volumeReport += $rep->report; 
                $nbReport++;
                $report->save();
            }
        }
        $reportEnseignement->extra = $volumeReport;
        $reportEnseignement->save();

        $app::$pub->info[]="{$volumeReport} hETD de report ont été installées pour {$nbReport} personnes concernées";

        echo $app::$viewer->render('admin/index.html.twig', []);
    }

    static function importCodesLDAP2020(){
        $app = \TDS\App::get();
        $ldap = new \TDS\LDAPExtern();

        $foire2020 = new \TDS\Database("foire2020", $app::$baseUser, $app::$basePwd, 'localhost');
        $enseignantList = $foire2020->getAll("
            SELECT 
                P.num as id,
                P.harpege as uid,
                round(VPH.heures) as charge 
            FROM enseignant as P
            LEFT JOIN voeu_enseignant_heures as VPH on P.num = VPH.num
            WHERE P.num > 0
            AND VPH.heures > 0
        ");

        $data = [];
        foreach($enseignantList as $enseignant){
            $rep = $ldap->list( "(uid={$enseignant->uid})", ['uid', 'supannempid']);
            $rep = $ldap->reformat($rep);

            $tmp = new stdClass();
            $tmp->id = $enseignant->id;
            $tmp->uid = $enseignant->uid;
            $tmp->siham = isset($rep[$enseignant->uid]->supannempid) ? $rep[$enseignant->uid]->supannempid : "";

            $data[] = $tmp;

            $foire2020->query("
                UPDATE enseignant
                SET emploi = '{$tmp->siham}'
                WHERE num = {$tmp->id}
            ");
        }


        $app::$cmpl["withJQuery"]=true;
        $app::$cmpl["withDataTables"]=true;

        $title = 'Liste des trucs';
        $nameList = ['id','uid','siham'];
    
        echo $app::$viewer->render('admin/standardList.html.twig', ['title' => $title, 'nameList' => $nameList, 'data' => $data]);
        exit();
    }
 
    public static function heures2020(){
        $app = \TDS\App::get();

        $foire2020 = new \TDS\Database("foire2020", $app::$baseUser, $app::$basePwd, 'localhost');
        $enseignantList = $foire2020->getAll("
            SELECT 
                P.num as id,
                P.emploi as ose,
                P.nom || ' ' || P.prenom as nom,
                round(VPH.heures*100)/100 as charge,
                E.intitule,
                E2.ecue,
                V.cours * e.s_cours * e.d_cours AS CM,
                V.ctd   * e.s_ctd   * e.d_ctd AS ctd,
                V.td    * e.s_td    * e.d_td  AS td,
                V.tp    * e.s_tp    * e.d_tp  AS tp,
                V.colle * e.s_colle * e.d_colle  AS extra,
                v.bonus
            FROM voeu as V
            LEFT JOIN enseignant as P on V.enseignant = P.num 
            LEFT JOIN enseignement as E on V.enseignement = E.num
            LEFT JOIN  voeu_enseignant_heures as VPH on VPH.num = P.num
            LEFT JOIN 
                ( SELECT 
                    E3.id,
                    string_agg(E3.ecue, ', ') as ecue
                FROM
                    ( SELECT DISTINCT
                        enseignement as id,
                        code_ecue as ecue
                    FROM structure_enseignement as SE 
                    ) as E3
                GROUP by E3.id
                ) as E2 on E2.id = E.num
            WHERE V.num > 0
        ");

        $app::$cmpl["withJQuery"]=true;
        $app::$cmpl["withDataTables"]=true;

        $title = 'Liste des trucs';
        $nameList = ['id','ose', 'nom', 'charge', 'ecue', 'intitule', 'cm', 'ctd', 'td', 'tp', 'extra', 'bonus'];
    
        echo $app::$viewer->render('admin/standardList.html.twig', ['title' => $title, 'nameList' => $nameList, 'data' => $enseignantList]);
        //var_dump($enseignantList);
    }

}
