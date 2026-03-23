<?php
namespace base\Model;

use \TDS\ManyToMany;
use \TDS\App;

class commentaire_composante extends ManyToMany implements \Model\_commentaire_composante_interface_ {
    use \Model\_commentaire_composante_;

    const __LEFT__ = "composante";
    const __RIGHT__ = "auteur";
}        
        