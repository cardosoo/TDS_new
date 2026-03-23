<?php
namespace foire\Model;

use \TDS\ManyToMany;

class Heritage extends ManyToMany implements \Model\_Heritage_interface_ {
    use \Model\_Heritage_;

    const __LEFT__ = "parent";
    const __RIGHT__ = "enfant";
}        
        