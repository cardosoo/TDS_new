<?php
namespace base\Model;

use \TDS\ManyToMany;
use \TDS\App;

class personne_foncRef extends ManyToMany implements \Model\_personne_foncRef_interface_ {
    use \Model\_personne_foncRef_;

    const __LEFT__ = "personne";
    const __RIGHT__ = "foncref";
}        
        