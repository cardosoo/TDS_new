<?php
namespace tssdv\Controllers;

class RechercheController extends \base\Controllers\RechercheController{



    public static function what(string|null $what = null, string|null $year = null ){
        $app = \TDS\App::get();


        $f = parent::buildWhat($what, $year);
        $q = new \TDS\Query($app::NS('Domaine'), "D");
        $f['domaineList'] = $q->addSQl("WHERE {$q->D_id}>0 ORDER BY {$q->D_nom}")->exec(false); 

        $app::$cmpl['TITLE'] =  "Recherche d'un enseignement";
        $app::$cmpl['withJQuery'] = true;
        $app::$cmpl['withDataTables'] = true; 
        $app::$cmpl['withKnockout'] = true;
        
        echo $app::$viewer->render('recherche/enseignement/index.html.twig', ['what' => $what, 'f' => $f]);


        /*        $app = \TDS\App::get();
    
        $what = isset($what)?htmlspecialchars(pg_escape_string($app::$db->conn, trim(urldecode($what)))):null;

        $f['searchValue'] = $what;
        $q = new \TDS\Query($app::NS('Cursus'), "C");
        $f['cursusList'] = $q->addSQl("WHERE {$q->C_id}>0 ")->exec(false);
        $f['semestreList'] = [
            (object)['id'=> 1, 'nom'=> '1er Semestre'],
            (object)['id'=> 2, 'nom'=> '2ème Semestre'],
            (object)['id'=> 3, 'nom'=> 'Annuel'],
            ];
        
        $f['modaliteList'] = [
        //    (object)['id'=> 0, 'name'=> 'Tous'],
            (object)['id'=> 1, 'nom'=> 'CM'],
            (object)['id'=> 2, 'nom'=> 'CTD'],
            (object)['id'=> 3, 'nom'=> 'TD'],
            (object)['id'=> 4, 'nom'=> 'TP'],
            (object)['id'=> 5, 'nom'=> 'Bonus'],
            ];
        
        $q = new \TDS\Query($app::NS('Composante'), "C");
        $f['composanteList'] = $q->addSQl("WHERE {$q->C_id}>0 ORDER BY {$q->C_ordre}")->exec(false);
        $q = new \TDS\Query($app::NS('Maquette'), "M");
        $f['maquetteList'] = $q->addSQl("WHERE {$q->M_id}>0 ORDER BY {$q->M_ordre}")->exec(false); 
        $q = new \TDS\Query($app::NS('Domaine'), "D");
        $f['domaineList'] = $q->addSQl("WHERE {$q->D_id}>0 ORDER BY {$q->D_nom}")->exec(false); 
        

        $app::$cmpl['TITLE'] =  "Recherche d'un enseignement";
        $app::$cmpl['withJQuery'] = true;
        $app::$cmpl['withDataTables'] = true; 
        $app::$cmpl['withKnockout'] = true;
        
        echo $app::$viewer->render('recherche/enseignement/index.html.twig', ['what' => $what, 'f' => $f]);
*/        
    }

    public static function rechercheEnseignement(){
//var_dump("rechercheEnseignement tssdv");
        parent::rechercheEnseignement();
    }


/*
    public static function rechercheEnseignement(){
        $app = \TDS\App::get();

        $with = new \stdClass;

        $searchValue        = filter_input(INPUT_POST,'searchValue'      , FILTER_UNSAFE_RAW);
        $cursus             = filter_input(INPUT_POST,'cursus'           , FILTER_VALIDATE_INT, FILTER_REQUIRE_ARRAY);
        $semestre           = filter_input(INPUT_POST,'semestre'         , FILTER_VALIDATE_INT, FILTER_REQUIRE_ARRAY);
        $composante         = filter_input(INPUT_POST,'composante'        , FILTER_VALIDATE_INT, FILTER_REQUIRE_ARRAY);
        $maquette           = filter_input(INPUT_POST,'maquette'         , FILTER_VALIDATE_INT, FILTER_REQUIRE_ARRAY);
        $domaine            = filter_input(INPUT_POST,'domaine'          , FILTER_VALIDATE_INT, FILTER_REQUIRE_ARRAY);
        $modalite           = filter_input(INPUT_POST,'modalite'         , FILTER_VALIDATE_INT, FILTER_REQUIRE_ARRAY);
        $with->withInactif        = filter_input(INPUT_POST,'withInactif'      , FILTER_VALIDATE_BOOLEAN);
        $with->onlyInactif        = filter_input(INPUT_POST,'onlyInactif'      , FILTER_VALIDATE_BOOLEAN);
        $with->withSousEffectif   = filter_input(INPUT_POST,'withSousEffectif' , FILTER_VALIDATE_BOOLEAN);
        $with->nonPrioritaire     = filter_input(INPUT_POST,'nonPrioritaire'   , FILTER_VALIDATE_BOOLEAN);
        $with->withComposante    = filter_input(INPUT_POST,'withComposante'    , FILTER_VALIDATE_BOOLEAN);
        $with->withMaquette       = filter_input(INPUT_POST,'withMaquette'     , FILTER_VALIDATE_BOOLEAN);
        $with->withEquipe         = filter_input(INPUT_POST,'withEquipe'       , FILTER_VALIDATE_BOOLEAN);
        $with->withCorrespondant  = filter_input(INPUT_POST,'withCorrespondant', FILTER_VALIDATE_BOOLEAN);
        $with->withDomaine        = filter_input(INPUT_POST,'withDomaine'     , FILTER_VALIDATE_BOOLEAN);
        $typeFiche          = filter_input(INPUT_POST,'typeFiche'        , FILTER_UNSAFE_RAW);
        
        if ($typeFiche==='hTD enseignants'){
            $with->withEquipe=true;
        }
        
        if (!isset($what)) $what=null;
                
        // Dans cette fonction de recherche il serait bien de chercher aussi dans les maquettes et en particulier sur les code UE/ECUE et intitulé
        $enseignementList = $app::NS('Enseignement')::search($searchValue,[
            'cursus'      => $cursus, 
            'periode'     => $semestre, 
            'composante'  => $composante, 
            'maquette'    => $maquette,
            'domaine'     => $domaine,
            ], $modalite, $with);
        
         
        $app::$cmpl['what'] = $what;
        $app::$cmpl['withSousEffectif'] = $with->withSousEffectif;
        $app::$cmpl['withComposante'] = $with->withComposante;
        $app::$cmpl['withDomaine'] = $with->withDomaine;
        $app::$cmpl['withMaquette'] = $with->withMaquette;
        $app::$cmpl['withEquipe'] = $with->withEquipe;
        $app::$cmpl['withCorrespondant'] = $with->withCorrespondant;
        $app::$cmpl['withDomaine'] = $with->withDomaine;

        echo $app::$viewer->render('recherche/enseignement/rechercheEnseignement.html.twig', ['enseignementList' => $enseignementList]);
    }
*/





}
