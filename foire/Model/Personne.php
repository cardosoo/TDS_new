<?php
namespace foire\Model;


class Personne extends \base\Model\Personne implements \Model\_Personne_interface_ {
    use \Model\_Personne_;

    public function withCommentaires(){
        $app = \TDS\App::get();
        $parent = parent::withCommentaires();
        $isGestionnaire = isset($app::$auth->roleList['Gestionnaire']);
        $concerned = $app::$auth->user->id == $this->id;
        
        return $parent || $concerned || $isGestionnaire;
    }

    public function canEditCommentaires(){
        $app = \TDS\App::get();
        $parent = parent::canEditCommentaires();
        $isGestionnaire = isset($app::$auth->roleList['Gestionnaire']);
        
        return $parent || $isGestionnaire;
    }

    public function withDocuments(){
        $app = \TDS\App::get();
        $parent = parent::withDocuments();
        $concerned = $app::$auth->user->id == $this->id;
        $isGestionnaire = isset($app::$auth->roleList['Gestionnaire']);
        
        return $parent || $concerned || $isGestionnaire;
    }

    public static function canEditDocuments(){
        $app = \TDS\App::get();
        $parent = parent::canEditDocuments();
        $isGestionnaire = isset($app::$auth->roleList['Gestionnaire']);
        
        return $parent || $isGestionnaire;
    }

    public function withStages(){
        $app = \TDS\App::get();
        $parent = parent::withStages();
        $concerned = $app::$auth->user->id == $this->id;
        $statutaire = in_array($app::$auth->user->__get('statut')->nom, ['MCF', 'PROF']);
        $withStage = $app::$phaseList[$app::$phase]->withStages;
        return $parent || ($concerned && $statutaire && $withStage);
    }

    public function canEditStages(){
        $app = \TDS\App::get();
        $parent = parent::canEditstages();
        $concerned = $app::$auth->user->id == $this->id;
        $statutaire = in_array($app::$auth->user->__get('statut')->nom, ['MCF', 'PROF']);
        $withEditStage = $app::$phaseList[$app::$phase]->withEditStages;
        return $parent || ($concerned && $statutaire && $withEditStage);
    }

    public function withOSE(){
        return false;

        $app = \TDS\App::get();
        $parent = parent::withOSE();
        $isGestionnaire = isset($app::$auth->roleList['Gestionnaire']);
        return $parent || $isGestionnaire;
    }

    public function canEditOSE(){
        return false;
        
        $app = \TDS\App::get();
        $parent = parent::canEditOSE();
        $isGestionnaire = isset($app::$auth->roleList['Gestionnaire']);
        return $parent || $isGestionnaire;
    }

    // withDetailsRH
    public function withDetailsRH(){
        $app = \TDS\App::get();
        $parent = parent::withDetailsRH();
        $isGestionnaire = isset($app::$auth->roleList['Gestionnaire']);
        return $parent || $isGestionnaire;
    }

    public function canEditDetailsRH(){
        $app = \TDS\App::get();
        $parent = parent::canEditDetailsRH();
        $isGestionnaire = isset($app::$auth->roleList['Gestionnaire']);
        return $parent || $isGestionnaire;
    }

    public function getReport(){
        $app = \TDS\App::get();

        $report = 0;
        $situationList = $this->__get('personne_situationList');

        foreach($situationList as $SL){
            if (in_array($SL->situation->nom, [ 'Report négatif', 'Report positif'] )){
                $report += $SL->reduction;
            }
        }

        return $report;
    }
    
}        
        