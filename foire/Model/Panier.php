<?php
namespace foire\Model;

use \TDS\ManyToMany;
use \TDS\Query;

class Panier extends ManyToMany implements \Model\_Panier_interface_ {
    use \Model\_Panier_;

    const __LEFT__ = "personne";
    const __RIGHT__ = "enseignement";

    public function getVoeu(){
        $app = \TDS\App::get();

        $q = new Query($app::NS('Voeu'), 'V');
        return $q->addSQL("
            WHERE {$q->V_enseignement} = {$this->__enseignement}
            AND   {$q->V_personne} = {$this->__personne}    
        ")->getOne();
    }


    public function panierBilan(){
        $V = $this->getVoeu();
        
        if (is_null($V)) {
            return null;
        }
        return $V->voeu_bilan_ligne->heures;
    }

    public function getAnciennete(){
        // var_dump(['personne' => $this->__get("personne")]);

        return $this->__get("enseignement")->computeAnciennete($this->__get("personne"));
    }

}        
        