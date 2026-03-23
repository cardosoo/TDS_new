<?php
namespace foire\Model;

use \TDS\ManyToMany;

class commentaire_enseignement extends ManyToMany implements \Model\_commentaire_enseignement_interface_ {
    use \Model\_commentaire_enseignement_;

    const __LEFT__ = "enseignement";
    const __RIGHT__ = "auteur";

    public function CRUD_beforeCreate(){
        $app = \TDS\App::get();

        $this->__set('auteur', $app::$auth->user->id);
    }


}        
        