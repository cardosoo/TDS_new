<?php
namespace tssdv\Model;

use \TDS\ManyToMany;
use \TDS\App;

class dommaine_enseignement extends ManyToMany implements \Model\_dommaine_enseignement_interface_ {
    use \Model\_dommaine_enseignement_;

    const __LEFT__ = "domaine";
    const __RIGHT__ = "enseignement";
}        
        