<?php
namespace foire\Model;


class responsable extends \base\Model\responsable implements \Model\_responsable_interface_ {
    use \Model\_responsable_;

    const __LEFT__ = "etape";
    const __RIGHT__ = "personne";
}        
        