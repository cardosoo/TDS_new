<?php
namespace tssdv\Model;

use \TDS\ManyToMany;
use \TDS\App;

class dommaine_personne extends ManyToMany implements \Model\_dommaine_personne_interface_ {
    use \Model\_dommaine_personne_;

    const __LEFT__ = "domaine";
    const __RIGHT__ = "personne";
}        
        