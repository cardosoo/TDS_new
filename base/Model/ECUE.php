<?php
namespace base\Model;

use \TDS\Table;
use \TDS\App;

class ECUE extends Table implements \Model\_ECUE_interface_ {
    use \Model\_ECUE_;

    public function nbetu(){
        return $this->peretu/100*$this->__get("ue")->nbetu();
    }

    function getGenericWithLink(){
        $gen = parent::getGeneric();
        $ue = $this->__get('ue'); 
        $m = is_null($ue)?null:$ue->semestre->etape->diplome->maquette;
        $gen2 = is_null($m)?"pas de Maquette ?":$m->getGenericWithLink();
        return "{$gen} <span class='searchMark'>maquette</span> {$gen2}";
    }

    public function deleteWithCascade(){
        $this->delete();
    }

    /**
     * Permet de renvoyer le cout en hETD pour une ECUE
     * calculé au prorata des étudiants qui apparaissent
     * dans la maquette rapporté au nombre total d'étudiants
     * qui apparaissent dans les différentes maquettes 
     * dans lequel l'enseignement est inscrit
     * 
     * ces nombres d'étudiants doivent être maintenu correctement
     * pour que le coût est un sens.
     */
    public function getCout(){
        if ($this->__enseignement > 0){
            $E = $this->__get("enseignement");
            if (!$E->actif){
                return 0;
            }
            $besoins = $E->enseignement_besoins->besoins;
            $nbEtuEcue = $this->nbetu();
            $nbEtuE  = $E->getNbEtu();
            $cout = $nbEtuE > 0 ? $besoins*$nbEtuEcue/$nbEtuE:$besoins;
            return $cout;
        } else {
            return 0;
        }
    }

    public function getHEtu(){
        if ($this->__enseignement <= 0) return [0, 0, 0, 0];
        $E = $this->__get("enseignement");
        if (!$E->actif) return [0, 0, 0, 0];

        return [$E->s_cm*$E->d_cm, $E->s_ctd*$E->d_ctd, $E->s_td*$E->d_td, $E->s_tp*$E->d_tp];
    }


}