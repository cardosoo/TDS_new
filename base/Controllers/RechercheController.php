<?php
namespace base\Controllers;

use base\Model\Composante;
use base\Model\Cursus;
use TDS\Controller;
use TDS\Query;
use base\Model\Personne;
//use base\Model\Enseignement;
use base\Model\Maquette;
use TDS\Model\Model;

use Knp\Snappy\Pdf;

class RechercheController extends Controller{

    public static function convertForJS($list){
        $n = [];
        foreach($list as $key => $val){
            $n[] = (object)[ 'id' => $key, 'nom' => $val];
        }
        return $n;
    }

    protected static function buildWhat($what, $year){
         $app = \TDS\App::get();
    
        $what = isset($what)?htmlspecialchars(pg_escape_string($app::$db->conn, trim(urldecode($what)))):null;
        if (is_null($year)){
            $year = $app::$currentYear;
        }

        $struct = new \base\Struct($year);
        //$typeList = $struct->getUsefulTypeList([]);        
        //$niveauList = $struct->getUsefulNiveauList([]);
        $structureList = self::convertForJS($struct->getUsefulStructureList(['inDB' => true]));        
        //$periodeList = $struct->getUsefulPeriodeList([]);  
        $etapeList = $struct->getUsefulEtapeList([]);


        $f['searchValue'] = $what;
        $f['cursusList'] =  \Base\Struct::getCursusList();
        $f['semestreList'] = \Base\Struct::getSemestreList();
        $f['modaliteList'] = \Base\Struct::getModaliteList();
        
        $f['structureList'] = $structureList;
        $f['maquetteList'] = $etapeList; 
        

        $app::$cmpl['TITLE'] =  "Recherche d'un enseignement";
        $app::$cmpl['withJQuery'] = true;
        $app::$cmpl['withDataTables'] = true; 
        $app::$cmpl['withKnockout'] = true;   
        
        return $f;
    }

    public static function what(string|null $what = null, string|null $year = null ){
        $app = \TDS\App::get();

        $f = self::buildWhat($what, $year);
        echo $app::$viewer->render('recherche/enseignement/index.html.twig', ['what' => $what, 'f' => $f]);       
    }

    public static function search($what=null){
        $app = \TDS\App::get();
//var_dump($app::class);
        $what = isset($what)?htmlspecialchars(pg_escape_string($app::$db->conn, trim(urldecode($what)))):null;
        if ( empty($what) ) {
            echo $app::$viewer->render('recherche/generique/index.html.twig', ['what'=> $what, 'list'=> [] ]);
            exit();            
        } 
        // on commence par faire la recherche dans les données de la foire
        $appName = $app::$appName;
        include($app::$pathList['base']."/model.php");
        $list = [];
        $nbFound = 0;
        foreach(Model::getEntityList() as $entityName => $entityDesc){
            $fullEntityName ="{$appName}\\Model\\{$entityName}";

            $whatList = $fullEntityName::searchByModel($what);
            if (!is_null($whatList)){
                $list[$entityName]=$whatList;
                $nbFound += count($whatList);
            }
        }
        // puis on fait la recherche dans les données de structure
        $struct = new \base\Struct();
        $structList = $struct->search($what);
        echo $app::$viewer->render('recherche/generique/index.html.twig', ['what' => $what, 'nbFound' => $nbFound,'list'=> $list, 'structList' => $structList]);
    }

    public static function crudSearch($what=null){
        $app = \TDS\App::get();

        $what = isset($what)?htmlspecialchars(pg_escape_string($app::$db->conn, trim(urldecode($what)))):null;
        if ( empty($what) ) {
            echo $app::$viewer->render('CRUD/search.html.twig', ['what'=> $what, 'list'=> [] ]);
            exit();            
        } 

        $appName = $app::$appName;
        include($app::$pathList['base']."/model.php");
        $list = [];
        $nbFound = 0;
        foreach(Model::getEntityList() as $entityName => $entityDesc){
            $fullEntityName =$app::NS($entityName);
            
            $whatList = $fullEntityName::searchByModel($what);

            if (!is_null($whatList)){
                $list[$entityName]=$whatList;
                $nbFound += count($whatList);
            }
        }
        echo $app::$viewer->render('CRUD/search.html.twig', ['what' => $what, 'nbFound' => $nbFound,'list'=> $list]);
    }

    protected static function serie($arr){
        $tmp = json_encode($arr);
        return '('.substr($tmp, 1,-1).')';
    }

    public static function selectEtapeJSON() {
        $app = \TDS\App::get();
        $struct = new \base\Struct();

        $selectors = [
            'cursus'    => filter_input(INPUT_POST,'cursus'   , FILTER_VALIDATE_INT, FILTER_REQUIRE_ARRAY),
            'semestre'  => filter_input(INPUT_POST,'semestre' , FILTER_VALIDATE_INT, FILTER_REQUIRE_ARRAY),
            'structure' => filter_input(INPUT_POST,'structure', FILTER_VALIDATE_INT, FILTER_REQUIRE_ARRAY),
            'etape' => [],
            'modalite'  => filter_input(INPUT_POST,'modalite' , FILTER_VALIDATE_INT, FILTER_REQUIRE_ARRAY),

        ];
        $with = new \stdClass;
        $with->withInactif        = filter_input(INPUT_POST,'withInactif'      , FILTER_VALIDATE_BOOLEAN);
        $with->onlyInactif        = filter_input(INPUT_POST,'onlyInactif'      , FILTER_VALIDATE_BOOLEAN);     
        
        if ($with->withInactif){
            $selectors['actif'] = $with->onlyInactif ? false : null ;
        } else {
            $selectors['actif'] = true ;
        }
        
        $filter = $struct->buildFilterFromSelectors($selectors);
        //$struct->explain = true;
        $etapeList =  $struct->getUsefulEtapeList($filter);
        echo json_encode(self::convertForJS($etapeList));
    }

    public static function getFicheFoireHTML(){
        $app = \TDS\App::get();
        echo file_get_contents($app::$pathList['plus'].'/'.$app::$appName.'/file.html');
    }

    public static function getFicheFoirePDF(){
        $app = \TDS\App::get();
        $snappy = new Pdf('/usr/bin/wkhtmltopdf');
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="file.pdf"');
            echo $snappy->getOutput(
                'https://foire.cardoso.cloudns.cl/foire/getFicheFoireHTML' ,
                [
                    'orientation' => 'landscape', 
                    'zoom' => 3,
                ]
            ) ;
    }


    protected static function buildSelectors(){
        $selectors = [
            'cursus'    => filter_input(INPUT_POST,'cursus'   , FILTER_VALIDATE_INT, FILTER_REQUIRE_ARRAY),
            'semestre'  => filter_input(INPUT_POST,'semestre' , FILTER_VALIDATE_INT, FILTER_REQUIRE_ARRAY),
            'structure' => filter_input(INPUT_POST,'structure', FILTER_VALIDATE_INT, FILTER_REQUIRE_ARRAY),
            'etape'     => filter_input(INPUT_POST,'etape'    , FILTER_VALIDATE_INT, FILTER_REQUIRE_ARRAY),
            'modalite'  => filter_input(INPUT_POST,'modalite' , FILTER_VALIDATE_INT, FILTER_REQUIRE_ARRAY),
        ];
        return $selectors;
    }

    public static function rechercheEnseignement(){
        $app = \TDS\App::get();
        $with = new \stdClass;
        $selectors = static::buildSelectors();
        $searchValue        = filter_input(INPUT_POST,'searchValue'      , FILTER_UNSAFE_RAW);
        $modalite                 = filter_input(INPUT_POST,'modalite'         , FILTER_VALIDATE_INT, FILTER_REQUIRE_ARRAY);

        $with->withInactif        = filter_input(INPUT_POST,'withInactif'      , FILTER_VALIDATE_BOOLEAN);
        $with->onlyInactif        = filter_input(INPUT_POST,'onlyInactif'      , FILTER_VALIDATE_BOOLEAN);
        $with->withSousEffectif   = filter_input(INPUT_POST,'withSousEffectif' , FILTER_VALIDATE_BOOLEAN);
        $with->nonPrioritaire     = filter_input(INPUT_POST,'nonPrioritaire'   , FILTER_VALIDATE_BOOLEAN);
        $with->withStructure      = filter_input(INPUT_POST,'withStructure'    , FILTER_VALIDATE_BOOLEAN);
        $with->withEtape          = filter_input(INPUT_POST,'withEtape'        , FILTER_VALIDATE_BOOLEAN);
        $with->withEquipe         = filter_input(INPUT_POST,'withEquipe'       , FILTER_VALIDATE_BOOLEAN);
        $with->withCorrespondant  = filter_input(INPUT_POST,'withCorrespondant', FILTER_VALIDATE_BOOLEAN);
        $with->withDomaine        = filter_input(INPUT_POST,'withDomaine'     , FILTER_VALIDATE_BOOLEAN);

        $typeFiche                = filter_input(INPUT_POST,'typeFiche'        , FILTER_UNSAFE_RAW);
        
        if ($with->withInactif){
            $selectors['actif'] = $with->onlyInactif ? false : null ;
        } else {
            $selectors['actif'] = true ;
        }
        
        if (!isset($what)) $what=null;

        // Dans cette fonction de recherche il serait bien de chercher aussi dans les maquettes et en particulier sur les code UE/ECUE et intitulé
        // Il y a un truc à changer ici pour que cela fonctionne avec la nouvelle façon de faire la recherche....        
        $struct = new \base\Struct();
        $filter = $struct->buildFilterFromSelectors($selectors);
//var_dump($app::NS('Enseignement'));
        $enseignementList = $app::NS('Enseignement')::search($searchValue, $selectors , $modalite, $with);
    
        // Je ne comprends pas très bien pourquoi il y a ces lignes ici... 
        $app::$cmpl['what'] = $what;
        $app::$cmpl['withSousEffectif'] = $with->withSousEffectif;
        $app::$cmpl['withStructure'] = $with->withStructure;
        $app::$cmpl['withEtape'] = $with->withEtape;
        $app::$cmpl['withEquipe'] = $with->withEquipe;
        $app::$cmpl['withCorrespondant'] = $with->withCorrespondant;
                

        if ($typeFiche==='Fiches pour foire'){
            
            if (count($enseignementList) == 0){
                file_put_contents($app::$pathList['plus']."/{$app::$appName}/file.html", "Pas de résultat");
                exit();
            }
            
            $EList = [];
            $count = 0;
            foreach($enseignementList as $res){
                $E = $res['enseignement'];
                $ecue = $res['ecue'];
                $order = $ecue->getCursusName().$ecue->getPeriodeName().$ecue->getEtape()->getNom().$ecue->getCode().$count;
                $count++;
                $EList[$order] = $E;
            }
            ksort($EList);
            
            $html = $app::$viewer->render('recherche/enseignement/fichesFoire.html.twig', ['enseignementList' => $EList]);
            file_put_contents($app::$pathList['plus']."/{$app::$appName}/file.html", $html);
            echo $app::$pathList['plus']."/{$app::$appName}/file.html", $html;

            exit();
        }
        
        echo $app::$viewer->render('recherche/enseignement/rechercheEnseignement.html.twig', ['enseignementList' => $enseignementList]);
    }

}
