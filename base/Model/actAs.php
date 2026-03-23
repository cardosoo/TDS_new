<?php
namespace base\Model;

use \TDS\ManyToMany;

class actAs extends ManyToMany implements \Model\_actAs_interface_ {
    use \Model\_actAs_;

    const __LEFT__ = "personne";
    const __RIGHT__ = "role";
}


