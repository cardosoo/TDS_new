<?php
namespace foire\Model;


class Composante extends \base\Model\Composante implements \Model\_Composante_interface_ {
    use \Model\_Composante_;

    public function withCommentaires(){
        $app = \TDS\App::get();
        $parent = parent::withCommentaires();
        $isGestionnaire = isset($app::$auth->roleList['Gestionnaire']);
        $isCENS = isset($app::$auth->roleList['CENS']);
        
        return $parent || $isGestionnaire || $isCENS;
    }

    public function canEditCommentaires(){
        $app = \TDS\App::get();
        $parent = parent::canEditCommentaires();
        $isGestionnaire = isset($app::$auth->roleList['Gestionnaire']);        
        $isCENS = isset($app::$auth->roleList['CENS']);
        
        return $parent || $isGestionnaire || $isCENS;
    }

    public function withDocuments(){
        $app = \TDS\App::get();
        $parent = parent::withDocuments();
        $isGestionnaire = isset($app::$auth->roleList['Gestionnaire']);
        $isCENS = isset($app::$auth->roleList['CENS']);
        
        return $parent || $isGestionnaire || $isCENS;
    }

    public function canEditDocuments(){
        $app = \TDS\App::get();
        $parent = parent::canEditDocuments();
        $isGestionnaire = isset($app::$auth->roleList['Gestionnaire']);
        $isCENS = isset($app::$auth->roleList['CENS']);
        
        return $parent || $isGestionnaire || $isCENS;
    }

}        
        