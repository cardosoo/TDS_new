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
    public static function allEcuesOSE($year){
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

    
    public static function activeUserList($year){
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

    public static function activeTeachingList($year){
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


}
