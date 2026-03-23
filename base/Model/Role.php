<?php
namespace base\Model;

use \TDS\Table;

class Role extends Table implements \Model\_Role_interface_ {
    use \Model\_Role_;

    function getGenericWithLink(){
        $app = \TDS\App::get();

        $gen = parent::getGeneric();
        $rep="$gen 
        <ul>
        ";
        foreach($this->__get('actasList') as $actAs){
            $id = $actAs->personne->id;
            $gen = $actAs->personne->getGenericWithLink();
            $rep.="<li>
            <a href='/{$app::$appName}/personne/{$id}'>$gen</a>
            </li>";
        }
        $rep.="</ul>
        ";
        return $rep;
    }

}        
        