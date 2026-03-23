<?php
namespace tssdv\Model;

use \TDS\ManyToMany;
use \TDS\App;

class domaine_personne extends ManyToMany implements \Model\_domaine_personne_interface_ {
    use \Model\_domaine_personne_;

    const __LEFT__ = "domaine";
    const __RIGHT__ = "personne";
}        
        