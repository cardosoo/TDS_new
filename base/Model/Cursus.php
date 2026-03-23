<?php
namespace base\Model;

use \TDS\Table;

class Cursus extends Table implements \Model\_Cursus_interface_ {
    use \Model\_Cursus_;

    function getGenericWithLink(){
        $gen = parent::getGeneric();
        $rep="$gen 
        <ul>
        ";
        foreach($this->__get('etapeList') as $etape){
            $gen = $etape->getGenericWithLink();
            $rep.="<li>$gen</li>";
        }
        $rep.="</ul>
        ";
        return $rep;
    }

}        
        