<?php

namespace base\Controllers;

use TDS\Database;
use TDS\Query;
use TDS\Utils;

class EnseignementController extends \TDS\Controller {

    public static function getOptions($id){
        $app = \TDS\App::get();

        $E = $app::load('Enseignement', $id);
        $asideTabList= [];
        if ($E->withCommentaires()){
            $asideTabList[]=[
                'name' => 'Commentaires',
                'label' => 'Commentaires ('.count(array_filter($E->commentaire_enseignementList, '\TDS\App::isActive')).')',
                'template' => 'enseignement/aside_commentaires.html.twig',
                'canEdit' => $E->canEditCommentaires(),
            ];
        } 
        if ($E->withDocuments() ){
            $docList = $E->getDocumentList();
            $asideTabList[]=[
                'name' => 'Documents',
                'label' => 'Documents ('.count($docList).')',
                'template' => 'enseignement/aside_documents.html.twig',
                'docList' => $docList, 
                'canEdit' => $E->canEditDocuments(),
            ];
        }

        if ($E->withOSE()){

            $asideTabList[]=[
                'name' => 'OSE',
                'label' => 'OSE',
                'fromOSE' => $E->comparaisonOSE(), 
                'template' => 'enseignement/aside_OSE.html.twig',
                'canEdit' => $E->canEditOSE(),
            ];
        }

        if ($E->withDetails()){
            $asideTabList[]=[
                'name' => 'Détails',
                'label' => 'Détails',
                'template' => 'enseignement/aside_details.html.twig',
                'canEdit' => $E->canEditDetails(),
            ];
        }

        return [ 
            'E' => $E, 
            'nbetu' => $E->getNbEtu(),
            'V' => $E->getVoeu(),  
            'VL' => $E->voeuList,
            'C' => $E->getCharge(),
            'asideTabList' =>$asideTabList,
        ];
    }

    public static function setCmpl($id, $isVoeu){
        $app = \TDS\App::get();
        $app::$cmpl["withJQuery"]=true;
        if ($isVoeu){
            $app::$cmpl["withKnockout"]=true;
        }

        $appName = $app::$appName;
        $app::$toCRUD="/{$appName}/CRUD/Enseignement/$id";
    }

    public static function fiche(int $id, $isVoeu = false){
        $app = \TDS\App::get();

        $options = self::getOptions($id);
        self::setCmpl($id, $isVoeu);

        $voeu = $isVoeu?"Voeu":"";
        echo $app::$viewer->render("enseignement/fiche{$voeu}.html.twig", $options);
    }

    protected static function verificationUsage(int $id, $error='404'){
        $app = \TDS\App::get();
        // vérifications d'usage
        
        
        if (!$app::$auth->isInBase()) Utils::error($error);
        if (!$app::$phaseList[$app::$phase]->withAjouterVoeux)  Utils::error($error);
        $enseignement = $app::load('Enseignement', $id);
//        if (! $enseignement->attribuable) Utils::error($error);
//        if (! $enseignement->actif) Utils::error($error);
        if (! $enseignement->isAttribuable()) Utils::error($error); 

    }

    public static function voeu(int $id){
        self::verificationUsage($id);
        self::fiche($id, true);
    }

    protected static function getVoeu(int $id){
        $app = \TDS\App::get();
        $userId = $app::$auth->user->id;
        $q = new Query($app::NS('Voeu'), 'V');
        $voeu = $q->addSQL("
            WHERE {$q->V_personne} = {$userId}
            AND {$q->V_enseignement} = {$id}
        ")->getOne();

        if (is_null($voeu)) { // alors c'est une création
            $V = $app::NS('Voeu');
            $voeu = new $V();
            $voeu->personne= $userId;
            $voeu->enseignement= $id;
        }
        return $voeu;
    }

    public static function addOrChangeVoeu(int $id){
        self::verificationUsage($id, 'XHR');

        // lecture du voeu actuel si il existe
        $voeu = self::getVoeu($id);

        // lecture des données en POST
        $args = [
            'cm'   => FILTER_VALIDATE_FLOAT,
            'td'      => FILTER_VALIDATE_FLOAT,
            'ctd'     => FILTER_VALIDATE_FLOAT,
            'tp'      => FILTER_VALIDATE_FLOAT,
            'extra'   => FILTER_VALIDATE_FLOAT,
            'bonus'   => FILTER_VALIDATE_FLOAT,
            'correspondant' => FILTER_VALIDATE_BOOLEAN,
        ];
        $propList = filter_input_array(INPUT_POST, $args);

        $voeu->cm = $propList['cm']?$propList['cm']:0;
        $voeu->ctd = $propList['ctd']?$propList['ctd']:0;
        $voeu->td = $propList['td']?$propList['td']:0;
        $voeu->tp = $propList['tp']?$propList['tp']:0;
        $voeu->extra = $propList['extra']?$propList['extra']:0;
        $voeu->bonus = $propList['bonus']?$propList['bonus']:0;
        $voeu->correspondant = $propList['correspondant']?"t":"f";

        $voeu->save();
        self::fiche($id);
    }

    public static function deleteVoeu(int $id){
        self::verificationUsage($id, 'XHR');
        $voeu = self::getVoeu($id);
        $voeu->delete();
        echo "success";
    }


    public static function historique($N, $Y){
        $app = \TDS\App::get();
        $baseName = $app::$appName."{$Y}";
        $db = new Database($baseName, $app::$baseUser, $app::$basePwd, 'localhost' );
        //pg_set_client_encoding($db->conn, "UNICODE");

        $V =  $db-> fetchAll("
                SELECT 
                V.*,
                VBL.heures as heures,
                P.prenom as prenom,
                P.nom as nom,
                P.id as id
            FROM voeu as V
            LEFT JOIN personne as P on P.id = V.personne
            LEFT JOIN voeu_bilan_ligne as VBL on VBL.id = V.id
            WHERE V.enseignement = {$N}
        ");
        $cmpl['annee']=$Y;
        echo $app::$viewer->render('enseignement/historique.html.twig', [ 'cmpl' => $cmpl, 'V' => $V]);
    }


    public static function saveSyllabus($id){
        $app = \TDS\App::get();

        $E = $app::load('Enseignement', $id);
        if (! $E->canEdit()){
            return "Pas les droits pour la modification";
        };

        $syllabus = filter_input(INPUT_POST, 'syllabus');
        $E->syllabus = $syllabus;
        $E->save();
        echo "success";
    }

    public static function editBesoins($id){
        $app = \TDS\App::get();

        $E = $app::load('Enseignement', $id);
        if  ( ! $E->canEditDetails()){
            echo $app::$viewer->render('errorNoAuthorization.html.twig');
            exit();
        }

        echo $app::$viewer->render('enseignement/edit_besoins.html.twig', ['E' => $E]);
    }

    public static function saveEditBesoins($id){
        $app = \TDS\App::get();

        $E = $app::load('Enseignement', $id);
        if  ( ! $E->canEditDetails() ){
            echo $app::$viewer->render('errorNoAuthorization.html.twig');
            exit();
        }

        // lecture des données en POST
        $args = [
            'cm'      => FILTER_VALIDATE_FLOAT,
            'td'      => FILTER_VALIDATE_FLOAT,
            'ctd'     => FILTER_VALIDATE_FLOAT,
            'tp'      => FILTER_VALIDATE_FLOAT,
            'extra'   => FILTER_VALIDATE_FLOAT,
            's_cm'      => FILTER_VALIDATE_FLOAT,
            's_td'      => FILTER_VALIDATE_FLOAT,
            's_ctd'     => FILTER_VALIDATE_FLOAT,
            's_tp'      => FILTER_VALIDATE_FLOAT,
            's_extra'   => FILTER_VALIDATE_FLOAT,
            'd_cm'      => FILTER_VALIDATE_FLOAT,
            'd_td'      => FILTER_VALIDATE_FLOAT,
            'd_ctd'     => FILTER_VALIDATE_FLOAT,
            'd_tp'      => FILTER_VALIDATE_FLOAT,
            'd_extra'   => FILTER_VALIDATE_FLOAT,
            'i_cm'      => FILTER_VALIDATE_FLOAT,
            'i_td'      => FILTER_VALIDATE_FLOAT,
            'i_ctd'     => FILTER_VALIDATE_FLOAT,
            'i_tp'      => FILTER_VALIDATE_FLOAT,
            'i_extra'   => FILTER_VALIDATE_FLOAT,
            'n_cm'      => FILTER_VALIDATE_FLOAT,
            'n_td'      => FILTER_VALIDATE_FLOAT,
            'n_ctd'     => FILTER_VALIDATE_FLOAT,
            'n_tp'      => FILTER_VALIDATE_FLOAT,
            'n_extra'   => FILTER_VALIDATE_FLOAT,
            'bonus'     => FILTER_VALIDATE_FLOAT,
        ];
        $propList = filter_input_array(INPUT_POST, $args);

        $E->cm = $propList['cm']?$propList['cm']:0;
        $E->ctd = $propList['ctd']?$propList['ctd']:0;
        $E->td = $propList['td']?$propList['td']:0;
        $E->tp = $propList['tp']?$propList['tp']:0;
        $E->extra = $propList['extra']?$propList['extra']:0;
        $E->s_cm = $propList['s_cm']?$propList['s_cm']:1;
        $E->s_ctd = $propList['s_ctd']?$propList['s_ctd']:1;
        $E->s_td = $propList['s_td']?$propList['s_td']:1;
        $E->s_tp = $propList['s_tp']?$propList['s_tp']:1;
        $E->s_extra = $propList['s_extra']?$propList['s_extra']:1;
        $E->d_cm = $propList['d_cm']?$propList['d_cm']:0;
        $E->d_ctd = $propList['d_ctd']?$propList['d_ctd']:0;
        $E->d_td = $propList['d_td']?$propList['d_td']:0;
        $E->d_tp = $propList['d_tp']?$propList['d_tp']:0;
        $E->d_extra = $propList['d_extra']?$propList['d_extra']:0;
        $E->i_cm = $propList['i_cm']?$propList['i_cm']:0;
        $E->i_ctd = $propList['i_ctd']?$propList['i_ctd']:0;
        $E->i_td = $propList['i_td']?$propList['i_td']:0;
        $E->i_tp = $propList['i_tp']?$propList['i_tp']:0;
        $E->i_extra = $propList['i_extra']?$propList['i_extra']:0;
        $E->n_cm = $propList['n_cm']?$propList['n_cm']:1;
        $E->n_ctd = $propList['n_ctd']?$propList['n_ctd']:1;
        $E->n_td = $propList['n_td']?$propList['n_td']:1;
        $E->n_tp = $propList['n_tp']?$propList['n_tp']:1;
        $E->n_extra = $propList['n_extra']?$propList['n_extra']:1;
        $E->bonus = $propList['bonus']?$propList['bonus']:0;

        $E->save();

         echo $app::$viewer->render('enseignement/edit_besoins.html.twig', ['E' => $E]);
   }


}