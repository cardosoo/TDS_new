<?php
namespace tssdv\Model;

use \TDS\ManyToMany;
use \TDS\App;

class Voeu extends \base\Model\Voeu implements \Model\_Voeu_interface_ {
    use \Model\_Voeu_;

    const __LEFT__ = "personne";
    const __RIGHT__ = "enseignement";
}        
        