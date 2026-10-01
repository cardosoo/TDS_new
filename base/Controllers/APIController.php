<?php

namespace base\Controllers;

class APIController extends \TDS\Controller {
    public static function listingServices(){
        $app = \TDS\App::get();

        $serviceList = $app::$db->fetchAll("
        SELECT
            P.ose,
            P.prenom,
            P.nom,
            ES.code,
            ES.ecue,
            E.intitule,
            VDH.cm,
            VDH.ctd,
            VDH.td,
            VDH.tp,
            VDH.extra,
            VDH.bonus
        FROM voeu as V
        LEFT JOIN enseignement as E on E.id = V.enseignement
        LEFT JOIN personne as P on P.id = V.personne
        LEFT JOIN enseignement_structure as ES on ES.id = E.id
        LEFT JOIN voeu_detail_heures as VDH on VDH.id = V.id
        ORDER BY P.nom, P.prenom
        
        ");
        echo $app::$viewer->render('api/listingService.csv.twig', ['serviceList' => $serviceList]);
   }

    public static function listingServicesOSE(){
        $app = \TDS\App::get();

        $oseNS = '\\'.$app::$appName.'\\OSE';
        $ose = new $oseNS;

        echo file_get_contents($ose->servicePath);
        
    }
    public static function allEcuesOSE(string $year){
        $app = \TDS\App::get();

        $baseName = $app::$appName."{$year}";
        $db = new \TDS\Database($baseName, $app::$baseUser, $app::$basePwd, 'localhost' );
        pg_set_client_encoding($db->conn, "UNICODE");

        $EList = $db-> getAll("
            SELECT DISTINCT
                SE.code_ecue as ecue
            FROM structure_enseignement as SE
            LEFT JOIN enseignement as E on SE.enseignement = E.id
            WHERE E.actif AND E.id>0
        ");

        $oseNS = '\\'.$app::$appName.'\\OSE';
        $ose = new $oseNS($year);

        $res = [];
        foreach($EList as $EL){
            if (!in_array( $EL->ecue, ["Code ECUE", "RES FIL-ISUPF"]) ){
                $res[$EL->ecue] = $ose->findECUE($EL->ecue);
            }
        }
        echo json_encode($res);

    }

    
    public static function activeUserList(string $year){
        $app = \TDS\App::get();

        $baseName = $app::$appName."{$year}";
        $db = new \TDS\Database($baseName, $app::$baseUser, $app::$basePwd, 'localhost' );
        pg_set_client_encoding($db->conn, "UNICODE");

        $userList = $db-> getAll("
            SELECT DISTINCT
                P.id,
                P.nom,
                P.prenom,
                P.ose,
                S.nom as statut
            FROM Personne as P
            LEFT JOIN Statut as S on P.statut = S.id
            WHERE P.actif AND P.id>0
            ORDER BY nom, prenom
        ");

        echo date("'d/m/Y\t'H:i:s")."\n";
        echo "id_personne\tNom\tPrénom\tOSE\tStatut\n";
        foreach($userList as $user){
            echo "{$user->id}\t{$user->nom}\t{$user->prenom}\t{$user->ose}\t{$user->statut}\n";
        }

    }

    public static function listingUserFoncRef(string $year, $withStages = false){
        $app = \TDS\App::get();

        $baseName = $app::$appName."{$year}";
        $db = new \TDS\Database($baseName, $app::$baseUser, $app::$basePwd, 'localhost' );
        pg_set_client_encoding($db->conn, "UNICODE");

        if ($withStages){
            $stageCondition = "";
        } else {
            $stageCondition = "AND FR.id != 4"; // on exclut les stages
        }

        $list = $db-> getAll('
            SELECT DISTINCT
                P.id as "personneId",
                P.nom,
                P.prenom,
                P.ose,

                FR.id as "foncRefId",
                FR.intitule as "intitule",
                
                R.code,

                PFR.id as "personneFoncRefId",
                PFR.volume as "volume",
                PFR.commentaire as "commentaire"

            FROM Personne_foncRef as PFR
            LEFT JOIN Personne as P on PFR.personne = P.id
            LEFT JOIN FoncRef as FR on PFR.foncRef = FR.id
            LEFT JOIN Referentiel as R on FR.referentiel = R.id

            WHERE P.actif AND P.id>0
            AND FR.actif and FR.id > 0
            AND PFR.actif and PFR.id > 0
            '.$stageCondition.'
            ORDER BY "foncRefId", nom, prenom
        ');

        echo date("'d/m/Y\t'H:i:s")."\n";
        echo "personneId\tprenom\tnom\tose\tfoncRefId\tintitule\tcode\tpersonneFoncRefId\tcommentaire\tvolume\n";
        foreach($list as $elm){
            $commentaire = str_replace(["\t", "\n", "\r"], " ", $elm->commentaire);
            echo "{$elm->personneId}\t{$elm->prenom}\t{$elm->nom}\t{$elm->ose}\t{$elm->foncRefId}\t{$elm->intitule}\t{$elm->code}\t{$elm->personneFoncRefId}\t{$commentaire}\t{$elm->volume}\n";
        }

    }

    public static function listingUserFoncRefWithStages(string $year){
        return self::listingUserFoncRef($year, true);
    }

    public static function listingUserSituation(string $year){
        $app = \TDS\App::get();

        $baseName = $app::$appName."{$year}";
        $db = new \TDS\Database($baseName, $app::$baseUser, $app::$basePwd, 'localhost' );
        pg_set_client_encoding($db->conn, "UNICODE");

        $list = $db-> getAll('
            SELECT DISTINCT
                P.id as "personneId",
                P.nom,
                P.prenom,
                P.ose,

                S.id as "situationId",
                S.nom as "situationNom",
                S.ose as "situationCode",

                PS.reduction as "reduction",
                PS.commentaire as "commentaire",
                PS.id as "personneSituationId"

            FROM Personne_situation as PS
            LEFT JOIN Personne as P on PS.personne = P.id
            LEFT JOIN Situation as S on PS.situation = S.id

            WHERE P.actif AND P.id > 0
            AND S.actif and S.id > 0
            AND PS.actif and PS.id > 0
            ORDER BY nom, prenom
        ');

        echo date("'d/m/Y\t'H:i:s")."\n";
        echo "personneId\tprenom\tnom\tose\tsituationId\tsituationNom\tsituationCode\treduction\tcommentaire\tpersonneSituationId\n";
        foreach($list as $elm){
            $commentaire = str_replace(["\t", "\n", "\r"], " ", $elm->commentaire);
            echo "{$elm->personneId}\t{$elm->prenom}\t{$elm->nom}\t{$elm->ose}\t{$elm->situationId}\t{$elm->situationNom}\t{$elm->situationCode}\t{$elm->reduction}\t{$commentaire}\t{$elm->personneSituationId}\n";
        }

    }


    /**
     * Renvoie la liste des enseignements pour une personne.
     * La sortie est un fichier texte (csv) destiné à être utilisé par les scripts python d'importation dans OSE.
     * Un pré-traitement est nécessaire pour prendre en compte les 2 situations suivantes :
     * - enseignement avec 2 codes ECUE (il faut en choisir un seul - tourjous le même. Le premier ?)
     * - participation à plusieurs enseignements différents qui partagent le même code ECUE. Il faut faire une somme dessus 
     *
     * @param string $year l'année à prendre en compte
     * @param string $code le code SIHAM de la personne
     * @return void 
     */
    public static function listingUserEnseignement(string $year, string $code){
        $app = \TDS\App::get();
        
        $baseName = $app::$appName."{$year}";
        $db = new \TDS\Database($baseName, $app::$baseUser, $app::$basePwd, 'localhost' );
        pg_set_client_encoding($db->conn, "UNICODE");

        $list = $db->getAll("
        SELECT
            V.id,
            E.code,
            VDH.cm,
            VDH.ctd,
            VDH.td,
            VDH.tp,
            VDH.extra,
            VDH.bonus
        FROM voeu as V
        LEFT JOIN enseignement as E on E.id = V.enseignement
        LEFT JOIN personne as P on P.id = V.personne
        LEFT JOIN voeu_detail_heures as VDH on VDH.id = V.id
        WHERE V.actif
        AND P.actif
        AND E.actif
        AND P.ose = '{$code}' 
        ORDER BY P.nom, P.prenom
        ");

        $list2 = [];
        foreach($list as $elm){
            $elm->code = explode('|', $elm->code)[0]; // pré-traitement de : enseignement avec 2 codes ECUE 
            
            if (! isset($list2[$elm->code])){
                $list2[$elm->code] = $elm;
            } else{
                $list2[$elm->code]->id .= "|".$elm->id;
                $list2[$elm->code]->cm += $elm->cm;
                $list2[$elm->code]->ctd += $elm->ctd;
                $list2[$elm->code]->td += $elm->td;
                $list2[$elm->code]->tp += $elm->tp;
                $list2[$elm->code]->extra += $elm->extra;
                $list2[$elm->code]->bonus += $elm->bonus;
            }
        }


        echo date("'d/m/Y\t'H:i:s")."\n";
        echo "voeuId\tcodeECUE\tCM\tCMTD\tTD\tTP\tEXTRA\tBONUS\n";
        foreach($list2 as $elm){
            echo "{$elm->id}\t{$elm->code}\t{$elm->cm}\t{$elm->ctd}\t{$elm->td}\t{$elm->tp}\t{$elm->extra}\t{$elm->bonus}\n";
        }

    }


    public static function activeTeachingList(string $year){
        $app = \TDS\App::get();

        $baseName = $app::$appName."{$year}";
        $db = new \TDS\Database($baseName, $app::$baseUser, $app::$basePwd, 'localhost' );
        pg_set_client_encoding($db->conn, "UNICODE");

        $enseignementList = $db-> getAll("
            SELECT DISTINCT
                E.id,
                E.nuac,
                E.nom,
                ES.ecue,
                ES.composante,
                ES.cursus,
                ES.etape
            FROM Enseignement as E
            LEFT JOIN enseignement_structure as ES on ES.id = E.id
            WHERE E.actif AND E.id>0
            ORDER BY ES.cursus, ES.ecue
        ");

        echo date("'d/m/Y\t'H:i:s")."\n";
        echo "id_enseignement\tnuac\tnom\tECUE\tcomposante\tcursus\tetape\n";
        foreach($enseignementList as $E){
            echo "{$E->id}\t{$E->nuac}\t{$E->nom}\t{$E->ecue}\t{$E->composante}\t{$E->cursus}\t{$E->etape}\n";
        }

    }

    /* version d'avant le 5 avril 2025, je ne sais pas à quoi elle est utilisée ?
    public static function activeFoncRef($year){
        $app = \TDS\App::get();

        $baseName = $app::$appName."{$year}";
        $db = new \TDS\Database($baseName, $app::$baseUser, $app::$basePwd, 'localhost' );
        pg_set_client_encoding($db->conn, "UNICODE");

        $foncRefList = $db-> getAll("
            SELECT DISTINCT
                FR.id,
                FR.intitule,
                R.code
            FROM FoncRef as FR
            LEFT JOIN Referentiel as R on FR.referentiel = R.id
            WHERE FR.actif AND FR.id>0
            ORDER BY R.code, FR.intitule
        ");

        echo date("'d/m/Y\t'H:i:s")."\n";
        echo "id_foncref\tintitule\tcode\n";
        foreach($foncRefList as $FR){
            echo "{$FR->id}\t{$FR->intitule}\t{$FR->code}\n";
        }
    }
    */


    public static function activeFoncRef($year){
        $app = \TDS\App::get();

        $baseName = $app::$appName."{$year}";
        $db = new \TDS\Database($baseName, $app::$baseUser, $app::$basePwd, 'localhost' );
        pg_set_client_encoding($db->conn, "UNICODE");

        $foncRefList = $db-> getAll("
            SELECT DISTINCT
                FR.id,
                FR.intitule,
                R.code
            FROM FoncRef as FR
            LEFT JOIN Referentiel as R on FR.referentiel = R.id
            WHERE FR.actif AND FR.id>0
            ORDER BY FR.intitule
        ");

        echo date("\t'd/m/Y\t'H:i:s")."\n";
        echo "id_foncref\tintitule\tcode\n";
        foreach($foncRefList as $FR){
            $intitule = ($FR->intitule === '-')? $FR->code : $FR->intitule;
            echo "{$FR->id}\t{$intitule}\t{$FR->code}\n";
        }
    }


    public static function activeSituationList($year){
        $app = \TDS\App::get();

        $baseName = $app::$appName."{$year}";
        $db = new \TDS\Database($baseName, $app::$baseUser, $app::$basePwd, 'localhost' );
        pg_set_client_encoding($db->conn, "UNICODE");

        $SituationList = $db-> getAll("
            SELECT DISTINCT
                S.id,
                S.nom,
                S.OSE
            FROM Situation as S
            WHERE S.actif AND S.id>0
            ORDER BY S.OSE
        ");


        echo date("'d/m/Y\t'H:i:s")."\n";
        echo "id_situation\tnom\tOSE\n";
        foreach($SituationList as $S){
            echo "{$S->id}\t{$S->nom}\t{$S->ose}\n";
        }
    
    }

    public static function activePersonneBilanList(int $year){
        $app = \TDS\App::get();

        $baseName = $app::$appName."{$year}";
        $db = new \TDS\Database($baseName, $app::$baseUser, $app::$basePwd, 'localhost' );
        pg_set_client_encoding($db->conn, "UNICODE");

        $bilanList = $db-> getAll("
            SELECT DISTINCT
                P.id,
                P.ose,
                P.prenom, 
                p.nom,
                S.nom as statut,
                VPB.heures as solde,
                PC.charge as charge,
                PRH.heures as referentiel,
                PSR.reduction as situation
            FROM Personne as P
            LEFT JOIN StatuT as S on S.id = P.statut
            LEFT JOIN voeu_personne_bilan as VPB on VPB.id = P.id
            LEFT JOIN personne_charge as PC on PC.id = P.id
            LEFT JOIN personne_referentiel_heures as PRH on PRH.id = P.id
            LEFT JOIN personne_situation_reduction as PSR on PSR.id = P.id
            WHERE P.actif AND P.id>0
            ORDER BY P.nom, P.prenom
        ");


        echo date("'d/m/Y\t'H:i:s")."\n";
        echo "id\tose\tprenom\tnom\tstatut\tsolde\tcharge\treferentiel\tsituation\n";
        foreach($bilanList as $B){
            echo "{$B->id}\t{$B->ose}\t{$B->prenom}\t{$B->nom}\t{$B->statut}\t{-$B->solde}\t{$B->charge}\t{$B->referentiel}\t{$B->situation}\n";
        }
    
    }


    public static function getEmail($id){
        $app = \TDS\App::get();
        $P = $app::NS('Personne')::load($id);
        echo $P->email;
    }

    public static function isUIDInBase($uid){
        $app = \TDS\App::get();

        $PL = $app::NS('Personne')::loadWhere("uid='{$uid}'");
        echo 1==count($PL)?"y":"n";
    }

    public static function structOSEEtape($code){
        $app = \TDS\App::get();

        $structOSE = new \base\Struct(2024, "OSE");
        $etapeList = \EtapeQuery::create()
        ->filterByCode($code.'%', \Propel\Runtime\ActiveQuery\Criteria::LIKE)
        ->find();

        $res = [];
        foreach($etapeList as $etape){
            $res[] = [
                'nom' => $etape->getNom(),
                'code' => $etape->getCode(),
                'type' => $etape->getType(),
                'niveau' => $etape->getNiveau(),
                'domaine' => $etape->getDomaine(),
                'ose_id' => $etape->getOseId(),
                'ose_nom' => $etape->getOseNom(),
            ];
        }
        echo json_encode($res);
    }

    public static function structOSEEcue($code){
        $app = \TDS\App::get();

        $structOSE = new \base\Struct(2024, "OSE");
        $ecueList = \ECUEQuery::create()
        ->filterByCode($code.'%', \Propel\Runtime\ActiveQuery\Criteria::LIKE)
        ->find();

        $res = [];
        foreach($ecueList as $ecue){
            $res[] = [
                'nom' => $ecue->getNom(),
                'code' => $ecue->getCode(),
                'periode' => $ecue->getPeriode(),
                'ose_nom' => $ecue->getOseNom(),
                'hCM' => $ecue->gethCM(),
                'gCM' => $ecue->getgCM(),
                'hTD' => $ecue->gethTD(),
                'gTD' => $ecue->getgTD(),
                'hTP' => $ecue->gethTP(),
                'gTP' => $ecue->getgTP(),
                'hCMTD' => $ecue->gethCMTD(),
                'gCMTD' => $ecue->getgCMTD(),
                'hExtra' => $ecue->gethExtra(),
                'gExtra' => $ecue->getgExtra(),
            ];
        }
        echo json_encode($res);
    }


    public static function getCurrentYear(){
        $app = \TDS\App::get();
        echo $app::$currentYear;
    }
    

    public static function test1(){
        var_dump($_POST);
    }
}