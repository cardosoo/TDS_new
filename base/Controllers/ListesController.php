<?php
namespace base\Controllers;

class ListesController extends \TDS\Controller {

    public static function foncRef($id){
        $app = \TDS\App::get();

        
        $foncRef = $app::NS("FoncRef")::load($id);
        $PL =  $app::NS("personne_foncRef")::loadWhere("actif and foncref={$id}", ['commentaire']);

        $app::$cmpl['withMarkdown'] = true;
        
        $app::$cmpl["withJQuery"]=true;
        $app::$cmpl["withDataTables"]=true;


        
        echo $app::$viewer->render('listes/FoncRef.html.twig', [ 'foncRef' => $foncRef, 'PL' => $PL]);

    }



}