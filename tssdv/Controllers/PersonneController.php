<?php
namespace tssdv\Controllers;

class PersonneController extends \base\Controllers\PersonneController {


    public static function fiche($id){
        $app = \TDS\App::get();
        $options = self::getOptions($id);

        $appName = $app::$appName;
        $app::$cmpl["withJQuery"]=true;
        $app::$cmpl['withDataTables'] = true; 
        $app::$cmpl['withKnockout'] = true;

        $app::$toCRUD="/{$appName}/CRUD/Personne/{$id}";

        echo $app::$viewer->render("personne/fiche.html.twig", $options);
    }



}
