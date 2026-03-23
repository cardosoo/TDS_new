<?php
namespace base\Model;

use \TDS\Table;

class Diplome extends Table implements \Model\_Diplome_interface_ {
    use \Model\_Diplome_;

    function getGenericWithLink(){
        $gen = parent::getGeneric();
        $gen2 = $this->__get('maquette')->getGenericWithLink();
        return "{$gen} <span class='searchMark'>maquette</span> {$gen2}";
    }

    function deleteWithCascade(){
        foreach($this->__get("etapeList") as $etape){
            $etape->deleteWithCascade();
        }
        $this->delete();
    }


}        
        