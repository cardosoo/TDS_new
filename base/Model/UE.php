<?php
namespace base\Model;

use \TDS\Table;

class UE extends Table implements \Model\_UE_interface_ {
    use \Model\_UE_;

    public function nbetu(){
        return $this->peretu/100*$this->__get("semestre")->nbetu();
    }

    function getGenericWithLink(){
        $gen = parent::getGeneric();
        $gen2 = $this->__get('semestre')->etape->diplome->maquette->getGenericWithLink();
        return "{$gen} <span class='searchMark'>maquette</span> {$gen2}";
    }

    function deleteWithCascade(){
        foreach($this->__get("ecueList") as $ecue){
            $ecue->deleteWithCascade();
        }
        $this->delete();
    }

}        
        