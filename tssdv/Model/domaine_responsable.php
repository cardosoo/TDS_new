<?php
namespace tssdv\Model;

use \TDS\ManyToMany;
use \TDS\App;

class domaine_responsable extends ManyToMany implements \Model\_domaine_responsable_interface_ {
    use \Model\_domaine_responsable_;

    const __LEFT__ = "domaine";
    const __RIGHT__ = "responsable";
}        
        