<?php
namespace tssdv\Model;

use \TDS\ManyToMany;
use \TDS\App;

class domaine_enseignement extends ManyToMany implements \Model\_domaine_enseignement_interface_ {
    use \Model\_domaine_enseignement_;

    const __LEFT__ = "domaine";
    const __RIGHT__ = "enseignement";
}        
        