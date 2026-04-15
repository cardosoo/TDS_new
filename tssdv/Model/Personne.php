<?php
namespace tssdv\Model;

use \TDS\Table;
use \TDS\App;

class Personne extends \base\Model\Personne implements \Model\_Personne_interface_ {
    use \Model\_Personne_;


    /**
     *  Renvoi la liste des enseignements  (en fait seulement leur id)
     *  qui ont la personne comme responsable
     */
    public function getEnseignementResponsable($order = 'E.nom'){
        $app= \TDS\App::get();

        return  $app::$db->fetchAll("
        SELECT DISTINCT
            E.id,
            E.nom
        FROM enseignement as E
        LEFT JOIN  voeu as V on V.enseignement = E.id
        WHERE E.id> 0 AND E.actif
        AND V.personne = {$this->id}
        AND V.correspondant
        ORDER BY $order
        ");
    }


    public function getDomaineResponsable($order = 'D.nom'){
        $app= \TDS\App::get();

        return  $app::$db->fetchAll("
        SELECT DISTINCT
            D.id,
            D.nom
        FROM domaine as D
        LEFT JOIN  domaine_responsable as DR on DR.responsable = {$this->id}
        WHERE D.actif
        AND D.id > 0
        ORDER BY $order
        ");
    }




    public function getChargeInDomaine($domaineID){
        $statut    = $this->__get('statut');
        $situation = $this->__get('situation');
        $DPL       = $this->__get('domaine_personneList');
        $charge = $statut->obligation;
        if ($situation ){
            $charge -= $situation->reduction;
        }

// Est-ce qu'il ne faut pas ici prendre en compte aussi ce qui est
// dans le référentiel ?
// d'ailleurs est-ce qu'il ne faudrait pas que tout cela soit 
// dans la fonction getChargeRepartition ?

        $totalQuotite = 0;
        $quotite = 0;
        foreach($DPL as $DP){
            $totalQuotite += $DP->quotite;
            if ($DP->__domaine == $domaineID){
                $quotite = $DP->quotite;
            }
        }
        
        return $charge * $quotite/$totalQuotite;
    }


    public function getServiceInDomaine($domaineID){
        $app= \TDS\App::get();

        return  floatval($app::$db->getOne("
        SELECT DISTINCT
            sum(VBL.heures) as heures
        FROM voeu as V
        LEFT JOIN voeu_bilan_ligne as VBL on VBL.id = V.id
        LEFT JOIN domaine_enseignement as DS on DS.enseignement = V.enseignement
        WHERE V.actif
        AND V.id > 0
        AND DS.domaine = {$domaineID}
        AND V.personne = {$this->id}
        ")->heures);
    }

    public function getServiceInReferentiel(){
        $app= \TDS\App::get();

        return  floatval($app::$db->getOne("
        SELECT DISTINCT
            sum(VBL.heures) as heures
        FROM voeu as V
        LEFT JOIN voeu_bilan_ligne as VBL on VBL.id = V.id
        LEFT JOIN enseignement_structure as ES on V.enseignement = ES.id
        WHERE V.actif
        AND V.id > 0
        AND V.personne = {$this->id}
        AND ES.maquette = 'Référentiel'
        ")->heures);
    }

    public function getTotalVoeux(){
        $app= \TDS\App::get();

        return  floatval($app::$db->getOne("
        SELECT 
            heures as heures
        FROM voeu_personne_heures as VPH
        WHERE VPH.id = {$this->id}
        ")->heures);

    }

    public function getChargeRepartition(){
        $app= \TDS\App::get();
        $statut    = $this->__get('statut');
        $situation = $this->__get('situation');
        $VL = $this->__get('voeuList');
        $VBL       = $this->__get('voeu_personne_bilan'); 

        
        $bilan = $VBL->heures;
        $obligation = $statut->obligation;
        // var_dump($this->__get('personne_situation_reduction'));
        $reduction =  $this->__get('personne_situation_reduction')->reduction + ($situation->reduction??0); //

        $charge = $obligation - $reduction;

        $domaineIdList = [];
        foreach($this->__get('domaine_personneList') as $DP){
            $domaineIdList[] = $DP->domaine->id;
        }

        $serviceEnseignement = 0;
        $serviceReferentiel = $this->__get('personne_referentiel_heures')->heures; 
        $serviceInDomaine = 0;
        $serviceHorsDomaine = 0;
        $serviceParDomaine = []; // Il faut voir ici ce que cela veut dire... faut-il moduler suivant la quotité des enseignements ?
        foreach($VL as $V){
            $E = $V->enseignement;
            $ES = $E->enseignement_structure;
            $isReferentiel = $ES->maquette == "Référentiel";
            $heures = $V->voeu_bilan_ligne->heures;
            if ($isReferentiel){
                $serviceReferentiel += $heures; 
            } else {
                $serviceEnseignement += $heures;

                $inDomaine = false;
                foreach($E->domaine_enseignementList as $DE){
                    $domaine = $DE->domaine;
                    if (!isset($serviceParDomaine[$domaine->id])){
                        $serviceParDomaine[$domaine->id] = 0;
                    }
                    $serviceParDomaine[$domaine->id] += $heures;
                    $inDomaine = $inDomaine || in_array($domaine->id, $domaineIdList);
               }
               if ($inDomaine){
                    $serviceInDomaine += $heures;
               } else {
                $serviceHorsDomaine += $heures;
               }
            }
        }



        return [ 
            'obligation' => $obligation, 
            'reduction' => $reduction, 
            'bilan' => $bilan, 
            'charge' => $charge, 
            'serviceEnseignement' => $serviceEnseignement,
            'serviceReferentiel' => $serviceReferentiel,
            'serviceInDomaine' => $serviceInDomaine,
            'serviceHorsDomaine' => $serviceHorsDomaine,
            'serviceParDomaine' => $serviceParDomaine,
        ];

    }

    public function withDetailsRH(){
        $app = \TDS\App::get();
        $parent = parent::withDetailsRH();
        $isRHEAdmin = isset($app::$auth->roleList['RHEAdmin']);
        
        return $parent || $isRHEAdmin;
    }

    public function canEditDetailsRH(){
        $app = \TDS\App::get();
        $parent = parent::canEditDetailsRH();
        $isRHEAdmin = isset($app::$auth->roleList['RHEAdmin']);
        
        return $parent || $isRHEAdmin;
    }

    public function withStages(){
        $app = \TDS\App::get();
        $parent = parent::withStages();
        $concerned = $app::$auth->user->id == $this->id;
        $withStage = $app::$phaseList[$app::$phase]->withStages;
        return $parent || ($concerned && $withStage);
    }

    public function canEditStages(){
        $app = \TDS\App::get();
        $parent = parent::withStages();
        $concerned = $app::$auth->user->id == $this->id;
        $withEditStage = $app::$phaseList[$app::$phase]->withEditStages;
        return $parent || ($concerned && $withEditStage );
    }

    // indique si l'utilisateur désigné est responsable d'au moins un des domaines auquel la personne est rattachée
    // si $userId est null alors c'est l'utilisateur loggué qui est testé
    public  function isResponsableDomaine( $userId = null){
        $app = \TDS\App::get();
        if (is_null($userId)){
            $userId = $app::$auth->user->id;
        }
        $isResponsableDomaine =  false;
        foreach($this->__get("domaine_personneList") as $DL ){
            foreach($DL->domaine->domaine_responsableList as $R){
                if ($R->responsable->id == $userId) $isResponsableDomaine = true;
            }
        }
        return $isResponsableDomaine;
    }

    public function isRHE(){
        $app = \TDS\App::get();

        $isRHEAdmin = isset($app::$auth->roleList['RHEAdmin']);
        $isBureauRHE = isset($app::$auth->roleList['bureauRHE']);
        return $isRHEAdmin | $isBureauRHE;
    }

}        
        