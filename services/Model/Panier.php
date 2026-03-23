<?php
namespace services\Model;

use \TDS\ManyToMany;
use \TDS\App;

class Panier extends \foire\Model\Panier implements \Model\_Panier_interface_ {
    use \Model\_Panier_;

    const __LEFT__ = "personne";
    const __RIGHT__ = "enseignement";
}        
        