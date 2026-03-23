<?php
namespace base\Model;

use \TDS\Table;

class Semestre extends Table implements \Model\_Semestre_interface_ {
    use \Model\_Semestre_;

    public function nbetu(){
        return $this->peretu/100*$this->__get("etape")->nbetu;
    }

    function getGenericWithLink(){
        $gen = parent::getGeneric();
        $gen2 = $this->__get('etape')->diplome->maquette->getGenericWithLink();
        return "{$gen} <span class='searchMark'>maquette</span> {$gen2}";
    }

    function deleteWithCascade(){
        foreach($this->__get("ueList") as $ue){
            $ue->deleteWithCascade();
        }
        $this->delete();
    }

}        
        