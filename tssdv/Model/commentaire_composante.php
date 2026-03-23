<?php
namespace tssdv\Model;

use \TDS\ManyToMany;
use \TDS\App;

class commentaire_composante extends \base\Model\commentaire_composante implements \Model\_commentaire_composante_interface_ {
    use \Model\_commentaire_composante_;

    const __LEFT__ = "composante";
    const __RIGHT__ = "auteur";
}        
        