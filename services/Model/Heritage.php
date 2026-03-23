<?php
namespace services\Model;

use \TDS\ManyToMany;
use \TDS\App;

class Heritage extends \foire\Model\Heritage implements \Model\_Heritage_interface_ {
    use \Model\_Heritage_;

    const __LEFT__ = "parent";
    const __RIGHT__ = "enfant";
}        
        