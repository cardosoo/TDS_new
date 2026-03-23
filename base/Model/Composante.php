<?php
namespace base\Model;

use \TDS\Table;

class Composante extends Table implements \Model\_Composante_interface_ {
    use \Model\_Composante_;

    function getGenericWithLink(){
        $app = \TDS\App::get();

        $gen = parent::getGeneric();
        $rep="<a href='/{$app::$appName}/composante/{$this->id}'>{$gen}</a> 
        <ul>
        ";
        foreach($this->__get('maquetteList') as $maquette){
            $id = $maquette->id;
            $gen = $maquette->getGenericWithLink();
            $appName = $app::$appName;
            $rep.="<li>
            <a href='/{$appName}/maquette/{$id}'>$gen</a>
            </li>";
        }
        $rep.="</ul>
        ";
        return $rep;
    }

    public function withCommentaires(){
        $app = \TDS\App::get();
        return $app::$auth->isAdmin || $app::$auth->isSuperAdmin;
    }

    public function withDocuments(){
        $app = \TDS\App::get();
        return $app::$auth->isAdmin || $app::$auth->isSuperAdmin;
    }

    public function canEditDocuments(){
        $app = \TDS\App::get();
        return $app::$auth->isAdmin || $app::$auth->isSuperAdmin;
    }

    public function canEditCommentaires(){
        $app = \TDS\App::get();
        return $app::$auth->isAdmin || $app::$auth->isSuperAdmin;
    }


}        
        