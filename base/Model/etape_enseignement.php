<?php
namespace base\Model;

use \TDS\ManyToMany;
use \TDS\App;

class etape_enseignement extends ManyToMany implements \Model\_etape_enseignement_interface_ {
    use \Model\_etape_enseignement_;

    const __LEFT__ = "etapeose";
    const __RIGHT__ = "enseignement";
}        
        