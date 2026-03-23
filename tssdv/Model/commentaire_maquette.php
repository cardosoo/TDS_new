<?php
namespace tssdv\Model;

use \TDS\ManyToMany;
use \TDS\App;

class commentaire_maquette extends \base\Model\commentaire_maquette implements \Model\_commentaire_maquette_interface_ {
    use \Model\_commentaire_maquette_;

    const __LEFT__ = "maquette";
    const __RIGHT__ = "auteur";
}        
        