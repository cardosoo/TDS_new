<?php
namespace base\Controllers;

use stdClass;
use TDS\Query;

class AdminController extends \zeroUP\Controllers\AdminController {

    static function allVOeux(){
        $app = \TDS\App::get();
        
        $q = new Query($app::NS('Voeu'), 'V');
        
        $q->join('V.personne', 'P', ['id', 'actif', 'nom', 'prenom']);
        $q->join('V.enseignement', 'E');
//        $q->addSQL("WHERE {$q->V_id}>0 AND V.actif");
        $q->addSQL("WHERE {$q->V_id}>0");

        $voeuList = $q->exec();

        $app::$cmpl["withJQuery"]=true;
        $app::$cmpl["withDataTables"]=true;
        
        echo $app::$viewer->render('admin/allVoeux.html.twig', ['voeuList' => $voeuList]);
    }

    static function allVOeux2(){
        $app = \TDS\App::get();
                
        $q = new Query($app::NS('Voeu'), 'V');
        $q2 = new Query($app::NS('structure_enseignement'), 'SE');

        $q->join('V.personne', 'P', ['id', 'actif', 'nom', 'prenom']);
        $q->join('V.enseignement', 'E');
/*
        $q->join('E.enseignement_periode', 'EP');
        $q->join('E.enseignement_structure', 'ES');
*/
        // $q->addSQL("WHERE {$q->V_id}>0 AND V.actif");
        $q->addSQL("WHERE {$q->V_id}>0");
        
        $voeuList = $q->exec();

        $app::$cmpl["withJQuery"]=true;
        $app::$cmpl["withDataTables"]=true;
        
        echo $app::$viewer->render('admin/allVoeux2.html.twig', ['voeuList' => $voeuList]);        
    }

    static function noVoeu(){
        $app = \TDS\App::get();

        $E = $app::$db->fetchAll("
                SELECT 
                P.*
            FROM personne as P
            LEFT JOIN personne_charge as PC on P.id = PC.id 
            LEFT JOIN (
                SELECT
                    personne,
                    sum(cm) as cm,
                    sum(ctd) as ctd,
                    sum(td) as td,
                    sum(tp) as tp
                FROM voeu
                GROUP BY personne
            ) as T on T.personne = P.id
            WHERE P.id> 0
            AND P.actif
            AND PC.charge>0
            AND ( 
                T.cm IS NULL
            OR T.cm+T.ctd+T.td+T.tp = 0
            ) 
       ");
        $app::$cmpl["withJQuery"]=true;
        $app::$cmpl["withDataTables"]=true;
      
        echo $app::$viewer->render('admin/noVoeu.html.twig', ['eList' => $E]);
    }

    static function chargesBesoins(){
        $app = \TDS\App::get();

        $chargeList = $app::$db->fetchAll("
            SELECT
                P.*,
                VPB.*, 
                PC.*,
                S.*
            FROM personne as P
            LEFT JOIN  voeu_personne_bilan as VPB on VPB.id = P.id
            LEFT JOIN personne_charge as PC on PC.id= P.id 
            LEFT JOIN statut as S on S.id = P.statut
            WHERE P.id> 0 AND P.actif
            AND S.obligation>0
            /* AND VPB.heures <>0 */
        ");
        
        $besoinList = $app::$db->fetchAll("
        SELECT
            E.*,
            VEB.*,
            ES.*
        FROM enseignement as E
        LEFT JOIN voeu_enseignement_bilan as VEB on E.id = VEB.id
        LEFT JOIN enseignement_structure as ES on ES.id = E.id
        WHERE E.id >0 AND E.actif
        AND (VEB.cm <> 0 OR VEB.ctd <>0 OR VEB.td <>0 OR VEB.tp<>0 OR VEB.extra<>0 OR VEB.bonus<>0) 
        ");
        
        $app::$cmpl["withJQuery"]=true;
        $app::$cmpl["withDataTables"]=true;
        
        echo $app::$viewer->render('admin/chargesBesoins.html.twig', ['chargeList' => $chargeList, 'besoinList'=> $besoinList ]);
    }

/*
    static function listCorrespondants(){
        $app = \TDS\App::get();

        $list = $app::$db->fetchAll("
            SELECT DISTINCT
                E.id,
                E.nuac,
                E.intitule,
                P.id,
                P.prenom,
                P.nom
            FROM enseignement as E
            LEFT JOIN voeu as V on (V.enseignement = E.id     AND  V.correspondant )
            LEFT JOIN personne as P on (V.personne = P.id     AND  V.correspondant )
            WHERE E.id>0
            AND E.actif
        ");
        
        $app::$cmpl["withJQuery"]=true;
        $app::$cmpl["withDataTables"]=true;
        
        echo $app::$viewer->render('admin/listCorrespondants.html.twig', ['list' => $list]);
    }
*/

    static function listCorrespondants(){
        $app = \TDS\App::get();

        $enseignementList = $app::NS('Enseignement')::loadWhere('actif');

        $app::$cmpl["withJQuery"]=true;
        $app::$cmpl["withDataTables"]=true;
        
        echo $app::$viewer->render('admin/listCorrespondants2.html.twig', ['EList' => $enseignementList]);

    }



    static function autoCorrespondants(){
        $app = \TDS\App::get();
 
        $ensList =  $app::$db->getAll("
            SELECT
                VEB.*
            FROM voeu_enseignement_bilan as VEB
            LEFT JOIN enseignement as E on E.id = VEB.id
            WHERE VEB.id>0
            AND VEB.correspondant > 0
            AND E.actif
        ");
        
        $mod = 0;
        foreach($ensList as $ens){
            // on récupère les voeux pour l'enseignement en question
            $VL = $app::$db->getAll("
                SELECT
                    V.*
                FROM voeu as V
                WHERE id>0
                AND V.enseignement={$ens->id}
            ");
        
            // si il n'y a qu'un seul voeu alors on affecte
            if (count($VL)==1){
                $app::$db->h_query("
                    UPDATE voeu
                    SET correspondant = 't'
                    WHERE id = {$VL[0]->id}
                ");
                $mod++;
            } else { // on regarde si quelqu'un a pris un cours (ou un bonus ?)
                foreach($VL as $V){
                    if ($V->cm>0){
                        $app::$db->h_query("
                            UPDATE voeu
                            SET correspondant = 't'
                            WHERE id = {$V->id}
                        ");
                        $mod++;
                    } else {
                        if ($V->bonus>0){
                            $app::$db->h_query("
                                UPDATE voeu
                                SET correspondant = 't'
                                WHERE id = {$V->id}
                            ");
                            $mod++;
                        }
                            
                    }
                } 
            }
        }

        echo $app::$viewer->render('admin/autoCorrespondants.html.twig', ['mod' => $mod]);
    }


    public static function deleteVoeuxInactif(){
        $app = \TDS\App::get();

        $sql = "DELETE FROM Voeu as V USING Enseignement as E
        WHERE E.id = V.enseignement AND NOT E.actif";
        $app::$db->h_query($sql);        
        $affectedRows = $app::$db->getAffectedRows();

        $app::$pub->info[]="{$affectedRows} voeu(x) avec des enseignemnents inactifs ont été supprimés";

        $sql = "DELETE FROM Voeu as V USING Personne as Pe
        WHERE Pe.id = V.personne AND NOT Pe.actif";
        $app::$db->h_query($sql);        
        $affectedRows = $app::$db->getAffectedRows();

        $app::$pub->info[]="{$affectedRows} voeu(x) avec des personnes inactives ont été supprimés";

        echo $app::$viewer->render('admin/index.html.twig', []);

    }


    public static function inactiveVoeuxBlancs(){
        $app = \TDS\App::get();
        
        $sql = "
        UPDATE voeu 
           SET actif = 'f'
        WHERE
           cm=0 and ctd =0 and td =0 and tp=0 and extra=0 and bonus=0
        ";

        $app::$db->h_query($sql);
        $affected = $app::$db->getAffectedRows();
        $app::$pub->info[] = "Les voeux blancs ont été rendus inactifs :  {$affected} voeu(x) concernés";

        self::home();       
    }

    // pour de vrai on supprime les voeux inactifs
    public static function deleteVoeuxBlancs(){
        $app = \TDS\App::get();
        
        $sql = "
        DELETE FROM voeu 
        WHERE NOT actif
        ";

        $app::$db->h_query($sql);
        $affected = $app::$db->getAffectedRows();
        $app::$pub->info[] = "Les voeux blancs inactifs ont été supprimés :  {$affected} voeu(x) concernés";

        self::home();
    }

    public static function infoEtapes(){
        $app = \TDS\App::get();

        $params = [];
        $composanteList = $app::NS('Composante')::loadWhere('actif');
        foreach($composanteList as $composante){
            $p = new \stdClass();
            $p->composante = $composante;
            $p->list = [];
            foreach($composante->maquetteList as $maquette){
                foreach($maquette->diplomeList as $diplome){
                    foreach($diplome->etapeList as $etape){
                        $l = new \stdClass();
                        $l->maquette = $maquette;
                        $l->diplome = $diplome;
                        $l->etape = $etape;
                        $p->list[]= $l;
                    }
                }

            }
            $params[] = $p;
        }

        $app::$cmpl["withJQuery"]=true;
        $app::$cmpl["withDataTables"]=true;

        echo $app::$viewer->render('admin/infoEtapes.html.twig', ['params' => $params]);
    }


    public static function listComments($entity){
        $app=\TDS\App::get();
        $className = $app::NS($entity);

        switch($entity){
            case 'Personne':
                $cl = 'commentaire_personne';
                $cd = 'personne';
                break; 
            case 'Enseignement':
                $cl = 'commentaire_enseignement';
                $cd = 'enseignement';
                break; 
            case 'Maquette':
                $cl = 'commentaire_maquette';
                $cd = 'maquette';
                break; 
            case 'Composante':
                $cl = 'commentaire_composante';
                $cd = 'composante';
                break; 
        };

        $cn = $app::NS($cl);
        $commentList = $cn::loadWhere('actif');

        $app::$cmpl["withJQuery"]=true;
        $app::$cmpl["withDataTables"]=true;
        echo $app::$viewer->render('admin/listComments.html.twig', ['CL' => $commentList, 'E' => $entity]);
    }

    public static function saveComment($entity, $id){
        $app=\TDS\App::get();
        $className = $app::NS($entity);

        switch($entity){
            case 'Personne':
                $cl = 'commentaire_personne';
                $cd = 'personne';
                break; 
            case 'Enseignement':
                $cl = 'commentaire_enseignement';
                $cd = 'enseignement';
                break; 
            case 'Maquette':
                $cl = 'commentaire_maquette';
                $cd = 'maquette';
                break; 
            case 'Composante':
                $cl = 'commentaire_composante';
                $cd = 'composante';
                break; 
        };
    
        $cn = $app::NS($cl);

        $comment = $cn::load($id);
        if (! $comment->$cd->canEditCommentaires()){
            echo "Pb : pas autoriser à modifier ce commenaire !";
            exit();            
        }

        $comment->commentaire = $_POST['commentaire'];
        $comment->auteur = $app::$auth->user->id;
        $comment->date = date('Y-m-d');
        $comment->save();
        echo "Done";
    }

    public static function addComment($entity, $id){
        $app=\TDS\App::get();
        $className = $app::NS($entity);

        switch($entity){
        case 'Personne':
            $cl = 'commentaire_personne';
            $cd = 'personne';
            break; 
        case 'Enseignement':
            $cl = 'commentaire_enseignement';
            $cd = 'enseignement';
            break; 
        case 'Maquette':
            $cl = 'commentaire_maquette';
            $cd = 'maquette';
            break; 
        case 'Composante':
            $cl = 'commentaire_composante';
            $cd = 'composante';
            break; 
        };

        $cn = $app::NS($cl);
        $E = $app::NS($entity)::load($id);
        if (! $E->canEditCommentaires()){
            echo "Pb";
            exit();            
        }

        $comment = new $cn;
        $comment->$cd = $id;
        $comment->auteur = $app::$auth->user->id;
        $comment->date =  date('Y-m-d');
        $comment->commentaire = '';
        $comment->save();
        echo $comment->id;
    }


    public static function maskComment($entity, $id){
        $app=\TDS\App::get();
        $className = $app::NS($entity);

        switch($entity){
        case 'Personne':
            $cl = 'commentaire_personne';
            $cd = 'personne';
            break; 
        case 'Enseignement':
            $cl = 'commentaire_enseignement';
            $cd = 'enseignement';
            break; 
        case 'Maquette':
            $cl = 'commentaire_maquette';
            $cd = 'maquette';
            break; 
        case 'Composante':
            $cl = 'commentaire_composante';
            $cd = 'composante';
            break; 
        };

        $cn = $app::NS($cl);

        $comment = $cn::load($id);
        if (! $comment->$cd->canEditCommentaires()){
            echo "Pb : pas autoriser à modifier ce commenaire !";
            exit();            
        }

        $comment->actif = false;
        $comment->save();
        echo "Done";
    }

    public static function delComment($entity, $id){
        $app=\TDS\App::get();
        $className = $app::NS($entity);

        switch($entity){
            case 'Personne':
                $cl = 'commentaire_personne';
                $cd = 'personne';
                break; 
            case 'Enseignement':
                $cl = 'commentaire_enseignement';
                $cd = 'enseignement';
                break; 
            case 'Maquette':
                $cl = 'commentaire_maquette';
                $cd = 'maquette';
                break; 
            case 'Composante':
                $cl = 'commentaire_composante';
                $cd = 'composante';
                break; 
            };
    
        $cn = $app::NS($cl);

        $comment = $cn::load($id);
        if (! $comment->$cd->canEditCommentaires()){
            echo "Pb : pas autoriser à modifier ce commenaire !";
            exit();            
        }

        $comment->delete();
        echo "Done";
    }

    private static function getDetail($t){
        if (count($t) == 0){ return 0;}
        return floatval($t[0]);
    }

    public static function verifECUE(){
        $app=\TDS\App::get();

        $ose = new \base\OSE();

        $semestreList = [
            '1' => 'Semestre 1',
            '2' => 'Semestre 2', 
            '3' => 'Annuel',
            'NULL' => 'NULL',
        ];

        $okList = [];
        $horsOSE = [];
        $horsDetailsOSE = [];
        $badDetails = [];
        $badPeriod = [];
        $multipleECUE = [];

        $enseignementList = array_slice($app::NS('Enseignement')::loadWhere('actif'), 0, 1500);

        foreach($enseignementList as $enseignement){
            $detailsFoire = $enseignement->enseignement_etudiant_details;
            $structure = $enseignement->enseignement_structure;
            $periode = $semestreList[substr($enseignement->enseignement_periode->periode, 1, -1)];

            $ecueList = explode('|', $structure->ecue);
            $rep = [];

            $pasOk = false;
            $ok = false;
            $p = false;

            foreach($ecueList as $codeECUE){
                $ecue = $ose->findECUE($codeECUE);
                $pasOSE = is_null($ecue);
                if ($pasOSE){
                    $horsOSE[$enseignement->id] = $enseignement;
                    $pasOk = true;
                } else {
                    $detailsOSE = $ose->getDetails($codeECUE);
                    if (is_null($detailsOSE)){
                        $horsDetailsOSE[$enseignement->id] = [
                            'enseignement' => $enseignement,
                            'ecue' => $ecue,
                        ];
                        $pasOk = true;    
                    } else {
                        $dOSE = [
                            'cm' => self::getDetail($detailsOSE['CM']),
                            'td' => (self::getDetail($detailsOSE['TD']) + self::getDetail($detailsOSE['TD2'])),
                            'tp' => (self::getDetail($detailsOSE['TP']) + self::getDetail($detailsOSE['TP7'])),
                            'ctd' => (self::getDetail($detailsOSE['CMTD']) + self::getDetail($detailsOSE['CMTD7'])),
                            'extra' => (self::getDetail($detailsOSE['PROJET']) + self::getDetail($detailsOSE['MD'])),
                            'bonus' => (self::getDetail($detailsOSE['FORFAI'])),
                        ];
                        
                        $rep[$codeECUE] = [
                            'ecue' => $ecue,
                            'dOSE' => $dOSE,
                            'detailOSE' => $detailsOSE,
                        ];

    
                        $CMok = $dOSE['cm'] == $detailsFoire->cm;
                        $TDok = $dOSE['td'] == $detailsFoire->td;
                        $TPok = $dOSE['tp'] == $detailsFoire->tp;
                        $CTDok = $dOSE['ctd'] == $detailsFoire->ctd;
                        $EXTRAok = $dOSE['extra'] == $detailsFoire->extra;
                        $BONUSok = $dOSE['bonus'] == $detailsFoire->bonus;
                        
                        $ok |= ( $CMok && $TDok && $TPok && $CTDok && $EXTRAok && $BONUSok );
                        $p |= $periode == $ecue['semestre'];
    
                    }
    
                }
            }
            if ( ! $pasOSE ){
                if (count($ecueList) > 1){
                    $multipleECUE[$enseignement->id] = [
                        'enseignement' => $enseignement,
                        'detailsFoire' => $detailsFoire,
                        'rep' => $rep,
                    ];
                    $pasOk = true;
                }
                if (!$ok){
                    $badDetails[$enseignement->id] = [
                        'enseignement' => $enseignement,
                        'detailsFoire' => $detailsFoire,
                        'rep' => $rep,
                    ];
                    $pasOk = true;
                } 
                if (!$p){
                    $badPeriod[$enseignement->id] = [
                        'ecue' => $ecueList[0],
                        'intitule' => $enseignement->intitule,
                        'rep' => $rep,
                    ];
                    $pasOk = true;
                }
                if (!$pasOk){
                    $okList[] = [
                        'ecue' => $ecueList[0],
                        'intitule' => $enseignement->intitule,
                        'rep' => $rep,
                    ];
                }    
            }
        }    
        $app::$cmpl["withJQuery"]=true;
        $app::$cmpl["withDataTables"]=true;

        echo $app::$viewer->render('admin/verifECUE.html.twig', [
            'OKL' => $okList, 
            'HOL' => $horsOSE,
            'BDL' => $badDetails,
            'BPL' => $badPeriod,
            'MEL' => $multipleECUE,
        ]);
    }

    public static function vacatairesSansVoeu(){
        $app = \TDS\App::get();
 
        $vacList =  $app::$db->getAll("
            SELECT 
                P.id as id_personne,
                P.nom || ' ' || P.prenom as personne
            FROM Personne as P
            LEFT JOIN Voeu as V on V.personne = P.id
            LEFT JOIN Statut as S on S.id = P.statut
            WHERE P.actif
            AND S.nom ILIKE 'VAC%'
            AND V.id IS NULL
        ");

        $nameList = [];
        $v = $vacList[0];
        foreach($v as $key => $value){
            $nameList[]= $key;
        }


        $app::$cmpl["withJQuery"]=true;
        $app::$cmpl["withDataTables"]=true;
// var_dump($vacList);

        echo $app::$viewer->render('admin/standardList.html.twig', [
            'title' => 'Liste des vactaires actifs sans voeu', 
            'nameList' => $nameList, 
            'data'=> $vacList]);
         
    }

}
