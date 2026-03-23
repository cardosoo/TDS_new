<?php
namespace base\Model;

use \TDS\ManyToMany;
use \TDS\App;

class commentaire_personne extends ManyToMany implements \Model\_commentaire_personne_interface_ {
    use \Model\_commentaire_personne_;

    const __LEFT__ = "personne";
    const __RIGHT__ = "auteur";
}        
        