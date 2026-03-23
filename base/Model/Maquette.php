<?php
namespace base\Model;

use \TDS\Table;

class Maquette extends Table implements \Model\_Maquette_interface_ {
    use \Model\_Maquette_;


    function getGenericWithLink(){
        $app = \TDS\App::get();
        
        $gen = parent::getGeneric();
        return "<a href='/{$app::$appName}/maquette/{$this->id}'>$gen</a>";
    }

    function deleteWithCascade(){
        foreach($this->__get("diplomeist") as $diplome){
            $diplome->deleteWithCascade();
        }
        $this->delete();
    }

    // wihtCommentaires
    public function withCommentaires(){
        $app = \TDS\App::get();
        return $app::$auth->isAdmin || $app::$auth->isSuperAdmin;
    }
    public function canEditCommentaires(){
        $app = \TDS\App::get();
        return $app::$auth->isAdmin || $app::$auth->isSuperAdmin;
    }

    // withDocuments
    public function withDocuments(){
        $app = \TDS\App::get();
        return $app::$auth->isAdmin || $app::$auth->isSuperAdmin;
    }

    public function canEditDocuments(){
        $app = \TDS\App::get();
        return $app::$auth->isAdmin || $app::$auth->isSuperAdmin;
    }


}        
        