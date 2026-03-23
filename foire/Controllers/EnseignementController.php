<?php

namespace foire\Controllers;

use TDS\Query;
use TDS\Utils;

class EnseignementController extends \base\Controllers\EnseignementController {


    public static function getOptions($id){
        $app = \TDS\App::get();

        $options = parent::getOptions($id);
        $options['PL'] = $options['E']->getPanierListActif();
        $options['P'] = $options['E']->getPanier();

        // on regarde si l'utilisateur connecté est responsable d'une étape ou maquette.
        
        $personneNS = $app::NS("Personne");
        $user = $app::$auth->inBase?$app::$auth->user:new $personneNS();
        $respEtape = false;
        $respMaquette = false;


        foreach ($options['E']->ecueList as $ecue){
            $etape  = $ecue->ue->semestre->etape;
            foreach($etape->responsableList as $responsable){
                if ($responsable->personne->id == $user->id){
                    $respEtape= true;
                }
            }
            $maquette = $etape->diplome->maquette;

            $respId = is_null($maquette->responsable)?0:$maquette->responsable->id;
            $respMaquette =  ($user->id == $respId);
            $respId = is_null($maquette->co_responsable)?0:$maquette->co_responsable->id;
            $respMaquette = $respMaquette || ($user->id == $respId);
        }
 
        $options['visuPanier'] = $respEtape || $respMaquette || $app::$auth->isAdmin;
        $options['respEtape'] = $respEtape;
        $options['respMaquette'] = $respMaquette;

        return $options;
    }

    public static function fiche(int $id, $isVoeu = false){
        $app = \TDS\App::get();

        $options = self::getOptions($id);
        self::setCmpl($id, $isVoeu);

        $voeu = $isVoeu?"Voeu":"";
        
        echo $app::$viewer->render("enseignement/fiche{$voeu}.html.twig", $options);
    }

    public static function voeu(int $id){

        self::verificationUsage($id);
        self::fiche($id, true);
    }


    protected static function verificationUsage(int $id, $error='404'){
        $app = \TDS\App::get();

        // vérifications d'usage
        if (!$app::$auth->isInBase()) Utils::error($error);
        if (!$app::$phaseList[$app::$phase]->withPanier)  Utils::error($error);
        $enseignement = $app::load('Enseignement', $id);
        if (! $enseignement->attribuable) Utils::error($error);
        if (! $enseignement->actif) Utils::error($error);
    }


    public static function addOrChangeVoeu(int $id){
        parent::verificationUsage($id, 'XHR');

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
        $voeu->correspondant = $propList['correspondant']?true:false;
        
        if ($voeu->id == 0){ // alors c'est une création... il faut calculer l'ancienneté
            $voeu->anciennete = $voeu->enseignement->computeAnciennete();
        }

        $voeu->actif = true;
        $voeu->save();
        self::fiche($id);
    }

    protected static function getPanier(int $id){
        $app = \TDS\App::get();

        $userId = $app::$auth->user->id;
        $q = new Query($app::NS('Panier'), 'P');
        $panier = $q->addSQL("
            WHERE {$q->P_personne} = {$userId}
            AND {$q->P_enseignement} = {$id}
        ")->getOne();

        if (is_null($panier)) { // alors c'est une création
            $P = $app::NS('Panier');
            $panier = new $P();
            $panier->personne= $userId;
            $panier->enseignement= $id;
        }
        return $panier;
    }



    public static function addPanier(int $id){
        self::verificationUsage($id, 'XHR');

        // lecture du panier actuel si il existe
        $panier = self::getPanier($id);

        // lecture des données en POST
        $args = [
            'cm'      => FILTER_VALIDATE_BOOLEAN,
            'td'      => FILTER_VALIDATE_BOOLEAN,
            'ctd'     => FILTER_VALIDATE_BOOLEAN,
            'tp'      => FILTER_VALIDATE_BOOLEAN,
            'commentaire' => FILTER_UNSAFE_RAW | FILTER_FLAG_NO_ENCODE_QUOTES,
        ];
        $propList = filter_input_array(INPUT_POST, $args);

        $panier->cm = $propList['cm']?$propList['cm']:0;
        $panier->ctd = $propList['ctd']?$propList['ctd']:0;
        $panier->td = $propList['td']?$propList['td']:0;
        $panier->tp = $propList['tp']?$propList['tp']:0;
        $panier->commentaire = $propList['commentaire']?$propList['commentaire']:"";

        $panier->save();
        echo "success";
    }

    public static function suppPanier(int $id){
        self::verificationUsage($id, 'XHR');
        $panier = self::getPanier($id);
        $panier->delete();
        echo "success";
    }

    public static function ajaxPanier(){
        $app = \TDS\App::get();

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $E = $app::NS('Enseignement')::load($id);
        echo $app::$viewer->render('enseignement/ajaxPanier.html.twig', ['Panier' => $E->panierList, 'E' => $E ]);        
    }


}
