<?php
namespace base\Model;

use \TDS\Table;

class Labo extends Table implements \Model\_Labo_interface_ {
    use \Model\_Labo_;


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
        