<?php
namespace base\Model;

use \TDS\Table;

class Statut extends Table implements \Model\_Statut_interface_ {
    use \Model\_Statut_;

    function getGenericWithLink(){
        $gen = parent::getGeneric();
        $rep="$gen 
        <ul>
        ";
        foreach($this->__get('personneList') as $personne){
            $gen = $personne->getGenericWithLink();
            $rep.="<li>$gen</li>";
        }
        $rep.="</ul>
        ";
        return $rep;
    }


}        
        