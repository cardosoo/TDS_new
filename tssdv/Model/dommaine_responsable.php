<?php
namespace tssdv\Model;

use \TDS\ManyToMany;
use \TDS\App;

class dommaine_responsable extends ManyToMany implements \Model\_dommaine_responsable_interface_ {
    use \Model\_dommaine_responsable_;

    const __LEFT__ = "domaine";
    const __RIGHT__ = "responsable";
}        
        