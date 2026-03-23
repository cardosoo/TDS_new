<?php
namespace base\Model;

use \TDS\ManyToMany;
use \TDS\App;

class etape_personne extends ManyToMany implements \Model\_etape_personne_interface_ {
    use \Model\_etape_personne_;

    const __LEFT__ = "etapeose";
    const __RIGHT__ = "personne";
}        
        