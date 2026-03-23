<?php
namespace base\Model;

use \TDS\ManyToMany;

class responsable extends ManyToMany implements \Model\_responsable_interface_ {
    use \Model\_responsable_;

    const __LEFT__ = "etape";
    const __RIGHT__ = "personne";
}        
        