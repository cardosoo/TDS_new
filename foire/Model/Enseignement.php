<?php
namespace foire\Model;

use TDS\Database;
use TDS\Query;

class Enseignement extends \base\Model\Enseignement implements \Model\_Enseignement_interface_ {
    use \Model\_Enseignement_;

    public function getParentList(){
        $heritageList = $this->__get("Heritage_Parents");
        $parentList = [-1];
        foreach($heritageList as $heritage){
            $parentList[] = $heritage->parent->id;
        }
        return $parentList;
    }

    public function computeAnciennete($personne = null){
        $app = \TDS\App::get();
        $P = $personne ?? $app::$auth->user;
        $parentList = $this->getParentList();
        $parentlist = join(', ', $parentList);

        $dbName =  $app::$appName.($app::$currentYear-1);
        $db = new Database($dbName, $app::$baseUser, $app::$basePwd, 'localhost' );
        $sql = "
        SELECT max(anciennete) as anc 
        FROM voeu 
        WHERE voeu.id >0
        AND  voeu.personne={$P->id}
        AND  (
                voeu.enseignement = {$this->id}
            OR voeu.enseignement in ({$parentlist})
        )
        ";

        $A = $db->getOne($sql)->anc;
        $anciennete = isset($A)?$A+1:0;
        return $anciennete;
    }

    public function getPanier($personne = null){   
        $app = \TDS\App::get();

        if (! $app::$auth->inBase) return null; // OC
        $P = $personne ?? $app::$auth->user;
        $q = new Query($app::NS('Panier'), 'P');
        $panier = $q->addSQL("
            WHERE {$q->P_personne} = {$P->id}
            AND {$q->P_enseignement} = {$this->id}
        ")->getOne();
        return $panier;
    }

    public function getPanierListActif(){
        $app = \TDS\App::get();
        
        $q = new Query($app::NS('Panier'), 'P');
        $panierListActif = $q->join('P.personne', 'PE')->addSQL("
            WHERE {$q->P_enseignement} = {$this->id}
            AND {$q->PE_actif}
        ")->exec();
        return $panierListActif;
    }

/*
    protected static function complement1Search(){
        return " 
                CONCAT(round(ENS.cm*100)/100,    '+', round(VEB.cm*100)/100   , '/', round(VEBP.cm*100)/100) as mCM,
                CONCAT(round(ENS.ctd*100)/100,   '+', round(VEB.ctd*100)/100  , '/', round(VEBP.ctd*100)/100) as mCTD,
                CONCAT(round(ENS.td*100)/100,    '+', round(VEB.td*100)/100   , '/', round(VEBP.td*100)/100) as mTD,
                CONCAT(round(ENS.tp*100)/100,    '+', round(VEB.tp*100)/100   , '/', round(VEBP.tp*100)/100) as mTP,
                CONCAT(round(ENS.bonus*100)/100, '+', round(VEB.bonus*100)/100, '/', round(VEBP.bonus*100)/100) as mBonus
        ";		
    }

    protected static function complement2Search(){
        return "
        LEFT JOIN voeu_enseignement_bilan_prioritaire as VEBP on VEBP.id = ENS.id
        ";
    }
*/    
}
