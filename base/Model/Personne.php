<?php
namespace base\Model;

use \TDS\Table;

class Personne extends Table implements \Model\_Personne_interface_ {
    use \Model\_Personne_;

    public function getName(){
        return $this->prenom.' '.$this->nom;
    }
    
    public static function loadFromUid($uid){
        return self::loadOneWhere("UID =  '{$uid}'");
    }

    public function getPhotoUrl(){
        $app = \TDS\App::get();
        return "/{$app::$appName}/photos/{$this->id}";
    }

    public function getCharge(){
        $statut = $this->__get('statut');
        if (is_null($statut)) return 0;
        $situation = $this->__get('situation');
        return $this->__get('personne_charge')->charge;
        //return $statut->obligation - (is_null($situation)? 0 : $situation->reduction);
    }

    public function getDirectLink(){
        $app = \TDS\App::get();
        
        $url = $app::$auth->buildDirectLink($this->id);
        return $url;
    }

    function canEdit(){
        $app = \TDS\App::get();
        if (! $app::$auth->inBase) return false;
        return  $app::$auth->user->id == $this->id;
    }

    function getGenericWithLink(){
        $app = \TDS\App::get();

        $gen = parent::getGeneric();
        return "<a href='/{$app::$appName}/personne/{$this->id}'>$gen</a>";
    }

    function getRoleList(){
        $roleList = [];
        foreach($this->__get('actasList') as $actAs){
            $roleList[$actAs->role->nom] = $actAs->role;
        }
        return $roleList;
    }

    function getRoleListArray(){
        return array_keys(self::getRoleList());
    }

    public function getEtapeResponsable($order = 'E.nom'){
        $app= \TDS\App::get();

        return  $app::$db->fetchAll("
        SELECT DISTINCT
            E.id,
            E.nom
        FROM etape as E
        LEFT JOIN  responsable as R on R.etape = E.id
        WHERE E.actif
        AND R.personne = {$this->id}
        AND E.id > 0
        ORDER BY $order
        ");
    }

    public static function saveStages($id){
        echo "Ok";
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

    public static function canEditDocuments(){
        $app = \TDS\App::get();
        return $app::$auth->isAdmin || $app::$auth->isSuperAdmin;
    }

    // withDetailsRH
    public function withDetailsRH(){
        $app = \TDS\App::get();
        return $app::$auth->isAdmin || $app::$auth->isSuperAdmin;
    }

    public function canEditDetailsRH(){
        $app = \TDS\App::get();
        return $app::$auth->isAdmin || $app::$auth->isSuperAdmin;
    }

    // withStages()
    public function withStages(){
        $app = \TDS\App::get();
        return $app::$auth->isAdmin || $app::$auth->isSuperAdmin;
    }

    public function canEditStages(){
        $app = \TDS\App::get();
        return $app::$auth->isAdmin || $app::$auth->isSuperAdmin;
    }

    // withOSE()
    public function withOSE(){
        return false;

        $app = \TDS\App::get();
        return $app::$auth->isAdmin || $app::$auth->isSuperAdmin;
    }

    public function canEditOSE(){
        return false;
    
        $app = \TDS\App::get();
        return $app::$auth->isAdmin || $app::$auth->isSuperAdmin;
    }

    public function canHistorique(){
        $app = \TDS\App::get();
        $isGestionnaire = isset($app::$auth->roleList['Gestionnaire']);
        $concerned = $app::$auth->user->id == $this->id;
        return $app::$auth->isAdmin || $app::$auth->isSuperAdmin || $concerned || $isGestionnaire;
    }
    
}        
        