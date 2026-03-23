<?php
namespace tssdv\Controllers;


class respDomaineController extends \TDS\Controller {

    public static function home(){
        $app = \TDS\App::get();

        echo $app::$viewer->render('respDomaine/index.html.twig');
    }
    

    public static function listePersonnes($id){
        $app = \TDS\App::get();

        $domaine = $app::NS('Domaine')::load($id);
    
        $app::$cmpl["withDataTables"]=true;
        $app::$cmpl['withJQuery'] = true;

        echo $app::$viewer->render('respDomaine/listePersonnes.html.twig', ['D' => $domaine]) ;
    }


    public static function listeEnseignements($id){
        $app = \TDS\App::get();
        
        $domaine = $app::NS('Domaine')::load($id);
    
        $app::$cmpl["withDataTables"]=true;
        $app::$cmpl['withJQuery'] = true;

        echo $app::$viewer->render('respDomaine/listeEnseignements.html.twig', ['D' => $domaine]) ;
    }


}
