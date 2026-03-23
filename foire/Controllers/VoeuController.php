<?php
namespace foire\Controllers;

use foire\Model\Voeu;
use stdClass;

class VoeuController extends \base\Controllers\TestController {

    public static function CRUD_calculAnciennete($personneId, $enseignementId){
        $app = \TDS\App::get();
        $p = $app::NS('Personne')::load($personneId);
        $e = $app::NS('Enseignement')::load($enseignementId);

        echo $e->computeAnciennete($p);
    }
}
