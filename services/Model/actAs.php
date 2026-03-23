<?php
namespace services\Model;

use \TDS\ManyToMany;
use \TDS\App;

class actAs extends \base\Model\actAs implements \Model\_actAs_interface_ {
    use \Model\_actAs_;

    const __LEFT__ = "personne";
    const __RIGHT__ = "role";
}        
        