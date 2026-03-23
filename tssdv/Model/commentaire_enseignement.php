<?php
namespace tssdv\Model;

use \TDS\ManyToMany;
use \TDS\App;

class commentaire_enseignement extends ManyToMany implements \Model\_commentaire_enseignement_interface_ {
    use \Model\_commentaire_enseignement_;

    const __LEFT__ = "enseignement";
    const __RIGHT__ = "auteur";
}        
        