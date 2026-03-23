<?php
namespace foire\Model;


class Voeu extends \base\Model\Voeu implements \Model\_Voeu_interface_ {
    use \Model\_Voeu_;

    const __LEFT__ = "personne";
    const __RIGHT__ = "enseignement";


    public function CRUD_beforeUpdate(){
        global $_PATCH;        
        $app = \TDS\App::get();

        if (isset($_PATCH['field']['anciennete'])){
            // var_dump($_PATCH['field']['anciennete']);
            if ( (empty($_PATCH['field']['anciennete']) && ('0' != $_PATCH['field']['anciennete']))|| (-1 == $_PATCH['field']['anciennete'])){
                // var_dump(empty($_PATCH['field']['anciennete']), (-1 == $_PATCH['field']['anciennete']));
                if (0 == $this->__enseignement){
                    $e = $app::NS('Enseignement')::load($_PATCH['field']['enseignement']);
                } else {
                    $e = $this->__get('enseignement');
                }
                if (0 == $this->__personne){
                    $p = $app::NS('Personne')::load($_PATCH['field']['personne']);
                } else {
                    $p = $this->__get('personne');
                }
                //echo "Pourquoi je suis là ?";
                $_PATCH['field']['anciennete'] = $e->computeAnciennete($p);
            }
        }
    }

    public function CRUD_beforeCreate(){
        $this->anciennete=-1;
    }

}
        