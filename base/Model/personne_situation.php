<?php
namespace base\Model;

use \TDS\ManyToMany;
use \TDS\App;

class personne_situation extends ManyToMany implements \Model\_personne_situation_interface_ {
    use \Model\_personne_situation_;

    const __LEFT__ = "personne";
    const __RIGHT__ = "situation";
}        
        