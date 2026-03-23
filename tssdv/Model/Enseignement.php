<?php
namespace tssdv\Model;

use \TDS\Table;
use \TDS\App;

class Enseignement extends \base\Model\Enseignement implements \Model\_Enseignement_interface_ {
    use \Model\_Enseignement_;



    function getCharge(){
        $app = \TDS\App::get();

        // Calcul de la charge en hETD pour 1 élément du type
        $C = new \stdClass();
        $C->cm    = $app::$hETD['cm']; // 1 // $this->s_cm   *$this->d_cm    *$app::$hETD['cm'];
        $C->ctd   = $app::$hETD['ctd']; // 1; //  $this->s_ctd  *$this->d_ctd   *$app::$hETD['ctd'];
        $C->td    = $app::$hETD['td']; // 1; //  $this->s_td   *$this->d_td    *$app::$hETD['td'];
        $C->tp    = $app::$hETD['tp']; // 1; //  $this->s_tp   *$this->d_tp    *$app::$hETD['tp'];
        $C->extra = $app::$hETD['extra']; // 1; //  $this->s_extra*$this->d_extra *$app::$hETD['extra'];

        return $C;
    } 

    function isAttribuable(){
        $app = \TDS\App::get();
        if (! $app::$auth->inBase) return false;
        $user = $app::load('Personne',$app::$auth->user->id);
        return $this->attribuable and $this->actif and $user->etat_ts and $this->etat_ts;
    }

/*
    protected static function complement1Search(){
        return " 
                CONCAT(round(EBD.cm*100)/100,    '/', round(VEB.cm*100)/100) as mCM,
                CONCAT(round(EBD.ctd*100)/100,   '/', round(VEB.ctd*100)/100) as mCTD,
                CONCAT(round(EBD.td*100)/100,    '/', round(VEB.td*100)/100) as mTD,
                CONCAT(round(EBD.tp*100)/100,    '/', round(VEB.tp*100)/100) as mTP,
                CONCAT(round(EBD.bonus*100)/100, '/', round(VEB.bonus*100)/100) as mBonus,
                ED.quotite as quotite 
        ";		
    }

    protected static function complement2Search(){
        return "
        LEFT JOIN enseignement_domaine as ED on ED.id = ENS.id
        LEFT JOIN enseignement_besoins_detail as EBD on EBD.id = ENS.id
        ";
    }
*/
 


    protected static function buildSelectors(){
        $selectors = parent::buildSelectors();
        $selectors['domaine'] =  filter_input(INPUT_POST,'domaine'          , FILTER_VALIDATE_INT, FILTER_REQUIRE_ARRAY);
        return $selectors;        
    }

    protected static function getJoinList(){
        $joinList = parent::getJoinList();
        //$joinList[] = ['ENS.domaine_enseignementList', 'DEL'];
        return $joinList;
    }


    public static function getHorsDomaine(){
        $app = \TDS\App::get();
        return $app::NS("Enseignement")::loadWhere("
            LEFT JOIN domaine_enseignement as DE on enseignement.id = DE.enseignement
            WHERE DE.enseignement IS NULL
            AND enseignement.actif
            AND enseignement.id>0
        ", null, false);
    }

    // indique si l'utilisateur désigné est responsable d'au moins un des domaines auquel l'enseignement est rattaché
    // si $userId est null alors c'est l'utilisateur loggué qui est testé
    public  function isResponsableDomaine( $userId = null){
        $app = \TDS\App::get();
        if (is_null($userId)){
            $userId = $app::$auth->user->id;
        }
        $isResponsableDomaine =  false;
        foreach($this->__get("domaine_enseignementList") as $DL ){
            foreach($DL->domaine->domaine_responsableList as $R){
                if ($R->responsable->id == $userId) $isResponsableDomaine = true;
            }
        }
        return $isResponsableDomaine;
    }

    public function withDetails(){
        $app = \TDS\App::get();
        $parent = parent::withDetails();
        $isRHEAdmin = isset($app::$auth->roleList['RHEAdmin']);

        $isResponsableDomaine =  self::isResponsableDomaine();
        $isCorrespondantEnseignement = self::isCorrespondantEnseignement();
        return $parent || $isRHEAdmin || $isResponsableDomaine || $isCorrespondantEnseignement;
    }

    public function canEditDetails(){
        return $this->withDetails();
    }

    function completeForCreation(){
        parent::completeForCreation();
        $this->__set("payeur",0);
        $this->__set("etat_ts", 1);
    }


    
}
        