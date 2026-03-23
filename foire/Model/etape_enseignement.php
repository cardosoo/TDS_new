<?php
namespace foire\Model;

use \TDS\ManyToMany;
use \TDS\App;

class etape_enseignement extends \base\Model\etape_enseignement implements \Model\_etape_enseignement_interface_ {
    use \Model\_etape_enseignement_;

    const __LEFT__ = "etape_ose";
    const __RIGHT__ = "enseignement";
}        
        