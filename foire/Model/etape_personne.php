<?php
namespace foire\Model;

use \TDS\ManyToMany;
use \TDS\App;

class etape_personne extends \base\Model\etape_personne implements \Model\_etape_personne_interface_ {
    use \Model\_etape_personne_;

    const __LEFT__ = "etape_ose";
    const __RIGHT__ = "personne";
}        
        