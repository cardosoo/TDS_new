<?php

namespace base\Controllers;

use base\Model\voeu_bilan_ligne;
use base\Router;
use stdClass;
use TDS\Database;

class PersonneController extends \TDS\Controller {

    static $args = [
        'nom'           => FILTER_UNSAFE_RAW,
        'prenom'        => FILTER_UNSAFE_RAW,
        'email'     => FILTER_UNSAFE_RAW,
        'tel1'     => FILTER_UNSAFE_RAW,
        'tel2'     => FILTER_UNSAFE_RAW,
        'labo'          => [
            'filter' => FILTER_VALIDATE_INT,
            'flags'  => FILTER_REQUIRE_SCALAR,
        ],
        'info'     => FILTER_UNSAFE_RAW,
        'adresse'     => FILTER_UNSAFE_RAW,
    ];


    public static function getOptions($id){
        $app = \TDS\App::get();
        $P = $app::load('Personne', $id);
        $P->voeuList;

        $laboNS = $app::NS('Labo');
        $order = $laboNS::entityDef['nom']['dbName'];
        $laboList = $laboNS::loadWhere("actif", [ $order ]);
        $asideTabList= [];
        
        if ($P->withCommentaires()){
            $asideTabList[]=[
                'name' => 'Commentaires',
                'label' => 'Commentaires ('.count(array_filter($P->commentaire_personneList, '\TDS\App::isActive')).')',
                'template' => 'personne/aside_commentaires.html.twig',
                'hasChangedCall' => 'hasChangedCommentaires',
                'canEdit' => $P->canEditCommentaires(),
            ];
        } 

        if ($P->withDocuments() ){
            $docList = $P->getDocumentList();
            $asideTabList[]=[
                'name' => 'Documents',
                'label' => 'Documents ('.count($docList).')',
                'template' => 'personne/aside_documents.html.twig',
                'docList' => $docList, 
                'canEdit' => $P->canEditDocuments(),
            ];
        }

        if ($P->withDetailsRH() ){
            $statutList = $app::NS('Statut')::loadWhere('actif',['nom']);
            $situationList = $app::NS('Situation')::loadWhere('actif', ['ose']);
            $referentielList = $app::NS('FoncRef')::loadWhere('actif', ['id']);
            $initialSearch= "";
            if ( !empty($P->ose)){
                $initialSearch=$P->ose;
            } else if (!empty($P->uid)) {
                $initialSearch = $P->uid;
            } else {
                $initialSearch = "{$P->prenom} {$P->nom}";
            }
            $asideTabList[]=[
                'name' => 'DetailsRH',
                'label' => 'Détails RH',
                'template' => 'personne/aside_detailsRH.html.twig',
                'statutList' => $statutList, 
                'situationList' => $situationList,
                'referentielList' => $referentielList,
                'initialSearch' => $initialSearch,
                'hasChangedCall' => 'hasChangedDetailsRH',
                'canEdit' => $P->canEditDetailsRH(),
            ];
        }

        if ($P->withStages()){
            $SL = $app::NS('personne_foncRef')::loadWhere("actif and foncref = 4 and personne = {$P->id}");

            $stageList = new stdClass;
            $sl = (object)['commentaire'=> '', 'volume'=> 0];
            $volume = 0;
            $error = '';
            if (count($SL)>1){
                $error = "Il y a plusieurs entrées pour le ref... c'est un problème !";
            } else {
                if (count($SL)==1){
                    $stageList = json_decode($SL[0]->commentaire, true);
                    $volume = $SL[0]->volume;
                    $sl = $SL[0];
                }
            }

            $asideTabList[]=[
                'name' => 'Stages',
                'label' => 'Stages',
                'stageList' => $stageList,
                'SL' => $sl,
                'volume' => $volume,
                'error' => $error,
                'template' => 'personne/aside_stages.html.twig',
                'hasChangedCall' => 'hasChangedStages',
                'canEdit' => $P->canEditStages(),
            ];
        }

        if ($P->withOSE()){

            $oseNS = "{$app::$appName}\OSE";
            $ose = new $oseNS;

            // O.C. Ici je ne comprends pas... en fait on ne fait rien de cette données présente dans OSE.
            // Cela pourrait permettre une comparaison immédiate ! par le côté enseignant !
            $fromOSEList = $ose->getService($P->ose);
            //var_dump($fromOSEList);
            $fromOSEList = [];
            foreach($P->voeuList as $V){
                $tmp = [];
                $codeList = explode('|',$V->enseignement->enseignement_structure->code);
                foreach($codeList as $code){
                    $co = explode('_', $code);
                    $ecue = $co[1];
                    $tmp[$ecue] = $ose->findECUE($ecue);
                }
                $fromOSEList[$V->id] = $tmp; 
            }


            $asideTabList[]=[
                'name' => 'OSE',
                'label' => 'OSE',
                'fromOSEList' => $fromOSEList,
                'template' => 'personne/aside_OSE.html.twig',
                'canEdit' => $P->canEditOSE(),
            ];
        }

        return [ 
            'personne' => $P, 
            'canEdit' => $P->canEdit(), 
            'canHistorique' => $P->canHistorique(),
            'laboList' => $laboList,
            'asideTabList' =>$asideTabList,
        ];
    }


    public static function fiche($id){
        $app = \TDS\App::get();

        $options = self::getOptions($id);

        $appName = $app::$appName;

        $app::$cmpl["withJQuery"]=true;
        $app::$cmpl['withDataTables'] = true; 
        $app::$cmpl['withKnockout'] = true;
        
        $app::$toCRUD="/{$appName}/CRUD/Personne/{$id}";
        echo $app::$viewer->render("personne/fiche.html.twig", $options);
    }

    public static function historique($id, $year){
        $app = \TDS\App::get();

       $baseName = $app::$appName."{$year}";
        $db = new Database($baseName, $app::$baseUser, $app::$basePwd, 'localhost' );
        pg_set_client_encoding($db->conn, "UNICODE");
      
        $V = $db->fetchAll("
            SELECT 
                V.*,
                VBL.heures as heures,
                E.nuac as nuac,
                E.code as code,
                E.nom as nom,
                E.intitule as intitule,
                E.id as id
            FROM voeu as V
            LEFT JOIN enseignement as E on E.id = V.enseignement
            LEFT JOIN voeu_bilan_ligne as VBL on VBL.id = V.id
            WHERE V.personne = {$id}
            ");
        $cmpl['annee']=$year;
        echo $app::$viewer->render('personne/historique.html.twig', [ 'cmpl' => $cmpl, 'V' => $V]);       
    }


    private static function getHistoryBefore2019($id, $year){
        $app = \TDS\App::get();

        $baseName = $app::$appName."{$year}";
        $db = new Database($baseName, $app::$baseUser, $app::$basePwd, 'localhost' );
        pg_set_client_encoding($db->conn, "UNICODE");

        // récupération des services d'enseignement
        $VList = $db->getAll("
        SELECT 
            E.id as id,
            E.nuac as nuac,
            E.intitule as intitule,
            E.nuac as code,
            VDH.cm,
            VDH.ctd,
            VDH.td,
            VDH.tp,
            VDH.extra,
            VDH.bonus,
            VBL.heures as hetd,
            ES.cursus,
            ES.maquette
        FROM voeu as V
        LEFT JOIN voeu_detail_heures as VDH on VDH.id = V.id
        LEFT JOIN enseignement as E on E.id = V.enseignement
        LEFT JOIN enseignement_structure as ES on ES.id = E.id
        LEFT JOIN voeu_bilan_ligne as VBL on VBL.id = V.id
        WHERE V.personne = {$id}
        AND V.actif
        AND E.actif
        ");

        // si c'est du bonus, il faut sans doute le sortir du service d'enseignement
        // il faut peut-être aussi regarder si il s'git d'un report

        // récupération du statut avec la charge associée
        $statut = $db->getOne("
            SELECT
                P.id as id,
                P.nom as nom,
                P.prenom as prenom,
                S.nom,
                s.obligation,
                PC.charge as charge
            FROM Personne as P
            LEFT JOIN statut as S on P.statut = S.id
            LEFT JOIN personne_charge as PC on PC.id = P.id
            WHERE P.id = {$id}
            AND P.actif

        ");

        // récupération des situations particulières
        $situationList = $db->getAll("
            SELECT
                S.nom,
                PS.reduction,
                PS.commentaire
            FROM personne_situation as PS
            LEFT JOIN situation as S on PS.situation = S.id
            WHERE PS.personne = {$id}
            AND PS.actif
            AND PS.id > 0
            AND S.actif
            AND S.id > 0
        ");
        // pour les situation des anciennes 
        $situation = $db->getAll("
            SELECT
                S.nom,
                S.reduction,
                '-' as commentaire
            FROM personne as P
            LEFT JOIN situation as S on P.situation = S.id
            WHERE P.id = {$id}
            AND S.id >0 
            AND P.actif
            AND S.actif
        ");
        if (count($situation) == 1){
            $situationList[] = $situation[0];
        }

        // récupération des éléments du référentiel
        if ($year<=2020){
            $referentielList = [];
        } else {
            $referentielList = $db->getAll("
                SELECT
                    PFR.volume,
                    PFR.commentaire,
                    FR.referentiel
                FROM personne_foncref as PFR
                LEFT JOIN foncref as FR on FR.id = PFR.foncref
                WHERE PFR.personne = {$id}
                AND PFR.actif
                AND PFR.id > 0
                AND FR.actif
                AND FR.id > 0
            ");
        }
        /*************************************************************************
         * Modification du résultat des requêtes pour faire coller 
         * l'ancienne architecture de la base de données 
         *************************************************************************/
        // modification de $VList pour permettre de modifier les PCC comptées dans les voeux comme référentiel et les reports comme situation particulière
        $nVList = [];
        foreach($VList as $V){
            // on identifie une PCC lorsqu'il n'y a que du bonus...
            if ( ($V->bonus + $V->extra != 0) && ($V->cm == 0 ) && ($V->ctd == 0) & ($V->td == 0) & ($V->tp == 0)){
                $referentielList[] =  [
                    'volume' => $V->bonus + $V->extra,
                    'commentaire' => $V->intitule,
                ];
            } else {
                $nVList[] = $V;
            }
        }

        return [
            "VList" => $nVList, 
            "statut" => $statut, 
            "situationList" => $situationList, 
            "referentielList" => $referentielList
        ];

    }




    // renvoi l'historique en heures avec le bilan en hETD
    // renvoi également les situatons particulières
    // renvoi les éventuels éléments du référentiel
    // renvoi le statut avec les obligations
    // cette version est en cours de modification pour prendre en compte la nouvelle structure des enseignements
    private static function getActualHistory($id, $year){
        $app = \TDS\App::get();

        $baseName = $app::$appName."{$year}";
        $db = new Database($baseName, $app::$baseUser, $app::$basePwd, 'localhost' );
        pg_set_client_encoding($db->conn, "UNICODE");

        // récupération des services d'enseignement
        $VList = $db->getAll("
        SELECT 
            E.id as id,
            E.nuac as nuac,
            E.intitule as intitule,
            E.code as code,
            VDH.cm,
            VDH.ctd,
            VDH.td,
            VDH.tp,
            VDH.extra,
            VDH.bonus,
            VBL.heures as hetd,
            ES.cursus,
            ES.maquette
        FROM voeu as V
        LEFT JOIN voeu_detail_heures as VDH on VDH.id = V.id
        LEFT JOIN enseignement as E on E.id = V.enseignement
        LEFT JOIN enseignement_structure as ES on ES.id = E.id
        LEFT JOIN voeu_bilan_ligne as VBL on VBL.id = V.id
        WHERE V.personne = {$id}
        AND V.actif
        AND E.actif
        ");

        // si c'est du bonus, il faut sans doute le sortir du service d'enseignement
        // il faut peut-être aussi regarder si il s'git d'un report

        // récupération du statut avec la charge associée
        $statut = $db->getOne("
            SELECT
                P.id as id,
                P.nom as nom,
                P.prenom as prenom,
                S.nom,
                s.obligation,
                PC.charge as charge
            FROM Personne as P
            LEFT JOIN statut as S on P.statut = S.id
            LEFT JOIN personne_charge as PC on PC.id = P.id
            WHERE P.id = {$id}
            AND P.actif

        ");

        // récupération des situations particulières
        $situationList = $db->getAll("
            SELECT
                S.nom,
                PS.reduction,
                PS.commentaire
            FROM personne_situation as PS
            LEFT JOIN situation as S on PS.situation = S.id
            WHERE PS.personne = {$id}
            AND PS.actif
            AND PS.id > 0
            AND S.actif
            AND S.id > 0
        ");
        // pour les situation des anciennes 
        $situation = $db->getAll("
            SELECT
                S.nom,
                S.reduction,
                '-' as commentaire
            FROM personne as P
            LEFT JOIN situation as S on P.situation = S.id
            WHERE P.id = {$id}
            AND S.id >0 
            AND P.actif
            AND S.actif
        ");
        if (count($situation) == 1){
            $situationList[] = $situation[0];
        }

        // récupération des éléments du référentiel
        if ($year<=2020){
            $referentielList = [];
        } else {
            $referentielList = $db->getAll("
                SELECT
                    PFR.volume,
                    PFR.commentaire,
                    FR.referentiel
                FROM personne_foncref as PFR
                LEFT JOIN foncref as FR on FR.id = PFR.foncref
                WHERE PFR.personne = {$id}
                AND PFR.actif
                AND PFR.id > 0
                AND FR.actif
                AND FR.id > 0
            ");
        }
        /*************************************************************************
         * Modification du résultat des requêtes pour faire coller 
         * l'ancienne architecture de la base de données 
         *************************************************************************/
        // modification de $VList pour permettre de modifier les PCC comptées dans les voeux comme référentiel et les reports comme situation particulière
        $nVList = [];
        foreach($VList as $V){
            // on identifie une PCC lorsqu'il n'y a que du bonus...
            if ( ($V->bonus + $V->extra != 0) && ($V->cm == 0 ) && ($V->ctd == 0) & ($V->td == 0) & ($V->tp == 0)){
                $referentielList[] =  [
                    'volume' => $V->bonus + $V->extra,
                    'commentaire' => $V->intitule,
                ];
            } else {
                $nVList[] = $V;
            }
        }

        return [
            "VList" => $nVList, 
            "statut" => $statut, 
            "situationList" => $situationList, 
            "referentielList" => $referentielList
        ];
    } 

    public static function historiqueComplet($id){
        $app = \TDS\App::get();

        $P = $app::NS('Personne')::load($id);   
        if ( ! $P->canHistorique()){
            echo $app::$viewer->render('error404.html.twig');
            exit();
        }

        $cYear = $app::$historyYearList[0]+1;
        $history[$cYear] = self::getActualHistory($id, $cYear); 
        foreach($app::$historyYearList as $year){
            if ($year<2019){
                $history[$year] = self::getHistoryBefore2019($id, $year); 
                continue;
            }
            $history[$year] = self::getActualHistory($id, $year); 

        }

        $app::$cmpl["withJQuery"]=true;
        $app::$cmpl["withDataTables"]=true;
// var_dump($history);
       echo $app::$viewer->render('personne/historiqueComplet.html.twig', ['P' => $P, 'HL' => $history ]);
    }

    public static function edit($id){
        $app = \TDS\App::get();

        $personne = $app::NS('Personne')::load($id);   
        if (! $personne->canEdit()){
            echo "Vous n'avez pas le droit de modifier cela !";
            exit();
        }
        
        $propList = filter_input_array(INPUT_POST, self::$args);
        foreach($propList as $key => $prop){
            $personne->$key = $prop;
        }

        $personne->save();

        if (count($_FILES)==0) {
            echo "success";
            exit();
        }
        if (is_array($_FILES["upload_file"]["tmp_name"] ) ) {
            echo "success";
            exit();
            
        }
        
        // ici il faut changer le photoDir
        if (! move_uploaded_file($_FILES["upload_file"]["tmp_name"], Router::getPhotosPath()."/photo_{$id}.jpg") ) {
            echo "Il y a un problème dans le chargement de la photo {$_FILES["upload_file"]["tmp_name"]} - {Router::getPhotosPath()}/photo_{$id}.jpg";
            exit();
        };
        
        echo "success";
    }

    public static function saveDetails($id){
        $app = \TDS\App::get();

        $P = $app::NS('Personne')::load($id);   
        if (! $P->canEditDetailsRH()){
            echo "Vous n'avez pas le droit de modifier cela !";
            exit();
        }

        $P->actif = !is_null(filter_input(INPUT_POST, 'actif'));
        $P->ose = filter_input(INPUT_POST, 'ose', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $P->uid = filter_input(INPUT_POST, 'uid', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $P->statut =  filter_input(INPUT_POST, 'statut', FILTER_VALIDATE_INT);

        $situationList = filter_input(INPUT_POST, 'situation', FILTER_DEFAULT , FILTER_REQUIRE_ARRAY);
        $referentielList = filter_input(INPUT_POST, 'referentiel', FILTER_DEFAULT , FILTER_REQUIRE_ARRAY);

        if (!is_null($situationList)){
            foreach($situationList as $situationId => $situation){
                $psNS = $app::NS('personne_situation');
                $ssid = ''.$situationId;
                if ($ssid[0] == '_'){
                    $S = new $psNS;
                    $S->personne=$id;

                } else {
                    $S = $psNS::load($situationId);
                }
                $toDelete=$situation['deleted']=='yes';
                if ($toDelete) {
                    $S->delete();
                } else {
                    $S->situation = $situation['situation'];
                    $S->debut = $situation['debut'];
                    $S->fin = $situation['fin'];
                    $S->reduction = $situation['reduction'];
                    $S->commentaire = $situation['commentaire'];
                    $S->save();
                }
            }
        }
        if (!is_null($referentielList)){
            foreach($referentielList as $referentielId => $referentiel){
                $pfrNS = $app::NS('personne_foncRef');
                $rrid = ''.$referentielId;
                if ($rrid[0] == '_'){
                    $R = new $pfrNS;
                    $R->personne=$id;

                } else {
                    $R = $pfrNS::load($referentielId);
                }
                $toDelete=$referentiel['deleted']=='yes';
                if ($toDelete) {
                    $R->delete();
                } else {
                    $R->foncref = $referentiel['referentiel'];
                    $R->volume = $referentiel['volume'];
                    $R->commentaire = $referentiel['commentaire'];
                    $R->save();
                }
            }
        }

        $P->save();
        echo "success";
    }

    // Cela n'a rien à faire ici... 
    // Ce devrait être dans tssdv non ?
    public static function validerTousLesVoeux(){
        $app = \TDS\App::get();

        $personne = $app::NS('Personne')::load($app::$auth->user->id);
        foreach($personne->voeuList as $V){
            if ($V->etat_ts<2){
                $V->etat_ts=2;
                $V->save();
            }
        }
        
        $app::$router->redirect("/{$app::$appName}/personne/{$personne->id}");
    }


    public static function searchLDAP(){

        $app = \TDS\App::get();

        $quoi = filter_input(INPUT_POST, 'searchValue', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $quoi = trim($quoi);

        if (empty($quoi)){
            echo "";
            exit();
        }

        $filter = "(|(uid=*{$quoi}*)(supannAliasLogin=*{$quoi}*)(supannEmpId={$quoi})(cn=*{$quoi}*)(displayName=*{$quoi}*))";        
        
        $rep = null;
        $ldap = new \TDS\LDAPExtern();
        $rep = $ldap->list($filter, ['uid', 'displayName', 'mail', 'eduPersonPrimaryAffiliation', 'eduPersonAffiliation', 'supannAliasLogin', 'supannEmpId'], 10);
        $rep = $ldap->reformat($rep);

        // Ici on récupère la liste des uid qui sont présents dans la base de données
        // sous forme d'un tableau 
        $uidList = [];
        foreach($rep as $r){
            $uidList[]=$r->uid;
        }
        $uidL = join("', '",$uidList);

        if (!empty($uidL)){
            $q = new \TDS\Query($app::NS('Personne'), 'P', ['uid']);
            $q->addSQL("WHERE {$q->P_uid} in ('{$uidL}')");
            $r = $q->exec();
            // pour les uid qui sont dans la base on ajoute un elem inBase qui contient l'id de la personne
            foreach($r as $elm){
                $rep[$elm['p']->uid]->inBase = $elm['p']->id;
            }
        }


        // Ici il faut remettre en forme les enregistrements et voir si ils existe dans la base de données...
        echo $app::$viewer->render('personne/searchLDAP.html.twig', ['ldapList' => $rep, 'search' =>  $quoi ]);
    }

    public static function searchLDAPNumetu(){
        //echo "Je suis dans \\base\\PersonneControlleur::searchLDAPNumetu";
        //exit();

        $app = \TDS\App::get();

        $quoi = filter_input(INPUT_POST, 'searchValue', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $quoi = trim($quoi);



        if (empty($quoi)){
            echo '';//json_encode([]);
            exit();
        }

        $filter = "(supannEtuId={$quoi})";        
        
        $rep = null;
        $ldap = new \TDS\LDAPExtern();
        $rep = $ldap->list($filter, ['uid','displayName', 'supannEtuEtape'], 1);
        $rep = $ldap->reformat($rep);

        if (count($rep) != 1){
            echo '';//json_encode([]);
            exit();
        }

        echo json_encode(reset($rep));
        exit();
    }

    public static function trombinoscope(){
        $app = \TDS\App::get();
        $pList = $app::NS('Personne')::loadWhere("actif", ['nom', 'prenom']);

        // Ici il faut remettre en forme les enregistrements et voir si ils existe dans la base de données...
        echo $app::$viewer->render('personne/trombinoscope.html.twig', ['pList' => $pList ]);
    }


    public static function saveStages($id){
        $app = \TDS\App::get();
        $SL = $app::NS('personne_foncRef')::loadWhere("actif and foncref = 4 and personne = {$id}");

        if (count($SL)>1){
            echo "Il y a plusieurs entrées pour le ref... c'est un problème !";
            exit();
        } 
        $new = count($SL)!=1;
        if (!$new){
            $S = $SL[0];
        } else {
            $frNS = $app::NS('personne_foncRef');
            $S = new $frNS;
            $S->personne = $id;
            $S->foncref = 4;
            $new = true;
        }

        $S->volume =  filter_input(INPUT_POST, 'volume', FILTER_VALIDATE_INT);
        $stages = filter_input(INPUT_POST, 'stages', FILTER_DEFAULT , FILTER_REQUIRE_ARRAY);

        if (is_null($stages)){
            if (!$new ){
                $S->delete();
            }
            echo "success";   
            exit();            
        }
        $stagesJSON = json_encode($stages);
        $S->commentaire = $stagesJSON;
    
        $S->save();
        echo "success";   
    }

}